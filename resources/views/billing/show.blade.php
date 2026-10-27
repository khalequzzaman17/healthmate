@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto space-y-6" x-data="{ openPayModal: false }">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 font-mono">{{ $invoice->invoice_number }}</h1>
            <p class="text-xs text-slate-500 mt-0.5">Created on {{ $invoice->created_at->format('F d, Y') }} &bull; Due Date: {{ $invoice->due_date?->format('M d, Y') ?? 'Immediate' }}</p>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" onclick="window.print()" class="px-3 py-1.5 rounded-lg border border-slate-200 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                Print Invoice
            </button>
            @if($invoice->dueAmount() > 0)
                <button type="button" @click="openPayModal = true" class="px-3.5 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-xs shadow-xs">
                    + Record Payment
                </button>
            @endif
        </div>
    </div>

    <!-- Invoice Sheet Paper -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-8 space-y-6">
        <!-- Clinic & Patient Header -->
        <div class="flex flex-col sm:flex-row justify-between gap-6 pb-6 border-b border-slate-100">
            <div>
                <span class="text-xs uppercase font-bold text-slate-400">Issued By</span>
                <div class="text-lg font-bold text-slate-900 mt-0.5">{{ $activeClinic->name }}</div>
                <div class="text-xs text-slate-500 mt-1">
                    {{ $activeClinic->address }}<br>
                    Phone: {{ $activeClinic->phone }} &bull; Email: {{ $activeClinic->email }}
                </div>
            </div>

            <div class="sm:text-right">
                <span class="text-xs uppercase font-bold text-slate-400">Billed To</span>
                <div class="text-lg font-bold text-slate-900 mt-0.5">{{ $invoice->patient->fullName }}</div>
                <div class="text-xs text-slate-500 mt-1">
                    Patient ID: <span class="font-mono font-semibold text-slate-800">{{ $invoice->patient->patient_number }}</span><br>
                    Phone: {{ $invoice->patient->phone }}
                </div>
            </div>
        </div>

        <!-- Line Items Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 text-[10px] text-slate-400 uppercase font-semibold border-b border-slate-100">
                    <tr>
                        <th class="px-4 py-3">Description</th>
                        <th class="px-4 py-3 text-center">Qty</th>
                        <th class="px-4 py-3 text-right">Unit Price</th>
                        <th class="px-4 py-3 text-right">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($invoice->items as $item)
                        <tr>
                            <td class="px-4 py-3 font-medium text-slate-900">{{ $item->description }}</td>
                            <td class="px-4 py-3 text-center">{{ $item->quantity }}</td>
                            <td class="px-4 py-3 text-right">BDT {{ number_format($item->unit_price, 2) }}</td>
                            <td class="px-4 py-3 text-right font-bold text-slate-900">BDT {{ number_format($item->total_price, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Totals Calculation -->
        <div class="flex justify-end pt-4 border-t border-slate-100">
            <div class="w-64 space-y-2 text-xs">
                <div class="flex justify-between text-slate-600">
                    <span>Subtotal:</span>
                    <span>BDT {{ number_format($invoice->subtotal, 2) }}</span>
                </div>
                @if($invoice->discount_amount > 0)
                    <div class="flex justify-between text-rose-600">
                        <span>Discount:</span>
                        <span>- BDT {{ number_format($invoice->discount_amount, 2) }}</span>
                    </div>
                @endif
                @if($invoice->tax_amount > 0)
                    <div class="flex justify-between text-slate-600">
                        <span>Tax / VAT:</span>
                        <span>+ BDT {{ number_format($invoice->tax_amount, 2) }}</span>
                    </div>
                @endif
                <div class="pt-2 border-t border-slate-200 flex justify-between font-bold text-sm text-slate-900">
                    <span>Total Amount:</span>
                    <span>BDT {{ number_format($invoice->total_amount, 2) }}</span>
                </div>
                <div class="flex justify-between text-emerald-700 font-semibold">
                    <span>Total Paid:</span>
                    <span>BDT {{ number_format($invoice->paid_amount, 2) }}</span>
                </div>
                <div class="flex justify-between font-bold text-xs {{ $invoice->dueAmount() > 0 ? 'text-rose-600' : 'text-slate-500' }}">
                    <span>Remaining Due:</span>
                    <span>BDT {{ number_format($invoice->dueAmount(), 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Payment History -->
        @if($invoice->payments->isNotEmpty())
            <div class="pt-6 border-t border-slate-100">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800 mb-3">Collected Payments History</h3>
                <div class="rounded-xl border border-slate-100 overflow-hidden">
                    <table class="w-full text-left text-xs text-slate-600">
                        <thead class="bg-slate-50 text-[10px] text-slate-400 uppercase font-semibold">
                            <tr>
                                <th class="px-4 py-2.5">Date</th>
                                <th class="px-4 py-2.5">Payment #</th>
                                <th class="px-4 py-2.5">Method</th>
                                <th class="px-4 py-2.5">Reference</th>
                                <th class="px-4 py-2.5 text-right">Amount</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($invoice->payments as $payment)
                                <tr>
                                    <td class="px-4 py-2.5">{{ $payment->paid_at->format('M d, Y h:i A') }}</td>
                                    <td class="px-4 py-2.5 font-mono text-[11px] font-semibold text-slate-900">{{ $payment->payment_number }}</td>
                                    <td class="px-4 py-2.5 font-medium text-slate-800">{{ $payment->method }}</td>
                                    <td class="px-4 py-2.5 text-slate-500">{{ $payment->reference ?? '—' }}</td>
                                    <td class="px-4 py-2.5 text-right font-bold text-emerald-700">BDT {{ number_format($payment->amount, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>

    <!-- Record Payment Modal -->
    <div x-show="openPayModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60" style="display: none;">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 text-left shadow-2xl space-y-4" @click.away="openPayModal = false">
            <h3 class="text-base font-bold text-slate-900">Record Payment</h3>
            <p class="text-xs text-slate-500">Collect payment against invoice {{ $invoice->invoice_number }}. Remaining due: <strong>BDT {{ number_format($invoice->dueAmount(), 2) }}</strong>.</p>

            <form action="{{ route('billing.payments.store', $invoice) }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Amount to Collect (BDT) *</label>
                    <input type="number" step="0.01" name="amount" value="{{ $invoice->dueAmount() }}" max="{{ $invoice->dueAmount() }}" required
                           class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-900">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Payment Method *</label>
                    <select name="method" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs">
                        <option value="Cash">Cash</option>
                        <option value="bKash">bKash (MFS)</option>
                        <option value="Nagad">Nagad (MFS)</option>
                        <option value="Credit / Debit Card">Credit / Debit Card</option>
                        <option value="Bank Transfer">Bank Transfer</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Transaction Reference / Note</label>
                    <input type="text" name="reference" placeholder="e.g. bKash TrxID or POS Slip #"
                           class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs">
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" @click="openPayModal = false" class="px-3 py-1.5 rounded-lg border border-slate-200 text-xs font-semibold text-slate-700">Cancel</button>
                    <button type="submit" class="px-4 py-1.5 rounded-lg bg-emerald-600 text-white text-xs font-semibold shadow-xs">Collect Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
