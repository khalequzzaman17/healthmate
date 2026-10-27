<?php

use App\Enums\AppointmentStatus;
use App\Enums\CommissionStatus;
use App\Enums\HealthReportStatus;
use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\CommissionLedgerEntry;
use App\Models\Consultation;
use App\Models\HealthReport;
use App\Models\User;
use App\Services\DoctorConsultationService;
use App\Services\TenantContext;

beforeEach(function () {
    $this->seed();
});

test('doctor can review report and complete consultation with diagnoses, prescription, and commission', function () {
    $clinic = Clinic::where('slug', 'carepoint')->firstOrFail();
    $doctor = User::where('email', 'dr.tariq@carepoint.test')->firstOrFail();
    $report = HealthReport::where('clinic_id', $clinic->id)->firstOrFail();
    $appointment = Appointment::where('clinic_id', $clinic->id)->firstOrFail();

    TenantContext::setClinic($clinic);

    $docService = app(DoctorConsultationService::class);

    // 1. Review Health Report
    $reviewedReport = $docService->reviewHealthReport(
        report: $report,
        doctor: $doctor,
        reviewNotes: 'Noted elevated FBG. Will adjust oral hypoglycemic agent and monitor.'
    );

    expect($reviewedReport->status)->toBe(HealthReportStatus::Reviewed)
        ->and($reviewedReport->reviewed_by_doctor_id)->toBe($doctor->id);

    // 2. Complete Consultation
    $consultation = $docService->completeConsultation(
        clinic: $clinic,
        appointment: $appointment,
        doctor: $doctor,
        clinicalData: [
            'chief_complaints' => 'Fatigue, elevated morning fasting blood glucose',
            'examination_findings' => 'Chest clear, Heart sounds normal, BP 142/90',
            'clinical_notes' => 'Uncontrolled Type 2 Diabetes on current regimen',
            'advice' => 'Dietary counseling, reduce simple carbohydrates, brisk walk 30 mins daily',
        ],
        diagnoses: [
            [
                'diagnosis_name' => 'Type 2 Diabetes Mellitus with hyperglycemia',
                'diagnosis_code' => 'E11.9',
                'type' => 'primary',
            ],
            [
                'diagnosis_name' => 'Essential Hypertension',
                'diagnosis_code' => 'I10',
                'type' => 'secondary',
            ],
        ],
        prescriptionItems: [
            [
                'medicine_name' => 'Tab. Metformin XR',
                'strength' => '1000mg',
                'dosage_form' => 'tablet',
                'dosage_instructions' => '0+0+1',
                'duration_days' => 30,
                'special_instructions' => 'Take with evening meal',
            ],
            [
                'medicine_name' => 'Tab. Empagliflozin',
                'strength' => '10mg',
                'dosage_form' => 'tablet',
                'dosage_instructions' => '1+0+0',
                'duration_days' => 30,
                'special_instructions' => 'Take before breakfast',
            ],
        ],
        healthReport: $reviewedReport
    );

    expect($consultation->status)->toBe('completed')
        ->and($consultation->diagnoses)->toHaveCount(2)
        ->and($consultation->prescription)->not->toBeNull()
        ->and($consultation->prescription->items)->toHaveCount(2)
        ->and($appointment->fresh()->status)->toBe(AppointmentStatus::Completed);

    // 3. Commission entry automatically created for Doctor
    $entry = CommissionLedgerEntry::where('source_type', Consultation::class)
        ->where('source_id', $consultation->id)
        ->first();

    expect($entry)->not->toBeNull()
        ->and($entry->staff_user_id)->toBe($doctor->id)
        ->and((float) $entry->amount)->toBe(250.00)
        ->and($entry->status)->toBe(CommissionStatus::Pending);

    TenantContext::clear();
});
