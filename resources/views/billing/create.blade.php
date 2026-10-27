@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto space-y-6" x-data="{
    items: [
        { description: 'General Medicine Consultation Fee', quantity: 1, unit_price: 800 }
    ],
    discount: 0,
    tax: 0,
    addItem() {
        this.items.push({ description: '', quantity: 1, unit_price: 0 });
    },
    removeItem(index) {
        if (this.items.length > 1) {
            this.items.splice(index, 1);
        }
    },
    subtotal() {
        return this.items.reduce((sum, item) => sum + ((parseFloat(item.quantity) || 0) * (parseFloat(item.unit_price) || 0)), 0);
    },
    total() {
        return Math.max(0, this.subtotal() - (parseFloat(this.discount) || 0) + (parseFloat(this.tax) || 0));
    }
}">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Create Patient Invoice</h1>
            <p class="text-xs text-slate-500 mt-0.5">Bill clinical consultations, lab tests, and medical procedures</p>
        </div>
        <a href="{{ route('billing.index') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900">&larr; Back to invoices</a>
    </div>

    <form action="{{ route('billing.store') }}" method="POST" class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 sm:p-8 space-y-6">
        @csrf

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pb-4 border-b border-slate-100">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Select Patient *</label>
                <select name="patient_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs">
                    <option value="">-- Choose Patient --</option>
                    @foreach($patients as $p)
                        <option value="{{ $p->id }}" {{ (old('patient_id', $preselectedPatientId) == $p->id) ? 'selected' : '' }}>
                            {{ $p->fullName }} ({{ $p->patient_number }}) &bull; {{ $p->phone }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Payment Due Date</label>
                <input type="date" name="due_date" value="{{ now()->addDays(7)->toDateString() }}"
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs">
            </div>
        </div>

        <!-- Line items builder -->
        <div>
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-xs font-bold uppercase tracking-wider text-slate-800">Invoice Line Items</h2>
                <button type="button" @click="addItem()" class="px-2.5 py-1 rounded-lg bg-sky-50 text-sky-700 font-semibold text-xs hover:bg-sky-100">
                    + Add Line Item
                </button>
            </div>

            <div class="space-y-3">
                <template x-for="(item, idx) in items" :key="idx">
                    <div class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 border border-slate-100">
                        <div class="flex-1">
                            <input type="text" :name="'items[' + idx + '][description]'" x-model="item.description" required placeholder="Service description"
                                   class="w-full px-3 py-1.5 rounded-lg border border-slate-200 text-xs bg-white">
                        </div>
                        <div class="w-20">
                            <input type="number" :name="'items[' + idx + '][quantity]'" x-model="item.quantity" min="1" required placeholder="Qty"
                                   class="w-full px-2 py-1.5 rounded-lg border border-slate-200 text-xs bg-white text-center">
                        </div>
                        <div class="w-28">
                            <input type="number" step="0.01" :name="'items[' + idx + '][unit_price]'" x-model="item.unit_price" min="0" required placeholder="Price"
                                   class="w-full px-3 py-1.5 rounded-lg border border-slate-200 text-xs bg-white text-right">
                        </div>
                        <div class="w-28 text-right font-bold text-xs text-slate-900">
                            BDT <span x-text="((parseFloat(item.quantity) || 0) * (parseFloat(item.unit_price) || 0)).toFixed(2)"></span>
                        </div>
                        <button type="button" @click="removeItem(idx)" class="p-1 text-slate-400 hover:text-rose-600">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                        </button>
                    </div>
                </template>
            </div>
        </div>

        <!-- Totals calculation -->
        <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row justify-between gap-6">
            <div class="flex-1">
                <label class="block text-xs font-semibold text-slate-700 mb-1">Notes / Instructions</label>
                <textarea name="notes" rows="2" placeholder="e.g. Inpatient medicines and pharmacy charges included"
                          class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs"></textarea>
            </div>

            <div class="w-full sm:w-64 space-y-2 text-xs">
                <div class="flex justify-between text-slate-600">
                    <span>Subtotal:</span>
                    <span class="font-bold text-slate-900">BDT <span x-text="subtotal().toFixed(2)"></span></span>
                </div>
                <div class="flex items-center justify-between text-slate-600 gap-2">
                    <span>Discount:</span>
                    <input type="number" step="0.01" name="discount_amount" x-model="discount" min="0"
                           class="w-24 px-2 py-1 rounded border border-slate-200 text-xs text-right">
                </div>
                <div class="flex items-center justify-between text-slate-600 gap-2">
                    <span>Tax (VAT):</span>
                    <input type="number" step="0.01" name="tax_amount" x-model="tax" min="0"
                           class="w-24 px-2 py-1 rounded border border-slate-200 text-xs text-right">
                </div>
                <div class="pt-2 border-t border-slate-200 flex justify-between font-bold text-sm text-slate-900">
                    <span>Total Due:</span>
                    <span class="text-sky-700">BDT <span x-text="total().toFixed(2)"></span></span>
                </div>
            </div>
        </div>

        <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
            <a href="{{ route('billing.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 hover:bg-slate-50">Cancel</a>
            <button type="submit" class="px-5 py-2.5 rounded-xl bg-sky-600 hover:bg-sky-500 text-white font-semibold text-xs shadow-sm transition">
                Create & Issue Invoice
            </button>
        </div>
    </form>
</div>
@endsection
