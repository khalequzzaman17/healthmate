<?php

use App\Enums\CallOutcome;
use App\Enums\CommissionStatus;
use App\Enums\FollowUpStatus;
use App\Enums\HealthReportStatus;
use App\Models\Clinic;
use App\Models\CommissionLedgerEntry;
use App\Models\HealthReport;
use App\Models\Patient;
use App\Models\User;
use App\Services\CommissionService;
use App\Services\NurseWorkflowService;
use App\Services\TenantContext;

beforeEach(function () {
    $this->seed();
});

test('nurse can record call, observation, and submit health report generating commission', function () {
    $clinic = Clinic::where('slug', 'carepoint')->firstOrFail();
    $nurse = User::where('email', 'nurse.poly@carepoint.test')->firstOrFail();
    $patient = Patient::withoutGlobalScopes()->where('patient_number', 'HM-2026-0002')->firstOrFail();

    TenantContext::setClinic($clinic);

    $nurseService = app(NurseWorkflowService::class);

    // 1. Create follow-up plan
    $plan = $nurseService->createFollowUpPlan(
        clinic: $clinic,
        patient: $patient,
        nurse: $nurse,
        dueDate: now()->addDays(2),
        instructions: 'Check vital signs and blood pressure'
    );

    expect($plan->status)->toBe(FollowUpStatus::Scheduled);

    // 2. Record call
    $call = $nurseService->recordCall(
        clinic: $clinic,
        patient: $patient,
        nurse: $nurse,
        outcome: CallOutcome::Completed,
        durationSeconds: 180,
        reached: true,
        notes: 'Patient felt well, reported normal readings',
        followUpPlan: $plan
    );

    expect($call->outcome)->toBe(CallOutcome::Completed)
        ->and($call->reached)->toBeTrue();

    // 3. Record structured health observations
    $obs = $nurseService->recordObservation(
        clinic: $clinic,
        patient: $patient,
        recordedBy: $nurse,
        data: [
            'systolic_bp' => 120,
            'diastolic_bp' => 80,
            'blood_glucose' => 5.8,
            'glucose_type' => 'fasting',
            'temperature_f' => 98.6,
            'weight_kg' => 62.0,
            'pulse_rate' => 72,
            'medication_adherence' => 'good',
            'escalation_needed' => false,
        ],
        call: $call
    );

    expect((float) $obs->blood_glucose)->toBe(5.8)
        ->and($obs->systolic_bp)->toBe(120);

    // 4. Submit Health Report
    $report = $nurseService->submitHealthReport(
        clinic: $clinic,
        patient: $patient,
        nurse: $nurse,
        title: 'Routine Health Check Report',
        summary: 'Patient vitals stable. BP 120/80, FBG 5.8 mmol/L.',
        call: $call,
        followUpPlan: $plan
    );

    expect($report->status)->toBe(HealthReportStatus::Submitted)
        ->and($plan->fresh()->status)->toBe(FollowUpStatus::Completed);

    // 5. Commission entry automatically created with idempotency
    $entry = CommissionLedgerEntry::where('source_type', HealthReport::class)
        ->where('source_id', $report->id)
        ->first();

    expect($entry)->not->toBeNull()
        ->and($entry->staff_user_id)->toBe($nurse->id)
        ->and((float) $entry->amount)->toBe(50.00)
        ->and($entry->status)->toBe(CommissionStatus::Pending);

    // Retrying the commission record does NOT create duplicate
    $duplicate = app(CommissionService::class)->recordEarning(
        clinic: $clinic,
        staffUser: $nurse,
        staffRole: 'nurse',
        activityType: 'nurse_follow_up',
        source: $report,
        calculationBasis: 0.0
    );

    expect($duplicate->id)->toBe($entry->id)
        ->and(CommissionLedgerEntry::where('source_type', HealthReport::class)->where('source_id', $report->id)->count())->toBe(1);

    TenantContext::clear();
});
