<?php

namespace App\Models;

use App\Enums\FollowUpStatus;
use App\Models\Concerns\BelongsToClinic;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FollowUpPlan extends Model
{
    use BelongsToClinic, HasFactory;

    protected $fillable = [
        'clinic_id',
        'patient_id',
        'assigned_nurse_id',
        'doctor_id',
        'due_date',
        'frequency',
        'priority',
        'instructions',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'status' => FollowUpStatus::class,
        ];
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function assignedNurse(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_nurse_id');
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function calls(): HasMany
    {
        return $this->hasMany(PatientCall::class);
    }
}
