@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Staff Commission Payouts</h1>
            <p class="text-xs text-slate-500 mt-0.5">Prepare, approve, and process payouts for eligible staff commission earnings</p>
        </div>
        <a href="{{ route('commissions.index') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900">&larr; Back to ledger</a>
    </div>

    <!-- Prepare Payout Card -->
    @if(in_array($userRole ?? '', ['admin', 'super_admin']))
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6">
            <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-3">Prepare New Payout</h2>
            <form action="{{ route('commissions.payouts.store') }}" method="POST" class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Select Staff Member with Approved Earnings</label>
                    <select name="staff_user_id" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs">
                        <option value="">-- Choose Staff --</option>
                        @foreach($eligibleStaff as $staff)
                            <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Payout Notes</label>
                    <input type="text" name="notes" placeholder="e.g. Monthly follow-up commission disbursement"
                           class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs">
                </div>

                <div>
                    <button type="submit" class="w-full py-2 px-4 rounded-xl bg-sky-600 hover:bg-sky-500 text-white font-semibold text-xs shadow-xs transition">
                        Prepare Payout Batch
                    </button>
                </div>
            </form>
        </div>
    @endif

    <!-- Payouts History Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100">
            <h2 class="text-sm font-bold text-slate-900">Payout Records & Batches</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-[10px] text-slate-500 uppercase font-semibold border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3">Payout #</th>
                        <th class="px-4 py-3">Staff Member</th>
                        <th class="px-4 py-3">Amount</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Prepared By</th>
                        <th class="px-4 py-3">Payment Info</th>
                        <th class="px-4 py-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($payouts as $payout)
                        <tr class="hover:bg-slate-50/50">
                            <td class="px-5 py-3.5 font-mono font-bold text-sky-700">{{ $payout->payout_number }}</td>
                            <td class="px-4 py-3.5 font-bold text-slate-900">{{ $payout->staffUser->name }}</td>
                            <td class="px-4 py-3.5 font-bold text-slate-900">BDT {{ number_format($payout->total_amount, 2) }}</td>
                            <td class="px-4 py-3.5">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold
                                    {{ $payout->status->value === 'paid' ? 'bg-emerald-50 text-emerald-700' : '' }}
                                    {{ $payout->status->value === 'approved' ? 'bg-sky-50 text-sky-700' : '' }}
                                    {{ $payout->status->value === 'prepared' ? 'bg-amber-50 text-amber-700' : '' }}">
                                    {{ $payout->status->label() }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5">{{ $payout->preparedBy->name }}</td>
                            <td class="px-4 py-3.5">
                                @if($payout->status->value === 'paid')
                                    <div class="font-medium text-slate-800">{{ $payout->payment_method }} &bull; Ref: {{ $payout->payment_reference }}</div>
                                    <div class="text-[10px] text-slate-400">{{ $payout->processed_at?->format('M d, Y') }}</div>
                                @else
                                    <span class="text-slate-400 italic">Not yet processed</span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 text-right">
                                <a href="{{ route('commissions.payouts.show', $payout) }}" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-[11px]">
                                    View Payout
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-8 text-center text-slate-400">No payout batches prepared yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-5 py-3 border-t border-slate-100">
            {{ $payouts->links() }}
        </div>
    </div>
</div>
@endsection
