<?php

use App\Models\Clinic;
use App\Models\ClinicMembership;
use App\Models\Patient;
use App\Models\User;
use App\Services\TenantContext;

beforeEach(function () {
    $this->seed();
});

test('clinic A cannot see clinic B patients under tenant context', function () {
    $clinic1 = Clinic::where('slug', 'carepoint')->firstOrFail();
    $clinic2 = Clinic::where('slug', 'apex-health')->firstOrFail();

    // In Clinic 1 context
    TenantContext::setClinic($clinic1);
    $clinic1Patients = Patient::all();

    expect($clinic1Patients)->not->toBeEmpty()
        ->and($clinic1Patients->pluck('clinic_id')->unique()->toArray())->toBe([$clinic1->id]);

    // Ensure clinic 2 patient is not present
    $clinic2Patient = Patient::withoutGlobalScopes()->where('clinic_id', $clinic2->id)->firstOrFail();
    expect($clinic1Patients->pluck('id'))->not->toContain($clinic2Patient->id);

    // Switch to Clinic 2 context
    TenantContext::setClinic($clinic2);
    $clinic2Patients = Patient::all();

    expect($clinic2Patients->pluck('clinic_id')->unique()->toArray())->toBe([$clinic2->id])
        ->and($clinic2Patients->pluck('id'))->toContain($clinic2Patient->id);

    TenantContext::clear();
});

test('user cannot access or switch to a clinic they have no membership in', function () {
    $clinic2 = Clinic::where('slug', 'apex-health')->firstOrFail();
    $nurse = User::where('email', 'nurse.poly@carepoint.test')->firstOrFail();

    // Nurse Poly is only in CarePoint, NOT in Apex
    expect(ClinicMembership::where('clinic_id', $clinic2->id)->where('user_id', $nurse->id)->exists())->toBeFalse();

    $response = $this->actingAs($nurse)
        ->post("/switch-clinic/{$clinic2->id}");

    $response->assertStatus(403);
});
