<?php

namespace App\Http\Controllers;

use App\Enums\HealthReportStatus;
use App\Models\Appointment;
use App\Models\HealthReport;
use App\Models\User;
use App\Services\DoctorConsultationService;
use App\Services\TenantContext;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DoctorWorkspaceController extends Controller
{
    public function __construct(
        protected DoctorConsultationService $doctorConsultationService
    ) {}

    public function index(Request $request): View
    {
        $doctor = Auth::user();
        $today = Carbon::today();

        $todayAppointments = Appointment::where('doctor_id', $doctor->id)
            ->whereDate('scheduled_at', $today)
            ->with(['patient', 'service'])
            ->orderBy('scheduled_at')
            ->get();

        $reviewQueue = HealthReport::where('status', HealthReportStatus::Submitted->value)
            ->with(['patient', 'nurse', 'call.healthObservations'])
            ->latest('submitted_at')
            ->get();

        return view('doctor.index', compact('todayAppointments', 'reviewQueue'));
    }

    public function showReport(HealthReport $report): View
    {
        $report->load(['patient.conditions', 'patient.allergies', 'nurse', 'call.healthObservations']);

        return view('doctor.review_report', compact('report'));
    }

    public function submitReview(Request $request, HealthReport $report): RedirectResponse
    {
        $doctor = Auth::user();

        $validated = $request->validate([
            'review_notes' => ['required', 'string', 'max:1000'],
        ]);

        $this->doctorConsultationService->reviewHealthReport(
            report: $report,
            doctor: $doctor,
            reviewNotes: $validated['review_notes']
        );

        return redirect()->route('doctor.index')->with('success', 'Health report reviewed and clinical notes recorded.');
    }

    public function consultation(Appointment $appointment): View
    {
        $appointment->load(['patient.allergies', 'patient.conditions', 'patient.consultations.diagnoses', 'service']);

        $clinic = TenantContext::getClinic();
        $nurses = User::whereHas('clinicMemberships', function ($q) use ($clinic) {
            $q->where('clinic_id', $clinic->id)->where('role', 'nurse')->where('status', 'active');
        })->get();

        return view('doctor.consultation', compact('appointment', 'nurses'));
    }

    public function storeConsultation(Request $request, Appointment $appointment): RedirectResponse
    {
        $doctor = Auth::user();
        $clinic = TenantContext::getClinic();

        $validated = $request->validate([
            'chief_complaints' => ['required', 'string'],
            'examination_findings' => ['nullable', 'string'],
            'clinical_notes' => ['nullable', 'string'],
            'advice' => ['nullable', 'string'],

            // Diagnoses
            'diagnoses' => ['nullable', 'array'],
            'diagnoses.*.diagnosis_name' => ['nullable', 'string', 'max:255'],
            'diagnoses.*.diagnosis_code' => ['nullable', 'string', 'max:50'],
            'diagnoses.*.type' => ['nullable', 'string'],

            // Prescription items
            'prescription_items' => ['nullable', 'array'],
            'prescription_items.*.medicine_name' => ['nullable', 'string', 'max:255'],
            'prescription_items.*.strength' => ['nullable', 'string', 'max:50'],
            'prescription_items.*.dosage_form' => ['nullable', 'string', 'max:50'],
            'prescription_items.*.dosage_instructions' => ['nullable', 'string', 'max:100'],
            'prescription_items.*.duration_days' => ['nullable', 'integer', 'min:1', 'max:365'],
            'prescription_items.*.special_instructions' => ['nullable', 'string', 'max:255'],

            // Optional follow up scheduling
            'schedule_follow_up' => ['nullable', 'boolean'],
            'next_follow_up_date' => ['nullable', 'date'],
            'assigned_nurse_id' => ['nullable', 'exists:users,id'],
        ]);

        $nextFollowUpDate = ! empty($validated['schedule_follow_up']) && ! empty($validated['next_follow_up_date'])
            ? Carbon::parse($validated['next_follow_up_date'])
            : null;

        $nurse = ! empty($validated['assigned_nurse_id']) ? User::find($validated['assigned_nurse_id']) : null;

        $this->doctorConsultationService->completeConsultation(
            clinic: $clinic,
            appointment: $appointment,
            doctor: $doctor,
            clinicalData: [
                'chief_complaints' => $validated['chief_complaints'],
                'examination_findings' => $validated['examination_findings'] ?? null,
                'clinical_notes' => $validated['clinical_notes'] ?? null,
                'advice' => $validated['advice'] ?? null,
            ],
            diagnoses: $validated['diagnoses'] ?? [],
            prescriptionItems: $validated['prescription_items'] ?? [],
            nextFollowUpDate: $nextFollowUpDate,
            assignedNurseForFollowUp: $nurse
        );

        return redirect()->route('patients.show', $appointment->patient_id)
            ->with('success', 'Consultation completed successfully. Prescription issued and Doctor commission recorded.');
    }
}
