<?php

namespace App\Models;

use App\Enums\CallOutcome;
use App\Models\Concerns\BelongsToClinic;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PatientCall extends Model
{
    use BelongsToClinic, HasFactory;

    protected $fillable = [
        'clinic_id',
        'patient_id',
        'nurse_id',
        'follow_up_plan_id',
        'call_date',
        'duration_seconds',
        'reached',
        'outcome',
        'notes',
        'next_follow_up_date',
    ];

    protected function casts(): array
    {
        return [
            'call_date' => 'datetime',
            'duration_seconds' => 'integer',
            'reached' => 'boolean',
            'outcome' => CallOutcome::class,
            'next_follow_up_date' => 'date',
        ];
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function nurse(): BelongsTo
    {
        return $this->belongsTo(User::class, 'nurse_id');
    }

    public function followUpPlan(): BelongsTo
    {
        return $this->belongsTo(FollowUpPlan::class);
    }

    public function healthObservations(): HasMany
    {
        return $this->hasMany(HealthObservation::class);
    }

    public function healthReport(): HasOne
    {
        return $this->hasOne(HealthReport::class);
    }
}
