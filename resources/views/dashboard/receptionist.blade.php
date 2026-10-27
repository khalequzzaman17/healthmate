@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Front Desk & Reception</h1>
            <p class="text-xs text-slate-500 mt-0.5">Patient check-in, appointments scheduling, and invoice billing</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('patients.create') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-sky-600 hover:bg-sky-500 text-white text-xs font-semibold shadow-sm transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                <span>New Patient</span>
            </a>
            <a href="{{ route('appointments.create') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-teal-600 hover:bg-teal-500 text-white text-xs font-semibold shadow-sm transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                <span>Book Appointment</span>
            </a>
        </div>
    </div>

    <!-- Today's Appointments Table with Quick Check-In -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h2 class="text-sm font-bold text-slate-900">Today's Patient Check-in Roster</h2>
                <p class="text-xs text-slate-500">{{ now()->format('l, F j, Y') }}</p>
            </div>
            <span class="text-xs font-semibold text-slate-600">{{ $todayAppointments->count() }} appointments scheduled</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-slate-500 uppercase font-semibold text-[10px] tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3">Time</th>
                        <th class="px-4 py-3">Patient</th>
                        <th class="px-4 py-3">Doctor</th>
                        <th class="px-4 py-3">Service</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($todayAppointments as $appt)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-5 py-3 font-semibold text-slate-900">{{ $appt->scheduled_at->format('h:i A') }}</td>
                            <td class="px-4 py-3">
                                <a href="{{ route('patients.show', $appt->patient) }}" class="font-bold text-slate-900 hover:text-sky-600">
                                    {{ $appt->patient->fullName }}
                                </a>
                                <div class="text-[11px] text-slate-400">{{ $appt->patient->phone }}</div>
                            </td>
                            <td class="px-4 py-3">Dr. {{ $appt->doctor->name }}</td>
                            <td class="px-4 py-3">{{ $appt->service?->name ?? 'General' }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold
                                    {{ $appt->status->value === 'completed' ? 'bg-emerald-50 text-emerald-700' : '' }}
                                    {{ $appt->status->value === 'scheduled' ? 'bg-sky-50 text-sky-700' : '' }}
                                    {{ $appt->status->value === 'checked_in' ? 'bg-amber-50 text-amber-700' : '' }}
                                    {{ $appt->status->value === 'canceled' ? 'bg-rose-50 text-rose-700' : '' }}">
                                    {{ $appt->status->label() }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                @if($appt->status->value === 'scheduled')
                                    <form action="{{ route('appointments.check-in', $appt) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="px-2.5 py-1 rounded bg-teal-600 hover:bg-teal-500 text-white font-semibold text-[11px] shadow-xs">
                                            Check In
                                        </button>
                                    </form>
                                @endif
                                <a href="{{ route('billing.create', ['patient_id' => $appt->patient_id]) }}" class="ml-2 px-2.5 py-1 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-[11px]">
                                    Invoice
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-8 text-center text-slate-400">No appointments scheduled for today.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
