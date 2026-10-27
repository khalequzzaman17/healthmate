<?php

namespace App\Http\Controllers;

use App\Enums\CallOutcome;
use App\Enums\FollowUpStatus;
use App\Models\FollowUpPlan;
use App\Models\HealthReport;
use App\Models\Patient;
use App\Models\PatientAssignment;
use App\Models\PatientCall;
use App\Services\NurseWorkflowService;
use App\Services\TenantContext;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NurseWorkspaceController extends Controller
{
    public function __construct(
        protected NurseWorkflowService $nurseWorkflowService
    ) {}

    public function index(Request $request): View
    {
        $nurse = Auth::user();
        $clinic = TenantContext::getClinic();
        $today = Carbon::today();

        // Follow ups queue
        $dueToday = FollowUpPlan::where('assigned_nurse_id', $nurse->id)
            ->whereDate('due_date', '<=', $today)
            ->where('status', FollowUpStatus::Scheduled->value)
            ->with(['patient'])
            ->orderBy('priority', 'desc')
            ->get();

        $overdue = FollowUpPlan::where('assigned_nurse_id', $nurse->id)
            ->whereDate('due_date', '<', $today)
            ->where('status', FollowUpStatus::Scheduled->value)
            ->with(['patient'])
            ->get();

        $assignedPatients = PatientAssignment::where('nurse_id', $nurse->id)
            ->where('status', 'active')
            ->with('patient')
            ->get();

        $recentCalls = PatientCall::where('nurse_id', $nurse->id)
            ->with('patient')
            ->latest()
            ->take(10)
            ->get();

        $submittedReports = HealthReport::where('nurse_id', $nurse->id)
            ->with(['patient', 'reviewerDoctor'])
            ->latest()
            ->take(10)
            ->get();

        return view('nurse.index', compact(
            'dueToday',
            'overdue',
            'assignedPatients',
            'recentCalls',
            'submittedReports'
        ));
    }

    public function createCall(Request $request, Patient $patient): View
    {
        $followUpPlanId = $request->input('follow_up_plan_id');
        $followUpPlan = $followUpPlanId ? FollowUpPlan::find($followUpPlanId) : null;

        return view('nurse.record_call', compact('patient', 'followUpPlan'));
    }

    public function storeCall(Request $request, Patient $patient): RedirectResponse
    {
        $clinic = TenantContext::getClinic();
        $nurse = Auth::user();

        $validated = $request->validate([
            'outcome' => ['required', 'string'],
            'duration_seconds' => ['required', 'integer', 'min:0'],
            'reached' => ['required', 'boolean'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'next_follow_up_date' => ['nullable', 'date'],
            'follow_up_plan_id' => ['nullable', 'exists:follow_up_plans,id'],

            // Structured observations (optional)
            'record_vitals' => ['nullable', 'boolean'],
            'systolic_bp' => ['nullable', 'integer', 'min:50', 'max:300'],
            'diastolic_bp' => ['nullable', 'integer', 'min:30', 'max:200'],
            'blood_glucose' => ['nullable', 'numeric', 'min:1', 'max:50'],
            'glucose_type' => ['nullable', 'string'],
            'temperature_f' => ['nullable', 'numeric', 'min:90', 'max:115'],
            'weight_kg' => ['nullable', 'numeric', 'min:1', 'max:300'],
            'pulse_rate' => ['nullable', 'integer', 'min:30', 'max:250'],
            'symptoms' => ['nullable', 'string'],
            'medication_adherence' => ['nullable', 'in:good,partial,poor'],
            'patient_concerns' => ['nullable', 'string'],
            'escalation_needed' => ['nullable', 'boolean'],

            // Health Report (optional)
            'submit_report' => ['nullable', 'boolean'],
            'report_title' => ['required_if:submit_report,1', 'nullable', 'string', 'max:255'],
            'report_summary' => ['required_if:submit_report,1', 'nullable', 'string'],
        ]);

        $outcome = CallOutcome::tryFrom($validated['outcome']) ?? CallOutcome::Completed;
        $followUpPlan = ! empty($validated['follow_up_plan_id']) ? FollowUpPlan::find($validated['follow_up_plan_id']) : null;
        $nextDate = ! empty($validated['next_follow_up_date']) ? Carbon::parse($validated['next_follow_up_date']) : null;

        // 1. Record Call
        $call = $this->nurseWorkflowService->recordCall(
            clinic: $clinic,
            patient: $patient,
            nurse: $nurse,
            outcome: $outcome,
            durationSeconds: (int) $validated['duration_seconds'],
            reached: (bool) $validated['reached'],
            notes: $validated['notes'] ?? null,
            nextFollowUpDate: $nextDate,
            followUpPlan: $followUpPlan
        );

        // 2. Record Observation if vitals entered
        if (! empty($validated['record_vitals'])) {
            $this->nurseWorkflowService->recordObservation(
                clinic: $clinic,
                patient: $patient,
                recordedBy: $nurse,
                data: [
                    'systolic_bp' => $validated['systolic_bp'] ?? null,
                    'diastolic_bp' => $validated['diastolic_bp'] ?? null,
                    'blood_glucose' => $validated['blood_glucose'] ?? null,
                    'glucose_type' => $validated['glucose_type'] ?? null,
                    'temperature_f' => $validated['temperature_f'] ?? null,
                    'weight_kg' => $validated['weight_kg'] ?? null,
                    'pulse_rate' => $validated['pulse_rate'] ?? null,
                    'symptoms' => $validated['symptoms'] ?? null,
                    'medication_adherence' => $validated['medication_adherence'] ?? 'good',
                    'patient_concerns' => $validated['patient_concerns'] ?? null,
                    'escalation_needed' => (bool) ($validated['escalation_needed'] ?? false),
                ],
                call: $call
            );
        }

        // 3. Submit Health Report to Doctor Review Queue if requested
        if (! empty($validated['submit_report'])) {
            $this->nurseWorkflowService->submitHealthReport(
                clinic: $clinic,
                patient: $patient,
                nurse: $nurse,
                title: $validated['report_title'],
                summary: $validated['report_summary'],
                call: $call,
                followUpPlan: $followUpPlan
            );

            return redirect()->route('nurse.index')->with('success', 'Call logged and Health Report submitted for Doctor review. Nurse commission recorded.');
        }

        return redirect()->route('nurse.index')->with('success', 'Call record saved successfully.');
    }
}
