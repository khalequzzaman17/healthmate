<?php

namespace App\Services;

use App\Enums\ClinicRole;
use App\Enums\SubscriptionStatus;
use App\Models\Clinic;
use App\Models\ClinicLocation;
use App\Models\ClinicMembership;
use App\Models\CommissionRule;
use App\Models\Service;
use App\Models\StaffProfile;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TenantService
{
    /**
     * Create a clinic workspace and onboard the clinic owner.
     */
    public function createClinic(array $data, User $owner, ?SubscriptionPlan $plan = null): Clinic
    {
        return DB::transaction(function () use ($data, $owner, $plan) {
            $slug = Str::slug($data['name']);
            $count = Clinic::where('slug', 'like', "{$slug}%")->count();
            if ($count > 0) {
                $slug = "{$slug}-".($count + 1);
            }

            $clinic = Clinic::create([
                'name' => $data['name'],
                'slug' => $slug,
                'email' => $data['email'] ?? $owner->email,
                'phone' => $data['phone'] ?? $owner->phone,
                'address' => $data['address'] ?? null,
                'city' => $data['city'] ?? 'Dhaka',
                'timezone' => $data['timezone'] ?? 'Asia/Dhaka',
                'currency' => $data['currency'] ?? 'BDT',
                'status' => 'active',
            ]);

            // Add owner as Clinic Admin
            ClinicMembership::create([
                'clinic_id' => $clinic->id,
                'user_id' => $owner->id,
                'role' => ClinicRole::Admin->value,
                'status' => 'active',
            ]);

            // Create Staff Profile for owner
            StaffProfile::create([
                'clinic_id' => $clinic->id,
                'user_id' => $owner->id,
                'title' => 'Director',
            ]);

            // Create default location
            ClinicLocation::create([
                'clinic_id' => $clinic->id,
                'name' => 'Main Branch',
                'address' => $clinic->address ?? 'Main Clinic Building',
                'phone' => $clinic->phone,
                'is_active' => true,
            ]);

            // Create or resolve default subscription plan
            $selectedPlan = $plan ?? SubscriptionPlan::firstOrCreate(
                ['slug' => 'starter-clinic'],
                [
                    'name' => 'Starter Clinic Plan',
                    'price' => 3500.00,
                    'interval' => 'monthly',
                    'max_doctors' => 5,
                    'max_patients' => 500,
                    'is_active' => true,
                ]
            );

            // Assign trial subscription
            Subscription::create([
                'clinic_id' => $clinic->id,
                'subscription_plan_id' => $selectedPlan->id,
                'status' => SubscriptionStatus::Trial->value,
                'trial_ends_at' => now()->addDays(14),
                'current_period_starts_at' => now(),
                'current_period_ends_at' => now()->addDays(14),
            ]);

            // Seed default services
            Service::create([
                'clinic_id' => $clinic->id,
                'name' => 'General Physician Consultation',
                'code' => 'GP-01',
                'description' => 'Standard outpatient general health consultation',
                'price' => 600.00,
                'duration_minutes' => 20,
                'is_active' => true,
            ]);

            Service::create([
                'clinic_id' => $clinic->id,
                'name' => 'Specialist Follow-up Consultation',
                'code' => 'SPEC-01',
                'description' => 'Detailed specialist review and prescription',
                'price' => 1000.00,
                'duration_minutes' => 30,
                'is_active' => true,
            ]);

            // Seed default commission rules
            CommissionRule::create([
                'clinic_id' => $clinic->id,
                'staff_role' => 'nurse',
                'activity_type' => 'nurse_follow_up',
                'calculation_type' => 'fixed',
                'amount' => 50.00,
                'is_approval_required' => true,
                'is_active' => true,
            ]);

            CommissionRule::create([
                'clinic_id' => $clinic->id,
                'staff_role' => 'doctor',
                'activity_type' => 'doctor_consultation',
                'calculation_type' => 'fixed',
                'amount' => 200.00,
                'is_approval_required' => true,
                'is_active' => true,
            ]);

            AuditService::log('clinic.created', $clinic, null, [
                'owner_id' => $owner->id,
                'slug' => $slug,
            ], $clinic->id);

            return $clinic;
        });
    }
}
