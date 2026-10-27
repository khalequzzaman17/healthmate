<?php

namespace App\Models;

use App\Enums\HealthReportStatus;
use App\Models\Concerns\BelongsToClinic;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class HealthReport extends Model
{
    use BelongsToClinic, HasFactory;

    protected $fillable = [
        'clinic_id',
        'patient_id',
        'nurse_id',
        'patient_call_id',
        'title',
        'summary',
        'status',
        'submitted_at',
        'reviewed_at',
        'reviewed_by_doctor_id',
        'review_notes',
    ];

    protected function casts(): array
    {
        return [
            'status' => HealthReportStatus::class,
            'submitted_at' => 'datetime',
            'reviewed_at' => 'datetime',
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

    public function reviewerDoctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by_doctor_id');
    }

    public function call(): BelongsTo
    {
        return $this->belongsTo(PatientCall::class, 'patient_call_id');
    }

    public function consultation(): HasOne
    {
        return $this->hasOne(Consultation::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(MedicalAttachment::class);
    }
}
