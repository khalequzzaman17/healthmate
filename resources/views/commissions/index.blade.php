@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Commission Ledger & Earnings</h1>
            <p class="text-xs text-slate-500 mt-0.5">Auditable, rule-versioned clinical activity earnings for Doctors and Nurses</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('commissions.payouts') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-sky-600 hover:bg-sky-500 text-white text-xs font-semibold shadow-sm transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                <span>Manage Staff Payouts</span>
            </a>
        </div>
    </div>

    <!-- Summary Metrics -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Pending Review</span>
                <div class="text-2xl font-bold text-amber-600 mt-1">BDT {{ number_format($totalPending, 2) }}</div>
                <div class="text-[11px] text-slate-400 mt-1">Awaiting admin approval</div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Approved / Payable</span>
                <div class="text-2xl font-bold text-sky-600 mt-1">BDT {{ number_format($totalApproved, 2) }}</div>
                <div class="text-[11px] text-slate-400 mt-1">Ready for payout preparation</div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Disbursed / Paid</span>
                <div class="text-2xl font-bold text-emerald-600 mt-1">BDT {{ number_format($totalPaid, 2) }}</div>
                <div class="text-[11px] text-slate-400 mt-1">Successfully paid out</div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
        <form method="GET" action="{{ route('commissions.index') }}" class="flex flex-wrap items-center gap-3">
            <div>
                <select name="status" class="px-3 py-1.5 rounded-lg border border-slate-200 text-xs">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ $status === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="payable" {{ $status === 'payable' ? 'selected' : '' }}>Payable</option>
                    <option value="paid" {{ $status === 'paid' ? 'selected' : '' }}>Paid</option>
                </select>
            </div>

            <div>
                <select name="staff_id" class="px-3 py-1.5 rounded-lg border border-slate-200 text-xs">
                    <option value="">All Staff Members</option>
                    @foreach($staffMembers as $staff)
                        <option value="{{ $staff->id }}" {{ $staffId == $staff->id ? 'selected' : '' }}>
                            {{ $staff->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="px-4 py-1.5 rounded-lg bg-slate-900 text-white text-xs font-semibold">
                Filter
            </button>
            <a href="{{ route('commissions.index') }}" class="px-3 py-1.5 rounded-lg bg-slate-100 text-slate-700 text-xs font-semibold">
                Reset
            </a>
        </form>
    </div>

    <!-- Ledger Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-[10px] text-slate-500 uppercase font-semibold border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3">Date</th>
                        <th class="px-4 py-3">Staff User</th>
                        <th class="px-4 py-3">Activity Type</th>
                        <th class="px-4 py-3">Basis & Rate Applied</th>
                        <th class="px-4 py-3">Commission Amount</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($entries as $entry)
                        <tr class="hover:bg-slate-50/50">
                            <td class="px-5 py-3.5 font-medium text-slate-900">
                                {{ $entry->created_at->format('M d, Y h:i A') }}
                                <div class="text-[10px] text-slate-400 font-mono truncate max-w-xs">{{ $entry->idempotency_key }}</div>
                            </td>
                            <td class="px-4 py-3.5 font-bold text-slate-900">{{ $entry->staffUser->name }}</td>
                            <td class="px-4 py-3.5">
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-800">
                                    {{ str_replace('_', ' ', ucfirst($entry->activity_type)) }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5">
                                @if($entry->calculation_basis > 0)
                                    <div>Basis: BDT {{ number_format($entry->calculation_basis, 2) }}</div>
                                @endif
                                <div class="text-slate-500">Rate: {{ $entry->rate_applied }}</div>
                            </td>
                            <td class="px-4 py-3.5 font-bold text-slate-900">
                                BDT {{ number_format($entry->amount, 2) }}
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold
                                    {{ $entry->status->value === 'pending' ? 'bg-amber-50 text-amber-700' : '' }}
                                    {{ $entry->status->value === 'approved' ? 'bg-sky-50 text-sky-700' : '' }}
                                    {{ $entry->status->value === 'payable' ? 'bg-teal-50 text-teal-700' : '' }}
                                    {{ $entry->status->value === 'paid' ? 'bg-emerald-50 text-emerald-700' : '' }}">
                                    {{ $entry->status->label() }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-right">
                                @if($entry->status->value === 'pending' && in_array($userRole ?? '', ['admin', 'super_admin']))
                                    <form action="{{ route('commissions.approve', $entry) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="px-2.5 py-1 rounded-lg bg-sky-600 hover:bg-sky-500 text-white font-semibold text-[11px] shadow-xs">
                                            Approve
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-8 text-center text-slate-400">No commission entries found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-5 py-3 border-t border-slate-100">
            {{ $entries->links() }}
        </div>
    </div>
</div>
@endsection
