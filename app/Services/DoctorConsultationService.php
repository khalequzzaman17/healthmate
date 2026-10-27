<?php

namespace App\Services;

use App\Enums\AppointmentStatus;
use App\Enums\HealthReportStatus;
use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\Consultation;
use App\Models\Diagnosis;
use App\Models\FollowUpPlan;
use App\Models\HealthReport;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DoctorConsultationService
{
    public function __construct(
        protected CommissionService $commissionService,
        protected AppointmentService $appointmentService
    ) {}

    /**
     * Doctor reviews a Nurse submitted health report.
     */
    public function reviewHealthReport(
        HealthReport $report,
        User $doctor,
        string $reviewNotes
    ): HealthReport {
        $report->update([
            'status' => HealthReportStatus::Reviewed,
            'reviewed_by_doctor_id' => $doctor->id,
            'reviewed_at' => now(),
            'review_notes' => $reviewNotes,
        ]);

        AuditService::log('health_report.reviewed', $report, null, [
            'doctor_id' => $doctor->id,
            'report_id' => $report->id,
        ], $report->clinic_id);

        return $report;
    }

    /**
     * Complete consultation with clinical documentation, diagnoses, prescription, and commission.
     *
     * @param  array<array<string, mixed>>  $diagnoses
     * @param  array<array<string, mixed>>  $prescriptionItems
     */
    public function completeConsultation(
        Clinic $clinic,
        Appointment $appointment,
        User $doctor,
        array $clinicalData,
        array $diagnoses = [],
        array $prescriptionItems = [],
        ?HealthReport $healthReport = null,
        ?Carbon $nextFollowUpDate = null,
        ?User $assignedNurseForFollowUp = null
    ): Consultation {
        return DB::transaction(function () use (
            $clinic,
            $appointment,
            $doctor,
            $clinicalData,
            $diagnoses,
            $prescriptionItems,
            $healthReport,
            $nextFollowUpDate,
            $assignedNurseForFollowUp
        ) {
            $patient = $appointment->patient;

            // 1. Create Consultation
            $consultation = Consultation::create([
                'clinic_id' => $clinic->id,
                'appointment_id' => $appointment->id,
                'patient_id' => $patient->id,
                'doctor_id' => $doctor->id,
                'health_report_id' => $healthReport?->id,
                'started_at' => $clinicalData['started_at'] ?? now(),
                'completed_at' => now(),
                'chief_complaints' => $clinicalData['chief_complaints'] ?? null,
                'examination_findings' => $clinicalData['examination_findings'] ?? null,
                'clinical_notes' => $clinicalData['clinical_notes'] ?? null,
                'advice' => $clinicalData['advice'] ?? null,
                'status' => 'completed',
            ]);

            // 2. Create Diagnoses
            foreach ($diagnoses as $diag) {
                if (empty($diag['diagnosis_name'])) {
                    continue;
                }

                Diagnosis::create([
                    'clinic_id' => $clinic->id,
                    'consultation_id' => $consultation->id,
                    'patient_id' => $patient->id,
                    'doctor_id' => $doctor->id,
                    'diagnosis_code' => $diag['diagnosis_code'] ?? null,
                    'diagnosis_name' => $diag['diagnosis_name'],
                    'type' => $diag['type'] ?? 'primary',
                    'notes' => $diag['notes'] ?? null,
                ]);
            }

            // 3. Create Prescription & Items if provided
            if (! empty($prescriptionItems)) {
                $rxNumber = 'RX-'.date('Ymd').'-'.strtoupper(bin2hex(random_bytes(3)));

                $prescription = Prescription::create([
                    'clinic_id' => $clinic->id,
                    'consultation_id' => $consultation->id,
                    'patient_id' => $patient->id,
                    'doctor_id' => $doctor->id,
                    'prescription_number' => $rxNumber,
                    'instructions' => $clinicalData['advice'] ?? null,
                    'issued_at' => now(),
                ]);

                foreach ($prescriptionItems as $item) {
                    if (empty($item['medicine_name'])) {
                        continue;
                    }

                    PrescriptionItem::create([
                        'prescription_id' => $prescription->id,
                        'medicine_name' => $item['medicine_name'],
                        'strength' => $item['strength'] ?? null,
                        'dosage_form' => $item['dosage_form'] ?? 'tablet',
                        'dosage_instructions' => $item['dosage_instructions'] ?? '1+0+1',
                        'route' => $item['route'] ?? 'oral',
                        'duration_days' => (int) ($item['duration_days'] ?? 7),
                        'special_instructions' => $item['special_instructions'] ?? null,
                    ]);
                }
            }

            // 4. Create Follow-up plan if indicated
            if ($nextFollowUpDate && $assignedNurseForFollowUp) {
                FollowUpPlan::create([
                    'clinic_id' => $clinic->id,
                    'patient_id' => $patient->id,
                    'assigned_nurse_id' => $assignedNurseForFollowUp->id,
                    'doctor_id' => $doctor->id,
                    'due_date' => $nextFollowUpDate,
                    'frequency' => 'once',
                    'priority' => 'normal',
                    'instructions' => 'Post-consultation check-in as directed by Dr. '.$doctor->name,
                ]);
            }

            // 5. Transition Appointment status to Completed
            if ($appointment->status === AppointmentStatus::Scheduled) {
                $this->appointmentService->transitionStatus($appointment, AppointmentStatus::CheckedIn, $doctor, 'Arrived for consultation');
            }
            if ($appointment->status === AppointmentStatus::CheckedIn) {
                $this->appointmentService->transitionStatus($appointment, AppointmentStatus::InConsultation, $doctor, 'Consultation in progress');
            }
            $this->appointmentService->transitionStatus($appointment, AppointmentStatus::Completed, $doctor, 'Consultation concluded');

            // 6. Record Doctor Commission Earning Event
            $serviceFee = (float) ($appointment->service?->price ?? 0);
            $this->commissionService->recordEarning(
                clinic: $clinic,
                staffUser: $doctor,
                staffRole: 'doctor',
                activityType: 'doctor_consultation',
                source: $consultation,
                calculationBasis: $serviceFee
            );

            AuditService::log('consultation.completed', $consultation, null, [
                'doctor_id' => $doctor->id,
                'patient_id' => $patient->id,
                'appointment_id' => $appointment->id,
            ], $clinic->id);

            return $consultation->load(['diagnoses', 'prescription.items']);
        });
    }
}
