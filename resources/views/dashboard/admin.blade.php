@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header banner -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Clinic Administration</h1>
            <p class="text-xs text-slate-500 mt-0.5">Real-time clinical, operational and financial overview of {{ $activeClinic->name }}</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('patients.create') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-sky-600 hover:bg-sky-500 text-white text-xs font-semibold shadow-sm transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                <span>Register Patient</span>
            </a>
            <a href="{{ route('appointments.create') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-semibold shadow-sm transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                <span>Book Slot</span>
            </a>
        </div>
    </div>

    <!-- Metrics Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Patients -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Patients</span>
                <div class="text-2xl font-bold text-slate-900 mt-1">{{ number_format($totalPatients) }}</div>
                <div class="text-[11px] text-emerald-600 font-medium mt-1 flex items-center gap-1">
                    <span>Registered in workspace</span>
                </div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
            </div>
        </div>

        <!-- Today's Appointments -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Today's Visits</span>
                <div class="text-2xl font-bold text-slate-900 mt-1">{{ number_format($todayAppointmentsCount) }}</div>
                <a href="{{ route('appointments.index') }}" class="text-[11px] text-sky-600 hover:text-sky-700 font-medium mt-1 inline-block">
                    View schedule &rarr;
                </a>
            </div>
            <div class="w-12 h-12 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
            </div>
        </div>

        <!-- Nurse Reports Pending Review -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Reports For Review</span>
                <div class="text-2xl font-bold text-slate-900 mt-1">{{ number_format($pendingReportsCount) }}</div>
                <div class="text-[11px] text-amber-600 font-medium mt-1">
                    Nurse call submissions
                </div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
            </div>
        </div>

        <!-- Revenue Collected -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Collected Revenue</span>
                <div class="text-2xl font-bold text-slate-900 mt-1">BDT {{ number_format($totalRevenue, 2) }}</div>
                <div class="text-[11px] text-slate-500 font-medium mt-1">
                    Outstanding: BDT {{ number_format($dueRevenue, 2) }}
                </div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
        </div>
    </div>

    <!-- Active Tables Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Appointments -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Recent Appointments</h2>
                    <p class="text-xs text-slate-500">Upcoming schedule and check-in status</p>
                </div>
                <a href="{{ route('appointments.index') }}" class="text-xs font-semibold text-sky-600 hover:text-sky-700">View all &rarr;</a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-slate-500 uppercase font-semibold text-[10px] tracking-wider border-b border-slate-100">
                        <tr>
                            <th class="px-5 py-3">Patient</th>
                            <th class="px-4 py-3">Doctor</th>
                            <th class="px-4 py-3">Time</th>
                            <th class="px-4 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($recentAppointments as $appt)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="px-5 py-3 font-medium text-slate-900">
                                    <a href="{{ route('patients.show', $appt->patient) }}" class="hover:text-sky-600">
                                        {{ $appt->patient->fullName }}
                                    </a>
                                </td>
                                <td class="px-4 py-3">Dr. {{ $appt->doctor->name }}</td>
                                <td class="px-4 py-3">{{ $appt->scheduled_at->format('M d, h:i A') }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold
                                        {{ $appt->status->value === 'completed' ? 'bg-emerald-50 text-emerald-700' : '' }}
                                        {{ $appt->status->value === 'scheduled' ? 'bg-sky-50 text-sky-700' : '' }}
                                        {{ $appt->status->value === 'checked_in' ? 'bg-amber-50 text-amber-700' : '' }}
                                        {{ $appt->status->value === 'canceled' ? 'bg-rose-50 text-rose-700' : '' }}">
                                        {{ $appt->status->label() }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-5 py-6 text-center text-slate-400">No appointments scheduled yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Nurse Health Reports Queue -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Nurse Care Reports</h2>
                    <p class="text-xs text-slate-500">Submitted follow-up summaries awaiting doctor review</p>
                </div>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                    {{ $pendingReportsCount }} Pending
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-slate-500 uppercase font-semibold text-[10px] tracking-wider border-b border-slate-100">
                        <tr>
                            <th class="px-5 py-3">Patient</th>
                            <th class="px-4 py-3">Nurse</th>
                            <th class="px-4 py-3">Summary</th>
                            <th class="px-4 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($recentReports as $rep)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="px-5 py-3 font-medium text-slate-900">
                                    {{ $rep->patient->fullName }}
                                </td>
                                <td class="px-4 py-3">{{ $rep->nurse->name }}</td>
                                <td class="px-4 py-3 max-w-xs truncate" title="{{ $rep->title }}">{{ $rep->title }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold
                                        {{ $rep->status->value === 'submitted' ? 'bg-amber-50 text-amber-700' : 'bg-emerald-50 text-emerald-700' }}">
                                        {{ $rep->status->label() }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-5 py-6 text-center text-slate-400">No health reports submitted yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
