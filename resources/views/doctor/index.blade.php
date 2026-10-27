@extends('layouts.app')

@section('content')
<div class="space-y-6" x-data="{ tab: 'queue' }">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Doctor Clinical Workspace</h1>
            <p class="text-xs text-slate-500 mt-0.5">Nurse follow-up review queue, consultation appointments, and diagnostic issuance</p>
        </div>
    </div>

    <!-- Tabs -->
    <div class="flex items-center gap-2 border-b border-slate-200 text-xs font-semibold">
        <button @click="tab = 'queue'" :class="tab === 'queue' ? 'border-sky-600 text-sky-600 border-b-2 font-bold' : 'text-slate-500 hover:text-slate-800'" class="pb-3 px-3 transition flex items-center gap-1.5">
            <span>Review Queue (Nurse Reports)</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-amber-100 text-amber-800 font-bold">{{ $reviewQueue->count() }}</span>
        </button>
        <button @click="tab = 'today'" :class="tab === 'today' ? 'border-sky-600 text-sky-600 border-b-2 font-bold' : 'text-slate-500 hover:text-slate-800'" class="pb-3 px-3 transition flex items-center gap-1.5">
            <span>Today's Consultation Appointments</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-sky-100 text-sky-800 font-bold">{{ $todayAppointments->count() }}</span>
        </button>
    </div>

    <!-- Review Queue -->
    <div x-show="tab === 'queue'" class="space-y-4">
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-[10px] text-slate-500 uppercase font-semibold border-b border-slate-100">
                        <tr>
                            <th class="px-5 py-3">Patient</th>
                            <th class="px-4 py-3">Report Details</th>
                            <th class="px-4 py-3">Submitted By</th>
                            <th class="px-4 py-3">Key Vitals</th>
                            <th class="px-4 py-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($reviewQueue as $rep)
                            <tr class="hover:bg-slate-50/50">
                                <td class="px-5 py-3.5">
                                    <a href="{{ route('patients.show', $rep->patient) }}" class="font-bold text-slate-900 hover:text-sky-600">
                                        {{ $rep->patient->fullName }}
                                    </a>
                                    <div class="text-[11px] text-slate-400 font-mono">{{ $rep->patient->patient_number }}</div>
                                </td>
                                <td class="px-4 py-3.5">
                                    <div class="font-bold text-slate-900">{{ $rep->title }}</div>
                                    <p class="text-xs text-slate-500 mt-0.5 line-clamp-1">{{ $rep->summary }}</p>
                                </td>
                                <td class="px-4 py-3.5">
                                    <div class="font-medium text-slate-800">Nurse {{ $rep->nurse->name }}</div>
                                    <div class="text-[11px] text-slate-400">{{ $rep->submitted_at->diffForHumans() }}</div>
                                </td>
                                <td class="px-4 py-3.5">
                                    @if($rep->call && $rep->call->healthObservations->isNotEmpty())
                                        @php $obs = $rep->call->healthObservations->first(); @endphp
                                        <div class="text-xs">
                                            @if($obs->systolic_bp)
                                                <span>BP: <strong>{{ $obs->systolic_bp }}/{{ $obs->diastolic_bp }}</strong></span>
                                            @endif
                                            @if($obs->blood_glucose)
                                                <span class="ml-1">FBG: <strong>{{ $obs->blood_glucose }}</strong></span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-slate-400">Call record</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5 text-right">
                                    <a href="{{ route('doctor.reports.show', $rep) }}" class="px-3 py-1.5 rounded-lg bg-amber-500 hover:bg-amber-600 text-white font-semibold text-[11px] shadow-xs">
                                        Review Report
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-8 text-center text-slate-400">All nurse submitted reports have been reviewed.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Today's Consultations -->
    <div x-show="tab === 'today'" style="display: none;" class="space-y-4">
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-[10px] text-slate-500 uppercase font-semibold border-b border-slate-100">
                        <tr>
                            <th class="px-5 py-3">Time</th>
                            <th class="px-4 py-3">Patient</th>
                            <th class="px-4 py-3">Service</th>
                            <th class="px-4 py-3">Duration</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($todayAppointments as $appt)
                            <tr class="hover:bg-slate-50/50">
                                <td class="px-5 py-3.5 font-bold text-slate-900">{{ $appt->scheduled_at->format('h:i A') }}</td>
                                <td class="px-4 py-3.5">
                                    <a href="{{ route('patients.show', $appt->patient) }}" class="font-bold text-slate-900 hover:text-sky-600">
                                        {{ $appt->patient->fullName }}
                                    </a>
                                    <div class="text-[11px] text-slate-400">{{ $appt->patient->phone }}</div>
                                </td>
                                <td class="px-4 py-3.5">{{ $appt->service?->name ?? 'Standard Consult' }}</td>
                                <td class="px-4 py-3.5">{{ $appt->duration_minutes }} mins</td>
                                <td class="px-4 py-3.5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold
                                        {{ $appt->status->value === 'completed' ? 'bg-emerald-50 text-emerald-700' : 'bg-sky-50 text-sky-700' }}">
                                        {{ $appt->status->label() }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 text-right">
                                    @if($appt->status->value !== 'completed')
                                        <a href="{{ route('doctor.consultations.create', $appt) }}" class="px-3 py-1.5 rounded-lg bg-sky-600 hover:bg-sky-500 text-white font-semibold text-[11px] shadow-xs">
                                            Open Consultation
                                        </a>
                                    @else
                                        <span class="text-xs text-emerald-600 font-bold">Finished</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-8 text-center text-slate-400">No appointments scheduled for you today.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
