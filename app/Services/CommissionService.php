<?php

namespace App\Services;

use App\Enums\CommissionStatus;
use App\Enums\PayoutStatus;
use App\Models\Clinic;
use App\Models\CommissionLedgerEntry;
use App\Models\CommissionPayout;
use App\Models\CommissionPayoutItem;
use App\Models\CommissionRule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CommissionService
{
    /**
     * Record a commission earning event for an eligible clinical activity.
     */
    public function recordEarning(
        Clinic $clinic,
        User $staffUser,
        string $staffRole,
        string $activityType,
        Model $source,
        float $calculationBasis = 0.0,
        ?string $customIdempotencyKey = null
    ): ?CommissionLedgerEntry {
        // Find active rule matching clinic, role, and activity
        $rule = CommissionRule::where('clinic_id', $clinic->id)
            ->where('staff_role', $staffRole)
            ->where('activity_type', $activityType)
            ->where('is_active', true)
            ->where(function ($query) {
                $today = Carbon::today();
                $query->whereNull('effective_from')->orWhere('effective_from', '<=', $today);
            })
            ->where(function ($query) {
                $today = Carbon::today();
                $query->whereNull('effective_to')->orWhere('effective_to', '>=', $today);
            })
            ->first();

        if (! $rule) {
            return null;
        }

        $idempotencyKey = $customIdempotencyKey ?? sprintf(
            'clinic_%d_role_%s_act_%s_src_%s_%d',
            $clinic->id,
            $staffRole,
            $activityType,
            class_basename($source),
            $source->getKey()
        );

        // Check if entry already exists (idempotency check)
        $existing = CommissionLedgerEntry::where('idempotency_key', $idempotencyKey)->first();
        if ($existing) {
            return $existing;
        }

        // Calculate commission amount
        $amount = 0.0;
        if ($rule->calculation_type === 'percentage') {
            $amount = round(($calculationBasis * (float) $rule->amount) / 100, 2);
        } else {
            $amount = (float) $rule->amount;
        }

        $initialStatus = $rule->is_approval_required
            ? CommissionStatus::Pending
            : CommissionStatus::Payable;

        return DB::transaction(function () use (
            $clinic,
            $staffUser,
            $activityType,
            $source,
            $rule,
            $calculationBasis,
            $amount,
            $initialStatus,
            $idempotencyKey
        ) {
            $entry = CommissionLedgerEntry::create([
                'clinic_id' => $clinic->id,
                'staff_user_id' => $staffUser->id,
                'activity_type' => $activityType,
                'source_type' => get_class($source),
                'source_id' => $source->getKey(),
                'commission_rule_id' => $rule->id,
                'calculation_basis' => $calculationBasis,
                'rate_applied' => $rule->amount,
                'amount' => $amount,
                'currency' => $clinic->currency ?? 'BDT',
                'status' => $initialStatus,
                'idempotency_key' => $idempotencyKey,
            ]);

            AuditService::log('commission.earned', $entry, null, [
                'staff_user_id' => $staffUser->id,
                'amount' => $amount,
                'activity' => $activityType,
            ], $clinic->id);

            return $entry;
        });
    }

    /**
     * Approve a pending commission entry.
     */
    public function approveEntry(CommissionLedgerEntry $entry, User $approver): CommissionLedgerEntry
    {
        if ($entry->status !== CommissionStatus::Pending) {
            throw ValidationException::withMessages([
                'entry' => 'Only pending commission entries can be approved.',
            ]);
        }

        $entry->update([
            'status' => CommissionStatus::Approved,
            'approved_by_user_id' => $approver->id,
            'approved_at' => now(),
        ]);

        AuditService::log('commission.approved', $entry, null, [
            'approved_by' => $approver->id,
        ], $entry->clinic_id);

        return $entry;
    }

    /**
     * Prepare a payout for a staff member from approved/payable commission entries.
     *
     * @param  array<int>|null  $entryIds
     */
    public function preparePayout(
        Clinic $clinic,
        User $staffUser,
        User $preparer,
        ?array $entryIds = null,
        ?string $notes = null
    ): CommissionPayout {
        return DB::transaction(function () use ($clinic, $staffUser, $preparer, $entryIds, $notes) {
            $query = CommissionLedgerEntry::where('clinic_id', $clinic->id)
                ->where('staff_user_id', $staffUser->id)
                ->whereIn('status', [CommissionStatus::Approved->value, CommissionStatus::Payable->value])
                ->whereDoesntHave('payoutItem')
                ->lockForUpdate();

            if (! empty($entryIds)) {
                $query->whereIn('id', $entryIds);
            }

            $entries = $query->get();

            if ($entries->isEmpty()) {
                throw ValidationException::withMessages([
                    'entries' => 'No approved or payable commission entries found for this staff member.',
                ]);
            }

            $totalAmount = $entries->sum('amount');
            $payoutNumber = 'PO-'.date('Ymd').'-'.strtoupper(bin2hex(random_bytes(3)));

            $payout = CommissionPayout::create([
                'clinic_id' => $clinic->id,
                'payout_number' => $payoutNumber,
                'staff_user_id' => $staffUser->id,
                'total_amount' => $totalAmount,
                'currency' => $clinic->currency ?? 'BDT',
                'status' => PayoutStatus::Prepared,
                'prepared_by_user_id' => $preparer->id,
                'notes' => $notes,
            ]);

            foreach ($entries as $entry) {
                CommissionPayoutItem::create([
                    'commission_payout_id' => $payout->id,
                    'commission_ledger_entry_id' => $entry->id,
                    'amount' => $entry->amount,
                ]);
            }

            AuditService::log('commission.payout_prepared', $payout, null, [
                'total_amount' => $totalAmount,
                'entries_count' => $entries->count(),
            ], $clinic->id);

            return $payout->load('items.ledgerEntry');
        });
    }

    /**
     * Approve a prepared payout.
     */
    public function approvePayout(CommissionPayout $payout, User $approver): CommissionPayout
    {
        if ($payout->status !== PayoutStatus::Prepared) {
            throw ValidationException::withMessages([
                'payout' => 'Only prepared payouts can be approved.',
            ]);
        }

        $payout->update([
            'status' => PayoutStatus::Approved,
            'approved_by_user_id' => $approver->id,
        ]);

        AuditService::log('commission.payout_approved', $payout, null, [
            'approved_by' => $approver->id,
        ], $payout->clinic_id);

        return $payout;
    }

    /**
     * Process an approved payout and mark entries as paid.
     */
    public function processPayout(
        CommissionPayout $payout,
        string $paymentMethod,
        ?string $paymentReference = null
    ): CommissionPayout {
        if (! in_array($payout->status, [PayoutStatus::Approved, PayoutStatus::Prepared])) {
            throw ValidationException::withMessages([
                'payout' => 'Only approved or prepared payouts can be processed.',
            ]);
        }

        return DB::transaction(function () use ($payout, $paymentMethod, $paymentReference) {
            $payout->update([
                'status' => PayoutStatus::Paid,
                'payment_method' => $paymentMethod,
                'payment_reference' => $paymentReference,
                'processed_at' => now(),
            ]);

            // Mark all underlying ledger entries as Paid
            foreach ($payout->items as $item) {
                $item->ledgerEntry->update([
                    'status' => CommissionStatus::Paid,
                ]);
            }

            AuditService::log('commission.payout_processed', $payout, null, [
                'payment_method' => $paymentMethod,
                'payment_reference' => $paymentReference,
            ], $payout->clinic_id);

            return $payout;
        });
    }
}
