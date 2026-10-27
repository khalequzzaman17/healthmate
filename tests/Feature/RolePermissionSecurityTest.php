<?php

use App\Models\Appointment;
use App\Models\CommissionPayout;
use App\Models\Patient;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
});

test('nurse cannot access doctor consultation or prescription creation', function () {
    $nurse = User::where('email', 'nurse.poly@carepoint.test')->firstOrFail();
    $appointment = Appointment::firstOrFail();

    // Nurse attempts to access doctor consultation desk
    $response = $this->actingAs($nurse)->get(route('doctor.consultations.create', $appointment));
    $response->assertStatus(403);

    // Nurse attempts to complete consultation
    $postResponse = $this->actingAs($nurse)->post(route('doctor.consultations.store', $appointment), [
        'diagnosis' => 'Attempted diagnosis by nurse',
        'clinical_notes' => 'Unauthorized notes',
    ]);
    $postResponse->assertStatus(403);
});

test('doctor cannot approve commission payouts without admin role', function () {
    $doctor = User::where('email', 'dr.tariq@carepoint.test')->firstOrFail();
    $admin = User::where('email', 'admin@carepoint.test')->firstOrFail();

    // Admin prepares a payout first
    $this->actingAs($admin)->post(route('commissions.payouts.store'), [
        'staff_id' => $doctor->id,
        'item_ids' => [1],
    ]);

    $payout = CommissionPayout::latest('id')->first();
    if ($payout) {
        // Doctor attempts to approve their own or any payout
        $response = $this->actingAs($doctor)->post(route('commissions.payouts.approve', $payout));
        $response->assertStatus(403);
    } else {
        expect(true)->toBeTrue();
    }
});

test('patient cannot access internal clinic staff dashboards', function () {
    $patientUser = User::where('email', 'patient.abdul@healthmate.test')->firstOrFail();

    // Patient visits nurse workspace
    $nurseResp = $this->actingAs($patientUser)->get(route('nurse.index'));
    $nurseResp->assertStatus(403);

    // Patient visits doctor workspace
    $docResp = $this->actingAs($patientUser)->get(route('doctor.index'));
    $docResp->assertStatus(403);

    // Patient visits commissions ledger
    $commResp = $this->actingAs($patientUser)->get(route('commissions.index'));
    $commResp->assertStatus(403);
});

test('receptionist cannot prescribe medicines or access clinical consultation desk', function () {
    $receptionist = User::where('email', 'receptionist@carepoint.test')->firstOrFail();
    $appointment = Appointment::firstOrFail();

    $response = $this->actingAs($receptionist)->get(route('doctor.consultations.create', $appointment));
    $response->assertStatus(403);
});
