<?php

namespace App\Models;

use App\Enums\CommissionStatus;
use App\Models\Concerns\BelongsToClinic;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class CommissionLedgerEntry extends Model
{
    use BelongsToClinic, HasFactory;

    protected $fillable = [
        'clinic_id',
        'staff_user_id',
        'activity_type',
        'source_type',
        'source_id',
        'commission_rule_id',
        'calculation_basis',
        'rate_applied',
        'amount',
        'currency',
        'status',
        'idempotency_key',
        'approved_by_user_id',
        'approved_at',
        'reversal_reason',
    ];

    protected function casts(): array
    {
        return [
            'calculation_basis' => 'decimal:2',
            'rate_applied' => 'decimal:2',
            'amount' => 'decimal:2',
            'status' => CommissionStatus::class,
            'approved_at' => 'datetime',
        ];
    }

    public function staffUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'staff_user_id');
    }

    public function source(): MorphTo
    {
        return $this->morphTo();
    }

    public function rule(): BelongsTo
    {
        return $this->belongsTo(CommissionRule::class, 'commission_rule_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_user_id');
    }

    public function payoutItem(): HasOne
    {
        return $this->hasOne(CommissionPayoutItem::class);
    }
}
