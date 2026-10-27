<?php

namespace App\Http\Controllers;

use App\Enums\CommissionStatus;
use App\Models\CommissionLedgerEntry;
use App\Models\CommissionPayout;
use App\Models\User;
use App\Services\CommissionService;
use App\Services\TenantContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CommissionController extends Controller
{
    public function __construct(
        protected CommissionService $commissionService
    ) {}

    public function index(Request $request): View
    {
        $status = $request->input('status');
        $staffId = $request->input('staff_id');

        $entries = CommissionLedgerEntry::query()
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($staffId, fn ($q) => $q->where('staff_user_id', $staffId))
            ->with(['staffUser', 'rule', 'approvedBy'])
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $totalPending = CommissionLedgerEntry::where('status', CommissionStatus::Pending->value)->sum('amount');
        $totalApproved = CommissionLedgerEntry::where('status', CommissionStatus::Approved->value)->sum('amount');
        $totalPaid = CommissionLedgerEntry::where('status', CommissionStatus::Paid->value)->sum('amount');

        $clinic = TenantContext::getClinic();
        $staffMembers = User::whereHas('clinicMemberships', function ($q) use ($clinic) {
            $q->where('clinic_id', $clinic->id)->whereIn('role', ['doctor', 'nurse'])->where('status', 'active');
        })->get();

        return view('commissions.index', compact(
            'entries',
            'status',
            'staffId',
            'totalPending',
            'totalApproved',
            'totalPaid',
            'staffMembers'
        ));
    }

    public function approve(CommissionLedgerEntry $entry): RedirectResponse
    {
        $this->commissionService->approveEntry($entry, Auth::user());

        return back()->with('success', 'Commission entry approved successfully.');
    }

    public function payouts(): View
    {
        $payouts = CommissionPayout::with(['staffUser', 'preparedBy', 'approvedBy', 'items'])
            ->latest()
            ->paginate(15);

        $clinic = TenantContext::getClinic();
        $eligibleStaff = User::whereHas('clinicMemberships', function ($q) use ($clinic) {
            $q->where('clinic_id', $clinic->id)->whereIn('role', ['doctor', 'nurse'])->where('status', 'active');
        })
            ->whereHas('commissionLedgerEntries', function ($q) use ($clinic) {
                $q->where('clinic_id', $clinic->id)
                    ->whereIn('status', [CommissionStatus::Approved->value, CommissionStatus::Payable->value])
                    ->whereDoesntHave('payoutItem');
            })
            ->get();

        return view('commissions.payouts', compact('payouts', 'eligibleStaff'));
    }

    public function preparePayout(Request $request): RedirectResponse
    {
        $clinic = TenantContext::getClinic();

        $validated = $request->validate([
            'staff_user_id' => ['required', 'exists:users,id'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $staffUser = User::findOrFail($validated['staff_user_id']);

        $payout = $this->commissionService->preparePayout(
            clinic: $clinic,
            staffUser: $staffUser,
            preparer: Auth::user(),
            notes: $validated['notes'] ?? null
        );

        return redirect()->route('commissions.payouts.show', $payout)
            ->with('success', "Payout {$payout->payout_number} prepared for {$staffUser->name}. Total: BDT {$payout->total_amount}");
    }

    public function showPayout(CommissionPayout $payout): View
    {
        $payout->load(['staffUser', 'preparedBy', 'approvedBy', 'items.ledgerEntry']);

        return view('commissions.payout_show', compact('payout'));
    }

    public function approvePayout(CommissionPayout $payout): RedirectResponse
    {
        $this->commissionService->approvePayout($payout, Auth::user());

        return back()->with('success', "Payout {$payout->payout_number} approved.");
    }

    public function processPayout(Request $request, CommissionPayout $payout): RedirectResponse
    {
        $validated = $request->validate([
            'payment_method' => ['required', 'string'],
            'payment_reference' => ['required', 'string', 'max:100'],
        ]);

        $this->commissionService->processPayout(
            payout: $payout,
            paymentMethod: $validated['payment_method'],
            paymentReference: $validated['payment_reference']
        );

        return back()->with('success', "Payout {$payout->payout_number} processed and marked as Paid.");
    }
}
