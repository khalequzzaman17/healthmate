<?php

use App\Enums\CommissionStatus;
use App\Enums\PayoutStatus;
use App\Models\Clinic;
use App\Models\CommissionLedgerEntry;
use App\Models\CommissionPayoutItem;
use App\Models\User;
use App\Services\CommissionService;
use App\Services\TenantContext;
use Illuminate\Database\QueryException;

beforeEach(function () {
    $this->seed();
});

test('commission service calculates, approves, prepares and processes payouts securely', function () {
    $clinic = Clinic::where('slug', 'carepoint')->firstOrFail();
    $nurse = User::where('email', 'nurse.poly@carepoint.test')->firstOrFail();
    $admin = User::where('email', 'admin@carepoint.test')->firstOrFail();

    TenantContext::setClinic($clinic);

    $commissionService = app(CommissionService::class);

    // Existing entry from seeder
    $entry = CommissionLedgerEntry::where('staff_user_id', $nurse->id)->firstOrFail();
    expect($entry->status)->toBe(CommissionStatus::Pending);

    // 1. Admin approves the entry
    $approvedEntry = $commissionService->approveEntry($entry, $admin);
    expect($approvedEntry->status)->toBe(CommissionStatus::Approved)
        ->and($approvedEntry->approved_by_user_id)->toBe($admin->id);

    // 2. Prepare payout
    $payout = $commissionService->preparePayout(
        clinic: $clinic,
        staffUser: $nurse,
        preparer: $admin,
        entryIds: [$approvedEntry->id],
        notes: 'Monthly nurse follow-up incentives'
    );

    expect($payout->status)->toBe(PayoutStatus::Prepared)
        ->and((float) $payout->total_amount)->toBe((float) $approvedEntry->amount)
        ->and($payout->items)->toHaveCount(1);

    // 3. Database uniqueness prevents this entry from being added to another payout
    expect(function () use ($payout, $approvedEntry) {
        CommissionPayoutItem::create([
            'commission_payout_id' => $payout->id,
            'commission_ledger_entry_id' => $approvedEntry->id,
            'amount' => $approvedEntry->amount,
        ]);
    })->toThrow(QueryException::class);

    // 4. Approve payout
    $commissionService->approvePayout($payout, $admin);
    expect($payout->fresh()->status)->toBe(PayoutStatus::Approved);

    // 5. Process payout
    $processed = $commissionService->processPayout(
        payout: $payout,
        paymentMethod: 'bKash',
        paymentReference: 'TRX-BKASH-77192'
    );

    expect($processed->status)->toBe(PayoutStatus::Paid)
        ->and($approvedEntry->fresh()->status)->toBe(CommissionStatus::Paid);

    TenantContext::clear();
});
