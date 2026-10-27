<?php

namespace App\Models;

use App\Enums\ClinicRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'is_super_admin',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_super_admin' => 'boolean',
        ];
    }

    public function clinicMemberships(): HasMany
    {
        return $this->hasMany(ClinicMembership::class);
    }

    public function clinics(): BelongsToMany
    {
        return $this->belongsToMany(Clinic::class, 'clinic_memberships')
            ->withPivot(['role', 'permissions', 'status'])
            ->withTimestamps();
    }

    public function staffProfiles(): HasMany
    {
        return $this->hasMany(StaffProfile::class);
    }

    public function patientProfile(): HasOne
    {
        return $this->hasOne(Patient::class);
    }

    public function getMembershipForClinic(Clinic|int $clinic): ?ClinicMembership
    {
        $clinicId = $clinic instanceof Clinic ? $clinic->id : $clinic;

        return $this->clinicMemberships()->where('clinic_id', $clinicId)->first();
    }

    public function roleInClinic(Clinic|int $clinic): ?ClinicRole
    {
        $membership = $this->getMembershipForClinic($clinic);

        return $membership ? ClinicRole::tryFrom($membership->role) : null;
    }

    public function hasRoleInClinic(Clinic|int $clinic, ClinicRole|string $role): bool
    {
        if ($this->is_super_admin) {
            return true;
        }

        $expectedRole = $role instanceof ClinicRole ? $role->value : $role;
        $membership = $this->getMembershipForClinic($clinic);

        return $membership && $membership->role === $expectedRole && $membership->status === 'active';
    }
}
