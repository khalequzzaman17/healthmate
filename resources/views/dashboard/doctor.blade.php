@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Doctor Clinical Desk</h1>
            <p class="text-xs text-slate-500 mt-0.5">Welcome, Dr. {{ auth()->user()->name }} &bull; Today's schedule and clinical review queue</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('doctor.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-sky-600 hover:bg-sky-500 text-white text-xs font-semibold shadow-sm transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                <span>Open Clinical Workspace</span>
            </a>
        </div>
    </div>

    <!-- Doctor Metrics -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Today's Consultations</span>
                <div class="text-2xl font-bold text-slate-900 mt-1">{{ $todayAppointments->count() }}</div>
                <div class="text-[11px] text-sky-600 font-medium mt-1">Scheduled for you today</div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Nurse Reports to Review</span>
                <div class="text-2xl font-bold text-slate-900 mt-1">{{ $reportsAwaitingReview->count() }}</div>
                <div class="text-[11px] text-amber-600 font-medium mt-1">Follow-up vitals submitted</div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Approved / Earned Commissions</span>
                <div class="text-2xl font-bold text-emerald-600 mt-1">BDT {{ number_format($commissionTotal, 2) }}</div>
                <div class="text-[11px] text-slate-500 font-medium mt-1">{{ $completedConsultationsCount }} completed consultations</div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
        </div>
    </div>

    <!-- Tables Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Today's Appointments & Consult button -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                <h2 class="text-sm font-bold text-slate-900">Today's Appointment Schedule</h2>
                <span class="text-xs font-medium text-slate-500">{{ now()->format('l, M d') }}</span>
            </div>

            <div class="divide-y divide-slate-100">
                @forelse($todayAppointments as $appt)
                    <div class="p-4 flex items-center justify-between hover:bg-slate-50/50 transition">
                        <div>
                            <div class="text-sm font-bold text-slate-900">{{ $appt->patient->fullName }}</div>
                            <div class="text-xs text-slate-500 mt-0.5">
                                {{ $appt->scheduled_at->format('h:i A') }} ({{ $appt->duration_minutes }} mins) &bull; {{ $appt->service?->name ?? 'General Consultation' }}
                            </div>
                            <span class="inline-flex mt-1 items-center px-2 py-0.5 rounded text-[10px] font-semibold
                                {{ $appt->status->value === 'completed' ? 'bg-emerald-50 text-emerald-700' : 'bg-sky-50 text-sky-700' }}">
                                {{ $appt->status->label() }}
                            </span>
                        </div>
                        <div>
                            @if($appt->status->value !== 'completed')
                                <a href="{{ route('doctor.consultations.create', $appt) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-sky-600 hover:bg-sky-500 text-white text-xs font-semibold shadow-xs">
                                    <span>Start Consult</span>
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                                </a>
                            @else
                                <span class="text-xs font-semibold text-emerald-600">Completed</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-slate-400 text-xs">No appointments scheduled for today.</div>
                @endforelse
            </div>
        </div>

        <!-- Nurse Reports awaiting Doctor Review -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                <h2 class="text-sm font-bold text-slate-900">Nurse Reports Awaiting Review</h2>
                <a href="{{ route('doctor.index') }}" class="text-xs font-semibold text-sky-600 hover:text-sky-700">View queue &rarr;</a>
            </div>

            <div class="divide-y divide-slate-100">
                @forelse($reportsAwaitingReview as $rep)
                    <div class="p-4 flex items-center justify-between hover:bg-slate-50/50 transition">
                        <div class="max-w-md">
                            <div class="text-xs font-bold text-slate-900">{{ $rep->title }}</div>
                            <div class="text-xs text-slate-500 mt-0.5">
                                Patient: <strong>{{ $rep->patient->fullName }}</strong> &bull; Nurse: {{ $rep->nurse->name }} &bull; {{ $rep->submitted_at->diffForHumans() }}
                            </div>
                            <p class="text-xs text-slate-600 mt-1 line-clamp-1">{{ $rep->summary }}</p>
                        </div>
                        <div class="shrink-0 ml-4">
                            <a href="{{ route('doctor.reports.show', $rep) }}" class="px-3 py-1.5 rounded-lg bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold">
                                Review & Note
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-slate-400 text-xs">All submitted nurse reports have been reviewed.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
