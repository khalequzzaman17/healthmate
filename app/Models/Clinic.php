<?php

namespace App\Models;

use App\Enums\SubscriptionStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Clinic extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'email',
        'phone',
        'address',
        'city',
        'timezone',
        'currency',
        'status',
        'logo_path',
    ];

    public function memberships(): HasMany
    {
        return $this->hasMany(ClinicMembership::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'clinic_memberships')
            ->withPivot(['role', 'permissions', 'status'])
            ->withTimestamps();
    }

    public function settings(): HasMany
    {
        return $this->hasMany(ClinicSetting::class);
    }

    public function locations(): HasMany
    {
        return $this->hasMany(ClinicLocation::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function activeSubscription(): HasOne
    {
        return $this->hasOne(Subscription::class)->latestOfMany();
    }

    public function patients(): HasMany
    {
        return $this->hasMany(Patient::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    public function followUpPlans(): HasMany
    {
        return $this->hasMany(FollowUpPlan::class);
    }

    public function patientCalls(): HasMany
    {
        return $this->hasMany(PatientCall::class);
    }

    public function healthReports(): HasMany
    {
        return $this->hasMany(HealthReport::class);
    }

    public function consultations(): HasMany
    {
        return $this->hasMany(Consultation::class);
    }

    public function commissionRules(): HasMany
    {
        return $this->hasMany(CommissionRule::class);
    }

    public function commissionLedgerEntries(): HasMany
    {
        return $this->hasMany(CommissionLedgerEntry::class);
    }

    public function commissionPayouts(): HasMany
    {
        return $this->hasMany(CommissionPayout::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function hasActiveSubscription(): bool
    {
        $subscription = $this->activeSubscription;

        if (! $subscription) {
            return false;
        }

        return in_array($subscription->status, [SubscriptionStatus::Trial->value, SubscriptionStatus::Active->value]);
    }
}
