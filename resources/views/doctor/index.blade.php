@extends('layouts.app')

@section('content')
<div class="space-y-6" x-data="{ tab: 'queue' }">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Doctor Clinical Workspace</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Nurse follow-up review queue, consultation appointments, and diagnostic issuance</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('doctor.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-[#1E56E3] hover:bg-blue-600 text-white text-xs font-semibold shadow-xs transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                <span>Refresh Queue</span>
            </a>
        </div>
    </div>

    <!-- Tabs Header -->
    <div class="flex items-center gap-2 border-b border-slate-200 dark:border-slate-800 text-xs font-semibold">
        <button type="button" @click="tab = 'queue'" 
                :class="tab === 'queue' ? 'border-[#1E56E3] text-[#1E56E3] border-b-2 font-bold' : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-200'" 
                class="pb-3 px-3 transition flex items-center gap-1.5">
            <span>Review Queue (Nurse Reports)</span>
            <span class="px-1.5 py-0.5 rounded-full text-[10px] bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-400 font-bold">
                {{ $reviewQueue->count() }}
            </span>
        </button>
        <button type="button" @click="tab = 'today'" 
                :class="tab === 'today' ? 'border-[#1E56E3] text-[#1E56E3] border-b-2 font-bold' : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-200'" 
                class="pb-3 px-3 transition flex items-center gap-1.5">
            <span>Today's Consultations</span>
            <span class="px-1.5 py-0.5 rounded-full text-[10px] bg-blue-100 text-blue-800 dark:bg-blue-950/60 dark:text-blue-400 font-bold">
                {{ $todayAppointments->count() }}
            </span>
        </button>
    </div>

    <!-- Tab 1: Review Queue -->
    <div x-show="tab === 'queue'" class="space-y-4">
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-2xs overflow-hidden transition-colors">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600 dark:text-slate-300">
                    <thead class="bg-slate-50/75 dark:bg-slate-800/40 text-[10px] text-slate-400 uppercase font-semibold border-b border-slate-100 dark:border-slate-800">
                        <tr>
                            <th class="px-5 py-3 font-medium">Patient</th>
                            <th class="px-4 py-3 font-medium">Report Details</th>
                            <th class="px-4 py-3 font-medium">Submitted By</th>
                            <th class="px-4 py-3 font-medium">Key Vitals</th>
                            <th class="px-4 py-3 font-medium text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60">
                        @forelse($reviewQueue as $rep)
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition">
                                <td class="px-5 py-3.5">
                                    <a href="{{ route('patients.show', $rep->patient) }}" class="font-bold text-slate-900 dark:text-white hover:text-[#1E56E3]">
                                        {{ $rep->patient->fullName }}
                                    </a>
                                    <div class="text-[11px] text-slate-400 font-mono">{{ $rep->patient->patient_number }}</div>
                                </td>
                                <td class="px-4 py-3.5">
                                    <div class="font-bold text-slate-900 dark:text-white">{{ $rep->title }}</div>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 line-clamp-1">{{ $rep->summary }}</p>
                                </td>
                                <td class="px-4 py-3.5">
                                    <div class="font-medium text-slate-800 dark:text-slate-200">Nurse {{ $rep->nurse->name }}</div>
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

    <!-- Tab 2: Today's Consultations -->
    <div x-show="tab === 'today'" style="display: none;" class="space-y-4">
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-2xs overflow-hidden transition-colors">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600 dark:text-slate-300">
                    <thead class="bg-slate-50/75 dark:bg-slate-800/40 text-[10px] text-slate-400 uppercase font-semibold border-b border-slate-100 dark:border-slate-800">
                        <tr>
                            <th class="px-5 py-3 font-medium">Time</th>
                            <th class="px-4 py-3 font-medium">Patient</th>
                            <th class="px-4 py-3 font-medium">Service</th>
                            <th class="px-4 py-3 font-medium">Duration</th>
                            <th class="px-4 py-3 font-medium">Status</th>
                            <th class="px-4 py-3 font-medium text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60">
                        @forelse($todayAppointments as $appt)
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition">
                                <td class="px-5 py-3.5 font-bold text-slate-900 dark:text-white">{{ $appt->scheduled_at->format('h:i A') }}</td>
                                <td class="px-4 py-3.5">
                                    <a href="{{ route('patients.show', $appt->patient) }}" class="font-bold text-slate-900 dark:text-white hover:text-[#1E56E3]">
                                        {{ $appt->patient->fullName }}
                                    </a>
                                    <div class="text-[11px] text-slate-400 font-mono">{{ $appt->patient->phone }}</div>
                                </td>
                                <td class="px-4 py-3.5">{{ $appt->service?->name ?? 'Standard Consult' }}</td>
                                <td class="px-4 py-3.5">{{ $appt->duration_minutes }} mins</td>
                                <td class="px-4 py-3.5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold
                                        {{ $appt->status->value === 'completed' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400' : 'bg-blue-50 text-blue-700 dark:bg-blue-950/60 dark:text-blue-400' }}">
                                        {{ $appt->status->label() }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 text-right">
                                    @if($appt->status->value !== 'completed')
                                        <a href="{{ route('doctor.consultations.create', $appt) }}" class="px-3.5 py-1.5 rounded-lg bg-[#1E56E3] hover:bg-blue-600 text-white font-semibold text-[11px] shadow-xs">
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
