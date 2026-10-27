@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Billing & Patient Invoices</h1>
            <p class="text-xs text-slate-500 mt-0.5">Manage patient accounts, service invoicing, and payment reconciliation</p>
        </div>
        <div>
            <a href="{{ route('billing.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-sky-600 hover:bg-sky-500 text-white text-xs font-semibold shadow-sm transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                <span>Create New Invoice</span>
            </a>
        </div>
    </div>

    <!-- Financial KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Invoiced</span>
                <div class="text-2xl font-bold text-slate-900 mt-1">BDT {{ number_format($totalBilled, 2) }}</div>
                <div class="text-[11px] text-slate-400 mt-1">Gross billed amount</div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-slate-50 text-slate-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Collected Revenue</span>
                <div class="text-2xl font-bold text-emerald-600 mt-1">BDT {{ number_format($totalCollected, 2) }}</div>
                <div class="text-[11px] text-emerald-600 font-medium mt-1">Cash, bKash, cards received</div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Outstanding Due</span>
                <div class="text-2xl font-bold text-rose-600 mt-1">BDT {{ number_format($totalOutstanding, 2) }}</div>
                <div class="text-[11px] text-slate-400 mt-1">Pending patient collection</div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
        </div>
    </div>

    <!-- Invoices Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-[10px] text-slate-500 uppercase font-semibold border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3">Invoice #</th>
                        <th class="px-4 py-3">Patient</th>
                        <th class="px-4 py-3">Date</th>
                        <th class="px-4 py-3">Total Amount</th>
                        <th class="px-4 py-3">Paid Amount</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($invoices as $inv)
                        <tr class="hover:bg-slate-50/50">
                            <td class="px-5 py-3.5 font-mono font-bold text-sky-700">{{ $inv->invoice_number }}</td>
                            <td class="px-4 py-3.5">
                                <a href="{{ route('patients.show', $inv->patient) }}" class="font-bold text-slate-900 hover:text-sky-600">
                                    {{ $inv->patient->fullName }}
                                </a>
                                <div class="text-[11px] text-slate-400">{{ $inv->patient->phone }}</div>
                            </td>
                            <td class="px-4 py-3.5">{{ $inv->created_at->format('M d, Y') }}</td>
                            <td class="px-4 py-3.5 font-bold text-slate-900">BDT {{ number_format($inv->total_amount, 2) }}</td>
                            <td class="px-4 py-3.5 font-semibold text-emerald-700">BDT {{ number_format($inv->paid_amount, 2) }}</td>
                            <td class="px-4 py-3.5">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold
                                    {{ $inv->status->value === 'paid' ? 'bg-emerald-50 text-emerald-700' : '' }}
                                    {{ $inv->status->value === 'unpaid' ? 'bg-rose-50 text-rose-700' : '' }}
                                    {{ $inv->status->value === 'partially_paid' ? 'bg-amber-50 text-amber-700' : '' }}">
                                    {{ $inv->status->label() }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-right">
                                <a href="{{ route('billing.show', $inv) }}" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-[11px]">
                                    View & Pay
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-8 text-center text-slate-400">No invoices recorded yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-5 py-3 border-t border-slate-100">
            {{ $invoices->links() }}
        </div>
    </div>
</div>
@endsection
