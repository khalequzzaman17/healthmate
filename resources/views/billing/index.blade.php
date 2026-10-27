@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Billing & Patient Invoices</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Manage patient accounts, service invoicing, and payment reconciliation</p>
        </div>
        <div>
            <a href="{{ route('billing.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-[#1E56E3] hover:bg-blue-600 text-white text-xs font-semibold shadow-md shadow-blue-600/25 transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                <span>Create New Invoice</span>
            </a>
        </div>
    </div>

    <!-- Financial KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-2xs flex items-center justify-between transition-colors">
            <div>
                <span class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Invoiced</span>
                <div class="text-2xl font-bold text-slate-900 dark:text-white mt-1">BDT {{ number_format($totalBilled, 2) }}</div>
                <div class="text-[11px] text-slate-400 dark:text-slate-500 mt-1">Gross billed amount</div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-300 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-2xs flex items-center justify-between transition-colors">
            <div>
                <span class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Collected Revenue</span>
                <div class="text-2xl font-bold text-emerald-600 dark:text-emerald-400 mt-1">BDT {{ number_format($totalCollected, 2) }}</div>
                <div class="text-[11px] text-emerald-600 dark:text-emerald-400 font-medium mt-1">Cash, bKash, cards received</div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-2xs flex items-center justify-between transition-colors">
            <div>
                <span class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Outstanding Due</span>
                <div class="text-2xl font-bold text-rose-600 dark:text-rose-400 mt-1">BDT {{ number_format($totalOutstanding, 2) }}</div>
                <div class="text-[11px] text-slate-400 dark:text-slate-500 mt-1">Pending patient collection</div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
        </div>
    </div>

    <!-- Invoices Table -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-2xs overflow-hidden transition-colors">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50/75 dark:bg-slate-800/40 text-[10px] text-slate-400 uppercase font-semibold tracking-wider border-b border-slate-100 dark:border-slate-800">
                    <tr>
                        <th class="px-5 py-3 font-medium">Invoice #</th>
                        <th class="px-4 py-3 font-medium">Patient</th>
                        <th class="px-4 py-3 font-medium">Date</th>
                        <th class="px-4 py-3 font-medium">Total Amount</th>
                        <th class="px-4 py-3 font-medium">Paid Amount</th>
                        <th class="px-4 py-3 font-medium">Status</th>
                        <th class="px-4 py-3 font-medium text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60 text-slate-600 dark:text-slate-300">
                    @forelse($invoices as $inv)
                        <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition">
                            <td class="px-5 py-3.5 font-mono font-bold text-blue-600 dark:text-blue-400">{{ $inv->invoice_number }}</td>
                            <td class="px-4 py-3.5">
                                <a href="{{ route('patients.show', $inv->patient) }}" class="font-bold text-slate-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 transition">
                                    {{ $inv->patient->fullName }}
                                </a>
                                <div class="text-[11px] text-slate-400 dark:text-slate-500">{{ $inv->patient->phone }}</div>
                            </td>
                            <td class="px-4 py-3.5 text-slate-500 dark:text-slate-400">{{ $inv->created_at->format('M d, Y') }}</td>
                            <td class="px-4 py-3.5 font-bold text-slate-900 dark:text-white">BDT {{ number_format($inv->total_amount, 2) }}</td>
                            <td class="px-4 py-3.5 font-semibold text-emerald-600 dark:text-emerald-400">BDT {{ number_format($inv->paid_amount, 2) }}</td>
                            <td class="px-4 py-3.5">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold
                                    {{ $inv->status->value === 'paid' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400 border border-emerald-200/60 dark:border-emerald-800/60' : '' }}
                                    {{ $inv->status->value === 'unpaid' ? 'bg-rose-50 text-rose-700 dark:bg-rose-950/60 dark:text-rose-400 border border-rose-200/60 dark:border-rose-800/60' : '' }}
                                    {{ $inv->status->value === 'partially_paid' ? 'bg-amber-50 text-amber-700 dark:bg-amber-950/60 dark:text-amber-400 border border-amber-200/60 dark:border-amber-800/60' : '' }}">
                                    {{ $inv->status->label() }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-right">
                                <a href="{{ route('billing.show', $inv) }}" class="px-2.5 py-1.5 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-semibold text-[11px] transition">
                                    View & Pay
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-8 text-center text-slate-400 dark:text-slate-500">No invoices recorded yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-5 py-3 border-t border-slate-100 dark:border-slate-800">
            {{ $invoices->links() }}
        </div>
    </div>
</div>
@endsection
