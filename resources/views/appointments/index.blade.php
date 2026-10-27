@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Appointment Management</h1>
            <p class="text-xs text-slate-500 mt-0.5">Doctor calendars, patient visits, check-ins and state machine status</p>
        </div>
        <div>
            <a href="{{ route('appointments.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-sky-600 hover:bg-sky-500 text-white text-xs font-semibold shadow-sm transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                <span>Book New Appointment</span>
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
        <form method="GET" action="{{ route('appointments.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">Date</label>
                <input type="date" name="date" value="{{ $date }}" class="w-full px-3 py-1.5 rounded-lg border border-slate-200 text-xs">
            </div>

            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">Status</label>
                <select name="status" class="w-full px-3 py-1.5 rounded-lg border border-slate-200 text-xs">
                    <option value="">All Statuses</option>
                    <option value="scheduled" {{ $status === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                    <option value="checked_in" {{ $status === 'checked_in' ? 'selected' : '' }}>Checked In</option>
                    <option value="in_consultation" {{ $status === 'in_consultation' ? 'selected' : '' }}>In Consultation</option>
                    <option value="completed" {{ $status === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="canceled" {{ $status === 'canceled' ? 'selected' : '' }}>Canceled</option>
                </select>
            </div>

            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">Doctor</label>
                <select name="doctor_id" class="w-full px-3 py-1.5 rounded-lg border border-slate-200 text-xs">
                    <option value="">All Doctors</option>
                    @foreach($doctors as $doc)
                        <option value="{{ $doc->id }}" {{ $doctorId == $doc->id ? 'selected' : '' }}>
                            Dr. {{ $doc->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="px-4 py-2 rounded-lg bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold">
                    Filter Schedule
                </button>
                <a href="{{ route('appointments.index') }}" class="px-3 py-2 rounded-lg bg-slate-100 text-slate-700 text-xs font-semibold">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Appointments Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-slate-500 uppercase font-semibold text-[10px] tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3">Time</th>
                        <th class="px-4 py-3">Patient</th>
                        <th class="px-4 py-3">Doctor</th>
                        <th class="px-4 py-3">Service</th>
                        <th class="px-4 py-3">Duration</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($appointments as $appt)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-5 py-3.5 font-bold text-slate-900">
                                {{ $appt->scheduled_at->format('h:i A') }}
                                <div class="text-[10px] text-slate-400 font-normal">{{ $appt->scheduled_at->format('M d, Y') }}</div>
                            </td>
                            <td class="px-4 py-3.5">
                                <a href="{{ route('patients.show', $appt->patient) }}" class="font-bold text-slate-900 hover:text-sky-600">
                                    {{ $appt->patient->fullName }}
                                </a>
                                <div class="text-[11px] text-slate-400">{{ $appt->patient->phone }}</div>
                            </td>
                            <td class="px-4 py-3.5 font-medium text-slate-800">Dr. {{ $appt->doctor->name }}</td>
                            <td class="px-4 py-3.5">{{ $appt->service?->name ?? 'General Consultation' }}</td>
                            <td class="px-4 py-3.5">{{ $appt->duration_minutes }} mins</td>
                            <td class="px-4 py-3.5">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold
                                    {{ $appt->status->value === 'completed' ? 'bg-emerald-50 text-emerald-700' : '' }}
                                    {{ $appt->status->value === 'scheduled' ? 'bg-sky-50 text-sky-700' : '' }}
                                    {{ $appt->status->value === 'checked_in' ? 'bg-amber-50 text-amber-700' : '' }}
                                    {{ $appt->status->value === 'in_consultation' ? 'bg-purple-50 text-purple-700' : '' }}
                                    {{ $appt->status->value === 'canceled' ? 'bg-rose-50 text-rose-700' : '' }}">
                                    {{ $appt->status->label() }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-right space-x-1.5">
                                @if($appt->status->value === 'scheduled')
                                    <form action="{{ route('appointments.check-in', $appt) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="px-2.5 py-1 rounded-lg bg-teal-600 hover:bg-teal-500 text-white font-semibold text-[11px]">
                                            Check In
                                        </button>
                                    </form>
                                @endif

                                @if(in_array($appt->status->value, ['scheduled', 'checked_in']))
                                    <a href="{{ route('doctor.consultations.create', $appt) }}" class="px-2.5 py-1 rounded-lg bg-sky-600 hover:bg-sky-500 text-white font-semibold text-[11px]">
                                        Consult
                                    </a>
                                @endif

                                @if(! in_array($appt->status->value, ['completed', 'canceled']))
                                    <form action="{{ route('appointments.cancel', $appt) }}" method="POST" class="inline" onsubmit="return confirm('Cancel this appointment?');">
                                        @csrf
                                        <input type="hidden" name="reason" value="Canceled by front desk / patient request">
                                        <button type="submit" class="px-2 py-1 rounded-lg text-rose-600 hover:bg-rose-50 font-semibold text-[11px]">
                                            Cancel
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-8 text-center text-slate-400">
                                No appointments found for the selected date and filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-5 py-3 border-t border-slate-100">
            {{ $appointments->links() }}
        </div>
    </div>
</div>
@endsection
