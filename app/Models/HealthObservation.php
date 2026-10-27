<?php

namespace App\Models;

use App\Models\Concerns\BelongsToClinic;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HealthObservation extends Model
{
    use BelongsToClinic, HasFactory;

    protected $fillable = [
        'clinic_id',
        'patient_id',
        'recorded_by_user_id',
        'patient_call_id',
        'systolic_bp',
        'diastolic_bp',
        'blood_glucose',
        'glucose_type',
        'temperature_f',
        'weight_kg',
        'pulse_rate',
        'symptoms',
        'medication_adherence',
        'patient_concerns',
        'escalation_needed',
    ];

    protected function casts(): array
    {
        return [
            'systolic_bp' => 'integer',
            'diastolic_bp' => 'integer',
            'blood_glucose' => 'decimal:2',
            'temperature_f' => 'decimal:1',
            'weight_kg' => 'decimal:2',
            'pulse_rate' => 'integer',
            'escalation_needed' => 'boolean',
        ];
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by_user_id');
    }

    public function call(): BelongsTo
    {
        return $this->belongsTo(PatientCall::class, 'patient_call_id');
    }
}
