<?php

namespace App\Services;

use App\Enums\CallOutcome;
use App\Enums\FollowUpStatus;
use App\Enums\HealthReportStatus;
use App\Models\Clinic;
use App\Models\FollowUpPlan;
use App\Models\HealthObservation;
use App\Models\HealthReport;
use App\Models\Patient;
use App\Models\PatientAssignment;
use App\Models\PatientCall;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class NurseWorkflowService
{
    public function __construct(
        protected CommissionService $commissionService
    ) {}

    /**
     * Assign a patient to a nurse.
     */
    public function assignPatient(Clinic $clinic, Patient $patient, User $nurse, User $assignedBy): PatientAssignment
    {
        return DB::transaction(function () use ($clinic, $patient, $nurse, $assignedBy) {
            // Deactivate any existing active assignments for this patient
            PatientAssignment::where('clinic_id', $clinic->id)
                ->where('patient_id', $patient->id)
                ->where('status', 'active')
                ->update(['status' => 'inactive']);

            $assignment = PatientAssignment::create([
                'clinic_id' => $clinic->id,
                'patient_id' => $patient->id,
                'nurse_id' => $nurse->id,
                'assigned_by_user_id' => $assignedBy->id,
                'status' => 'active',
            ]);

            AuditService::log('patient.assigned_to_nurse', $assignment, null, [
                'nurse_id' => $nurse->id,
                'patient_id' => $patient->id,
            ], $clinic->id);

            return $assignment;
        });
    }

    /**
     * Create a scheduled follow-up plan for a patient.
     */
    public function createFollowUpPlan(
        Clinic $clinic,
        Patient $patient,
        User $nurse,
        Carbon $dueDate,
        string $frequency = 'once',
        string $priority = 'normal',
        ?string $instructions = null,
        ?User $doctor = null
    ): FollowUpPlan {
        $plan = FollowUpPlan::create([
            'clinic_id' => $clinic->id,
            'patient_id' => $patient->id,
            'assigned_nurse_id' => $nurse->id,
            'doctor_id' => $doctor?->id,
            'due_date' => $dueDate,
            'frequency' => $frequency,
            'priority' => $priority,
            'instructions' => $instructions,
            'status' => FollowUpStatus::Scheduled,
        ]);

        AuditService::log('follow_up.scheduled', $plan, null, [
            'patient_id' => $patient->id,
            'nurse_id' => $nurse->id,
            'due_date' => $dueDate->toDateString(),
        ], $clinic->id);

        return $plan;
    }

    /**
     * Record a call attempt and structured observation.
     */
    public function recordCall(
        Clinic $clinic,
        Patient $patient,
        User $nurse,
        CallOutcome $outcome,
        int $durationSeconds = 0,
        bool $reached = true,
        ?string $notes = null,
        ?Carbon $nextFollowUpDate = null,
        ?FollowUpPlan $followUpPlan = null
    ): PatientCall {
        $call = PatientCall::create([
            'clinic_id' => $clinic->id,
            'patient_id' => $patient->id,
            'nurse_id' => $nurse->id,
            'follow_up_plan_id' => $followUpPlan?->id,
            'call_date' => now(),
            'duration_seconds' => $durationSeconds,
            'reached' => $reached,
            'outcome' => $outcome,
            'notes' => $notes,
            'next_follow_up_date' => $nextFollowUpDate,
        ]);

        AuditService::log('nurse.call_recorded', $call, null, [
            'patient_id' => $patient->id,
            'outcome' => $outcome->value,
            'reached' => $reached,
        ], $clinic->id);

        return $call;
    }

    /**
     * Record structured health observations.
     */
    public function recordObservation(
        Clinic $clinic,
        Patient $patient,
        User $recordedBy,
        array $data,
        ?PatientCall $call = null
    ): HealthObservation {
        $observation = HealthObservation::create([
            'clinic_id' => $clinic->id,
            'patient_id' => $patient->id,
            'recorded_by_user_id' => $recordedBy->id,
            'patient_call_id' => $call?->id,
            'systolic_bp' => $data['systolic_bp'] ?? null,
            'diastolic_bp' => $data['diastolic_bp'] ?? null,
            'blood_glucose' => $data['blood_glucose'] ?? null,
            'glucose_type' => $data['glucose_type'] ?? null,
            'temperature_f' => $data['temperature_f'] ?? null,
            'weight_kg' => $data['weight_kg'] ?? null,
            'pulse_rate' => $data['pulse_rate'] ?? null,
            'symptoms' => $data['symptoms'] ?? null,
            'medication_adherence' => $data['medication_adherence'] ?? 'good',
            'patient_concerns' => $data['patient_concerns'] ?? null,
            'escalation_needed' => (bool) ($data['escalation_needed'] ?? false),
        ]);

        AuditService::log('health_observation.recorded', $observation, null, [
            'patient_id' => $patient->id,
            'escalation_needed' => $observation->escalation_needed,
        ], $clinic->id);

        return $observation;
    }

    /**
     * Submit a comprehensive health report to the Doctor Review Queue.
     * Completes follow-up plan and generates Nurse commission earning event.
     */
    public function submitHealthReport(
        Clinic $clinic,
        Patient $patient,
        User $nurse,
        string $title,
        string $summary,
        ?PatientCall $call = null,
        ?FollowUpPlan $followUpPlan = null
    ): HealthReport {
        return DB::transaction(function () use ($clinic, $patient, $nurse, $title, $summary, $call, $followUpPlan) {
            $report = HealthReport::create([
                'clinic_id' => $clinic->id,
                'patient_id' => $patient->id,
                'nurse_id' => $nurse->id,
                'patient_call_id' => $call?->id,
                'title' => $title,
                'summary' => $summary,
                'status' => HealthReportStatus::Submitted,
                'submitted_at' => now(),
            ]);

            // Mark the follow-up plan as completed if one was linked
            if ($followUpPlan) {
                $followUpPlan->update([
                    'status' => FollowUpStatus::Completed,
                ]);
            }

            // Calculate & record nurse commission for this completed follow-up/report
            $this->commissionService->recordEarning(
                clinic: $clinic,
                staffUser: $nurse,
                staffRole: 'nurse',
                activityType: 'nurse_follow_up',
                source: $report,
                calculationBasis: 0.0
            );

            AuditService::log('health_report.submitted', $report, null, [
                'patient_id' => $patient->id,
                'nurse_id' => $nurse->id,
                'title' => $title,
            ], $clinic->id);

            return $report;
        });
    }
}
