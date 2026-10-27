@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Payout {{ $payout->payout_number }}</h1>
            <p class="text-xs text-slate-500 mt-0.5">Staff: <strong>{{ $payout->staffUser->name }}</strong> &bull; Total Amount: <strong>BDT {{ number_format($payout->total_amount, 2) }}</strong></p>
        </div>
        <a href="{{ route('commissions.payouts') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900">&larr; Back to payouts</a>
    </div>

    <!-- Payout Details Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 space-y-4">
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 pb-4 border-b border-slate-100">
            <div>
                <span class="text-[10px] uppercase font-bold text-slate-400 block">Status</span>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold mt-1
                    {{ $payout->status->value === 'paid' ? 'bg-emerald-50 text-emerald-700' : '' }}
                    {{ $payout->status->value === 'approved' ? 'bg-sky-50 text-sky-700' : '' }}
                    {{ $payout->status->value === 'prepared' ? 'bg-amber-50 text-amber-700' : '' }}">
                    {{ $payout->status->label() }}
                </span>
            </div>

            <div>
                <span class="text-[10px] uppercase font-bold text-slate-400 block">Total Payout</span>
                <span class="text-base font-bold text-slate-900 block mt-1">BDT {{ number_format($payout->total_amount, 2) }}</span>
            </div>

            <div>
                <span class="text-[10px] uppercase font-bold text-slate-400 block">Prepared By</span>
                <span class="text-xs font-medium text-slate-800 block mt-1">{{ $payout->preparedBy->name }}</span>
            </div>

            <div>
                <span class="text-[10px] uppercase font-bold text-slate-400 block">Approved By</span>
                <span class="text-xs font-medium text-slate-800 block mt-1">{{ $payout->approvedBy?->name ?? 'Pending Approval' }}</span>
            </div>
        </div>

        @if($payout->status->value === 'paid')
            <div class="p-3.5 rounded-xl bg-emerald-50 border border-emerald-100 text-xs text-emerald-800">
                <strong>Disbursement Completed:</strong> Paid via <strong>{{ $payout->payment_method }}</strong> with transaction reference <strong>{{ $payout->payment_reference }}</strong> on {{ $payout->processed_at?->format('F d, Y h:i A') }}.
            </div>
        @endif

        <!-- Underlying Commission Items Table -->
        <div>
            <h2 class="text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Included Commission Activities ({{ $payout->items->count() }})</h2>
            <div class="overflow-x-auto rounded-xl border border-slate-100">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-[10px] text-slate-400 uppercase font-semibold">
                        <tr>
                            <th class="px-4 py-2.5">Date</th>
                            <th class="px-4 py-2.5">Activity</th>
                            <th class="px-4 py-2.5">Idempotency Key</th>
                            <th class="px-4 py-2.5 text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($payout->items as $item)
                            <tr>
                                <td class="px-4 py-2.5">{{ $item->ledgerEntry->created_at->format('M d, Y') }}</td>
                                <td class="px-4 py-2.5 font-medium text-slate-900">{{ str_replace('_', ' ', ucfirst($item->ledgerEntry->activity_type)) }}</td>
                                <td class="px-4 py-2.5 font-mono text-[10px] text-slate-400">{{ $item->ledgerEntry->idempotency_key }}</td>
                                <td class="px-4 py-2.5 text-right font-bold text-slate-900">BDT {{ number_format($item->amount, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Workflow Actions -->
        @if(in_array($userRole ?? '', ['admin', 'super_admin']))
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                @if($payout->status->value === 'prepared')
                    <form action="{{ route('commissions.payouts.approve', $payout) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-4 py-2 rounded-xl bg-sky-600 hover:bg-sky-500 text-white font-semibold text-xs shadow-xs">
                            Approve Payout
                        </button>
                    </form>
                @endif

                @if(in_array($payout->status->value, ['prepared', 'approved']))
                    <div x-data="{ openProcess: false }">
                        <button type="button" @click="openProcess = true" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-xs shadow-xs">
                            Process Payout & Mark Paid
                        </button>

                        <!-- Process Modal -->
                        <div x-show="openProcess" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60" style="display: none;">
                            <div class="bg-white rounded-2xl max-w-md w-full p-6 text-left shadow-2xl space-y-4" @click.away="openProcess = false">
                                <h3 class="text-base font-bold text-slate-900">Process Payout & Record Payment</h3>
                                <p class="text-xs text-slate-500">Provide disbursement transaction reference for audit reconciliation.</p>

                                <form action="{{ route('commissions.payouts.process', $payout) }}" method="POST" class="space-y-4">
                                    @csrf
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">Disbursement Method *</label>
                                        <select name="payment_method" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs">
                                            <option value="bKash">bKash (MFS)</option>
                                            <option value="Nagad">Nagad (MFS)</option>
                                            <option value="Bank Transfer">Bank Transfer (EFT / BEFTN)</option>
                                            <option value="Cash">Cash in Hand</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">Transaction Reference / Voucher # *</label>
                                        <input type="text" name="payment_reference" required placeholder="e.g. TRX-BKASH-881920 or Voucher #401"
                                               class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs">
                                    </div>

                                    <div class="flex items-center justify-end gap-2 pt-2">
                                        <button type="button" @click="openProcess = false" class="px-3 py-1.5 rounded-lg border border-slate-200 text-xs font-semibold text-slate-700">Cancel</button>
                                        <button type="submit" class="px-4 py-1.5 rounded-lg bg-emerald-600 text-white text-xs font-semibold shadow-xs">Confirm Disbursement</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection
