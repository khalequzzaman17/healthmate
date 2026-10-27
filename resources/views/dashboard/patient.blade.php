@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Welcome, {{ $patient ? $patient->fullName : auth()->user()->name }}</h1>
            <p class="text-xs text-slate-500 mt-0.5">Your personal healthcare portal &bull; Appointments, prescriptions and medical care</p>
        </div>
        <div>
            <a href="{{ route('portal.book') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-sky-600 hover:bg-sky-500 text-white text-xs font-semibold shadow-sm transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                <span>Book New Appointment</span>
            </a>
        </div>
    </div>

    <!-- Patient Portal Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Upcoming Appointments -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5">
            <h2 class="text-sm font-bold text-slate-900 mb-3 flex items-center justify-between">
                <span>Upcoming Appointments</span>
                <span class="text-xs text-sky-600 font-semibold">{{ $upcomingAppointments->count() }} booked</span>
            </h2>

            <div class="space-y-3">
                @forelse($upcomingAppointments as $appt)
                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-between">
                        <div>
                            <div class="text-sm font-bold text-slate-900">Dr. {{ $appt->doctor->name }}</div>
                            <div class="text-xs text-slate-500 mt-0.5">
                                {{ $appt->scheduled_at->format('M d, Y &bull; h:i A') }} &bull; {{ $appt->service?->name ?? 'General Consultation' }}
                            </div>
                        </div>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-sky-100 text-sky-800">
                            {{ $appt->status->label() }}
                        </span>
                    </div>
                @empty
                    <div class="text-xs text-slate-400 py-6 text-center">You have no upcoming appointments.</div>
                @endforelse
            </div>
        </div>

        <!-- Recent Prescriptions -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5">
            <h2 class="text-sm font-bold text-slate-900 mb-3">Recent Prescriptions</h2>

            <div class="space-y-3">
                @forelse($prescriptions as $rx)
                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                        <div class="flex items-center justify-between text-xs font-bold text-slate-900">
                            <span>Prescription #{{ $rx->prescription_number }}</span>
                            <span class="text-slate-500 font-normal">{{ $rx->issued_at->format('M d, Y') }}</span>
                        </div>
                        <div class="text-xs text-slate-600 mt-1">Prescribed by Dr. {{ $rx->doctor->name }}</div>
                        <div class="mt-2 pt-2 border-t border-slate-200/60 space-y-1">
                            @foreach($rx->items as $item)
                                <div class="text-xs text-slate-700 flex items-center justify-between">
                                    <span class="font-medium">{{ $item->medicine_name }} ({{ $item->strength }})</span>
                                    <span class="text-slate-500">{{ $item->dosage_instructions }} &bull; {{ $item->duration_days }} days</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="text-xs text-slate-400 py-6 text-center">No prescriptions found.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
