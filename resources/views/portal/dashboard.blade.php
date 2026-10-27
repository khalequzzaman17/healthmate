@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Welcome, {{ $patient->fullName }}</h1>
            <p class="text-xs text-slate-500 mt-0.5">Patient ID: <span class="font-mono font-bold text-sky-700">{{ $patient->patient_number }}</span> &bull; HealthMate Patient Care Portal</p>
        </div>
        <div>
            <a href="{{ route('portal.book') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-sky-600 hover:bg-sky-500 text-white text-xs font-semibold shadow-sm transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                <span>Book Appointment</span>
            </a>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Upcoming Appointments</span>
            <div class="text-2xl font-bold text-slate-900 mt-1">{{ $upcomingAppointments->count() }}</div>
            <a href="{{ route('portal.book') }}" class="text-[11px] text-sky-600 font-semibold mt-1 inline-block">Book new slot &rarr;</a>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Active Prescriptions</span>
            <div class="text-2xl font-bold text-slate-900 mt-1">{{ $prescriptions->count() }}</div>
            <a href="{{ route('portal.records') }}" class="text-[11px] text-sky-600 font-semibold mt-1 inline-block">View medicine schedule &rarr;</a>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Billing History</span>
            <div class="text-2xl font-bold text-slate-900 mt-1">{{ $invoices->count() }}</div>
            <span class="text-[11px] text-slate-400 mt-1 block">Invoices & receipts</span>
        </div>
    </div>

    <!-- Portal Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Upcoming Appointments -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5">
            <h2 class="text-sm font-bold text-slate-900 mb-3">Your Upcoming Appointments</h2>
            <div class="space-y-3">
                @forelse($upcomingAppointments as $appt)
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-between">
                        <div>
                            <div class="text-sm font-bold text-slate-900">Dr. {{ $appt->doctor->name }}</div>
                            <div class="text-xs text-slate-500 mt-0.5">
                                {{ $appt->scheduled_at->format('l, F j, Y &bull; h:i A') }}
                            </div>
                            <div class="text-xs text-slate-600 mt-1">{{ $appt->service?->name ?? 'Standard Visit' }}</div>
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-sky-100 text-sky-800">
                            {{ $appt->status->label() }}
                        </span>
                    </div>
                @empty
                    <div class="py-8 text-center text-xs text-slate-400">You have no upcoming appointments scheduled.</div>
                @endforelse
            </div>
        </div>

        <!-- Recent Prescriptions -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5">
            <h2 class="text-sm font-bold text-slate-900 mb-3">Your Prescriptions</h2>
            <div class="space-y-3">
                @forelse($prescriptions as $rx)
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                        <div class="flex items-center justify-between text-xs font-bold text-slate-900 border-b border-slate-200/60 pb-2">
                            <span>Prescription #{{ $rx->prescription_number }}</span>
                            <span class="text-slate-500 font-normal">{{ $rx->issued_at->format('M d, Y') }}</span>
                        </div>
                        <div class="text-xs text-slate-600 mt-1.5">Attending Doctor: <strong>Dr. {{ $rx->doctor->name }}</strong></div>
                        <div class="mt-2.5 space-y-1">
                            @foreach($rx->items as $item)
                                <div class="text-xs text-slate-700 flex justify-between bg-white p-2 rounded-lg border border-slate-100">
                                    <span class="font-bold text-slate-900">{{ $item->medicine_name }} ({{ $item->strength }})</span>
                                    <span class="text-slate-600">{{ $item->dosage_instructions }} &bull; {{ $item->duration_days }} days</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center text-xs text-slate-400">No prescriptions recorded yet.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
