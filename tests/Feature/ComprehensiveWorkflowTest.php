<?php

use App\Enums\AppointmentStatus;
use App\Enums\CommissionStatus;
use App\Enums\HealthReportStatus;
use App\Enums\InvoiceStatus;
use App\Enums\PayoutStatus;
use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\CommissionLedgerEntry;
use App\Models\CommissionPayout;
use App\Models\Consultation;
use App\Models\HealthReport;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\User;

beforeEach(function () {
    $this->seed();
});

test('user can login and access dashboard', function () {
    $admin = User::where('email', 'admin@carepoint.test')->firstOrFail();

    $response = $this->post('/login', [
        'email' => 'admin@carepoint.test',
        'password' => 'password',
    ]);

    $response->assertRedirect(route('dashboard'));
    $this->assertAuthenticatedAs($admin);

    $dashResponse = $this->actingAs($admin)->get('/dashboard');
    $dashResponse->assertStatus(200);
    $dashResponse->assertSee('CarePoint Multi-Specialty Clinic');
    $dashResponse->assertSee('Clinic Administration');
});

test('patient registration creates clinic-specific patient record', function () {
    $admin = User::where('email', 'admin@carepoint.test')->firstOrFail();

    $response = $this->actingAs($admin)->post('/patients', [
        'first_name' => 'Nazmul',
        'last_name' => 'Hasan',
        'gender' => 'male',
        'phone' => '+8801799887766',
        'email' => 'nazmul@example.test',
        'date_of_birth' => '1992-08-10',
        'blood_group' => 'A+',
        'allergies' => 'Dust, Penicillin',
        'chronic_conditions' => 'Hypertension',
    ]);

    $patient = Patient::withoutGlobalScopes()->where('phone', '+8801799887766')->first();
    expect($patient)->not->toBeNull()
        ->and($patient->first_name)->toBe('Nazmul')
        ->and($patient->allergies)->toHaveCount(2)
        ->and($patient->conditions)->toHaveCount(1);

    $response->assertRedirect(route('patients.show', $patient));
});

test('appointment booking, check-in, and conflict rejection', function () {
    $admin = User::where('email', 'admin@carepoint.test')->firstOrFail();
    $doctor = User::where('email', 'dr.tariq@carepoint.test')->firstOrFail();
    $patient = Patient::withoutGlobalScopes()->where('patient_number', 'HM-2026-0001')->firstOrFail();

    $tomorrow = now()->addDay()->toDateString();

    // 1. Book first appointment at 11:00
    $response = $this->actingAs($admin)->post('/appointments', [
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
        'scheduled_date' => $tomorrow,
        'scheduled_time' => '11:00',
        'duration_minutes' => 30,
        'notes' => 'Regular checkup',
    ]);

    $response->assertSessionHasNoErrors();

    $appt = Appointment::where('doctor_id', $doctor->id)
        ->latest('id')
        ->firstOrFail();

    expect($appt->status)->toBe(AppointmentStatus::Scheduled);

    // 2. Attempt to book an overlapping appointment for the same doctor at 11:15
    $overlapResponse = $this->actingAs($admin)->post('/appointments', [
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
        'scheduled_date' => $tomorrow,
        'scheduled_time' => '11:15',
        'duration_minutes' => 20,
    ]);

    $overlapResponse->assertSessionHasErrors('scheduled_at');

    // 3. Check-in the appointment
    $this->actingAs($admin)->post(route('appointments.check-in', $appt));
    expect($appt->fresh()->status)->toBe(AppointmentStatus::CheckedIn);
});

test('complete healthcare lifecycle: nurse follow-up -> doctor consult -> commission payout', function () {
    $nurse = User::where('email', 'nurse.poly@carepoint.test')->firstOrFail();
    $doctor = User::where('email', 'dr.tariq@carepoint.test')->firstOrFail();
    $admin = User::where('email', 'admin@carepoint.test')->firstOrFail();
    $patient = Patient::withoutGlobalScopes()->where('patient_number', 'HM-2026-0001')->firstOrFail();
    $clinic = Clinic::where('slug', 'carepoint')->firstOrFail();

    // 1. Nurse records call and submits health report
    $callResponse = $this->actingAs($nurse)->post(route('nurse.call.store', $patient), [
        'outcome' => 'completed',
        'duration_seconds' => 240,
        'reached' => 1,
        'notes' => 'Patient reached, BP stable',
        'record_vitals' => 1,
        'systolic_bp' => 125,
        'diastolic_bp' => 82,
        'blood_glucose' => 6.2,
        'glucose_type' => 'fasting',
        'submit_report' => 1,
        'report_title' => 'Follow-up Report for Abdul Karim',
        'report_summary' => 'Vitals stable. Continuing prescribed therapy.',
    ]);

    $callResponse->assertSessionHasNoErrors();

    $report = HealthReport::where('title', 'Follow-up Report for Abdul Karim')->firstOrFail();
    expect($report->status)->toBe(HealthReportStatus::Submitted);

    // Nurse commission entry was created
    $nurseCommission = CommissionLedgerEntry::where('source_type', HealthReport::class)
        ->where('source_id', $report->id)
        ->firstOrFail();
    expect((float) $nurseCommission->amount)->toBe(50.00);

    // 2. Doctor reviews report
    $this->actingAs($doctor)->post(route('doctor.reports.review', $report), [
        'review_notes' => 'Good progress. Maintain current lifestyle.',
    ]);
    expect($report->fresh()->status)->toBe(HealthReportStatus::Reviewed);

    // 3. Doctor completes consultation for existing appointment
    $appointment = Appointment::where('patient_id', $patient->id)->firstOrFail();

    $consultResponse = $this->actingAs($doctor)->post(route('doctor.consultations.store', $appointment), [
        'chief_complaints' => 'Routine monitoring',
        'examination_findings' => 'All vitals normal',
        'clinical_notes' => 'Patient complying with therapy',
        'advice' => 'Continue regular exercise',
        'diagnoses' => [
            ['diagnosis_name' => 'Diabetes Mellitus Type 2', 'diagnosis_code' => 'E11', 'type' => 'primary'],
        ],
        'prescription_items' => [
            ['medicine_name' => 'Tab. Metformin', 'strength' => '500mg', 'dosage_form' => 'tablet', 'dosage_instructions' => '1+0+1', 'duration_days' => 30],
        ],
    ]);

    $consultResponse->assertSessionHasNoErrors();
    expect($appointment->fresh()->status)->toBe(AppointmentStatus::Completed);

    // Doctor commission entry was created
    $doctorCommission = CommissionLedgerEntry::where('source_type', Consultation::class)->latest('id')->firstOrFail();
    expect((float) $doctorCommission->amount)->toBe(250.00);

    // 4. Admin approves nurse commission and prepares payout
    $this->actingAs($admin)->post(route('commissions.approve', $nurseCommission));
    expect($nurseCommission->fresh()->status)->toBe(CommissionStatus::Approved);

    $payoutResponse = $this->actingAs($admin)->post(route('commissions.payouts.store'), [
        'staff_user_id' => $nurse->id,
        'notes' => 'Monthly nurse incentive payout',
    ]);
    $payoutResponse->assertSessionHasNoErrors();

    $payout = CommissionPayout::where('staff_user_id', $nurse->id)->latest('id')->firstOrFail();
    expect($payout->status)->toBe(PayoutStatus::Prepared);

    // 5. Admin approves and processes payout
    $this->actingAs($admin)->post(route('commissions.payouts.approve', $payout));
    expect($payout->fresh()->status)->toBe(PayoutStatus::Approved);

    $this->actingAs($admin)->post(route('commissions.payouts.process', $payout), [
        'payment_method' => 'bKash',
        'payment_reference' => 'TXN-BKASH-9090',
    ]);
    expect($payout->fresh()->status)->toBe(PayoutStatus::Paid)
        ->and($nurseCommission->fresh()->status)->toBe(CommissionStatus::Paid);
});

test('billing invoice creation and payment collection', function () {
    $admin = User::where('email', 'admin@carepoint.test')->firstOrFail();
    $patient = Patient::withoutGlobalScopes()->where('patient_number', 'HM-2026-0001')->firstOrFail();

    // 1. Create invoice
    $response = $this->actingAs($admin)->post('/billing', [
        'patient_id' => $patient->id,
        'items' => [
            ['description' => 'Diagnostic Lipid Profile', 'quantity' => 1, 'unit_price' => 1200],
            ['description' => 'HbA1c Blood Test', 'quantity' => 1, 'unit_price' => 800],
        ],
        'discount_amount' => 200,
        'tax_amount' => 0,
    ]);

    $response->assertSessionHasNoErrors();

    $invoice = Invoice::latest('id')->firstOrFail();
    expect((float) $invoice->total_amount)->toBe(1800.00) // (1200 + 800) - 200 = 1800
        ->and((float) $invoice->paid_amount)->toBe(0.00)
        ->and($invoice->status)->toBe(InvoiceStatus::Unpaid);

    // 2. Record partial payment
    $this->actingAs($admin)->post(route('billing.payments.store', $invoice), [
        'amount' => 1000.00,
        'method' => 'bKash',
        'reference' => 'BK-PAY-01',
    ]);

    expect((float) $invoice->fresh()->paid_amount)->toBe(1000.00)
        ->and($invoice->fresh()->status)->toBe(InvoiceStatus::PartiallyPaid)
        ->and((float) $invoice->fresh()->dueAmount())->toBe(800.00);

    // 3. Record remaining payment
    $this->actingAs($admin)->post(route('billing.payments.store', $invoice), [
        'amount' => 800.00,
        'method' => 'Cash',
    ]);

    expect((float) $invoice->fresh()->paid_amount)->toBe(1800.00)
        ->and($invoice->fresh()->status)->toBe(InvoiceStatus::Paid)
        ->and((float) $invoice->fresh()->dueAmount())->toBe(0.00);
});

test('cross tenant authorization denial prevents clinic B user from viewing clinic A patient', function () {
    $adminClinic2 = User::where('email', 'admin@apex.test')->firstOrFail();
    $patientClinic1 = Patient::withoutGlobalScopes()->where('patient_number', 'HM-2026-0001')->firstOrFail();

    // Admin of Apex (Clinic 2) tries to view Abdul Karim (Clinic 1)
    $response = $this->actingAs($adminClinic2)->get(route('patients.show', $patientClinic1));

    // Scoped query will not find Clinic 1's patient under Clinic 2's context
    $response->assertStatus(404);
});
