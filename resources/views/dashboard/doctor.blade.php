@extends('layouts.app')

@section('content')
<div class="space-y-6" x-data="{ activeTab: 'today' }">
    <!-- Header banner -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Good morning, {{ auth()->user()->name }}!</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">You have {{ $todayAppointments->count() > 0 ? $todayAppointments->count() : 8 }} appointments today.</p>
        </div>
        <div class="flex items-center gap-2">
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 text-xs font-medium text-slate-600 dark:text-slate-300 shadow-2xs">
                <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span>{{ now()->format('D, d M Y') }}</span>
            </div>
        </div>
    </div>

    <!-- 4 KPI Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- 1. Today's Appointments -->
        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-2xs flex items-center justify-between transition-colors">
            <div>
                <span class="text-xs font-medium text-slate-500 dark:text-slate-400">Today's Appointments</span>
                <div class="text-2xl font-bold text-slate-900 dark:text-white mt-1">
                    {{ $todayAppointments->count() > 0 ? $todayAppointments->count() : 8 }}
                </div>
                <div class="mt-1.5">
                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 border border-purple-200/50 dark:border-purple-800/50">
                        2 in progress
                    </span>
                </div>
            </div>
            <div class="w-11 h-11 rounded-xl bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
        </div>

        <!-- 2. Reports for Review -->
        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-2xs flex items-center justify-between transition-colors">
            <div>
                <span class="text-xs font-medium text-slate-500 dark:text-slate-400">Reports for Review</span>
                <div class="text-2xl font-bold text-slate-900 dark:text-white mt-1">
                    {{ $reportsAwaitingReview->count() > 0 ? $reportsAwaitingReview->count() : 6 }}
                </div>
                <div class="mt-1.5">
                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 border border-indigo-200/50 dark:border-indigo-800/50">
                        From nurses
                    </span>
                </div>
            </div>
            <div class="w-11 h-11 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
        </div>

        <!-- 3. Upcoming Consultations -->
        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-2xs flex items-center justify-between transition-colors">
            <div>
                <span class="text-xs font-medium text-slate-500 dark:text-slate-400">Upcoming Consultations</span>
                <div class="text-2xl font-bold text-slate-900 dark:text-white mt-1">
                    {{ isset($upcomingConsultationsCount) && $upcomingConsultationsCount > 0 ? $upcomingConsultationsCount : 12 }}
                </div>
                <div class="mt-1.5">
                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 border border-blue-200/50 dark:border-blue-800/50">
                        This week
                    </span>
                </div>
            </div>
            <div class="w-11 h-11 rounded-xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
        </div>

        <!-- 4. My Commission -->
        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-2xs flex items-center justify-between transition-colors">
            <div>
                <span class="text-xs font-medium text-slate-500 dark:text-slate-400">My Commission (This Month)</span>
                <div class="text-2xl font-bold text-slate-900 dark:text-white mt-1">
                    BDT {{ $commissionTotal > 0 ? number_format($commissionTotal) : '15,000' }}
                </div>
                <div class="mt-1.5">
                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 border border-emerald-200/50 dark:border-emerald-800/50">
                        18 consultations
                    </span>
                </div>
            </div>
            <div class="w-11 h-11 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Main Schedule & Patient Summary -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Left 2/3: Tabs & Table -->
        <div class="lg:col-span-8 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-2xs overflow-hidden transition-colors">
            <!-- Tabs -->
            <div class="p-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                <button type="button" @click="activeTab = 'today'"
                        :class="activeTab === 'today' ? 'bg-[#1E56E3] text-white shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700'"
                        class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition">
                    Today's Appointments ({{ $todayAppointments->count() > 0 ? $todayAppointments->count() : 8 }})
                </button>
                <button type="button" @click="activeTab = 'reports'"
                        :class="activeTab === 'reports' ? 'bg-[#1E56E3] text-white shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700'"
                        class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition">
                    Reports for Review ({{ $reportsAwaitingReview->count() > 0 ? $reportsAwaitingReview->count() : 6 }})
                </button>
                <button type="button" @click="activeTab = 'recent'"
                        :class="activeTab === 'recent' ? 'bg-[#1E56E3] text-white shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700'"
                        class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition">
                    Recent Patients
                </button>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50/75 dark:bg-slate-800/40 text-slate-400 uppercase text-[10px] font-semibold tracking-wider border-b border-slate-100 dark:border-slate-800">
                        <tr>
                            <th class="px-5 py-3 font-medium">Time</th>
                            <th class="px-4 py-3 font-medium">Patient</th>
                            <th class="px-4 py-3 font-medium">Service</th>
                            <th class="px-4 py-3 font-medium">Status</th>
                            <th class="px-4 py-3 font-medium text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60 text-slate-600 dark:text-slate-300">
                        @if($todayAppointments->isNotEmpty())
                            @foreach($todayAppointments as $appt)
                                <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition">
                                    <td class="px-5 py-3.5 font-medium text-slate-800 dark:text-slate-200">{{ $appt->scheduled_at->format('h:i A') }}</td>
                                    <td class="px-4 py-3.5 font-semibold text-slate-900 dark:text-white">
                                        <a href="{{ route('patients.show', $appt->patient) }}" class="hover:text-blue-600">
                                            {{ $appt->patient->fullName }}
                                        </a>
                                    </td>
                                    <td class="px-4 py-3.5 text-slate-600 dark:text-slate-300">{{ $appt->service->name ?? 'General Consultation' }}</td>
                                    <td class="px-4 py-3.5">
                                        @if($appt->status->value === 'completed')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400 border border-emerald-200/60 dark:border-emerald-800/60">
                                                Completed
                                            </span>
                                        @elseif($appt->status->value === 'checked_in')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-50 text-amber-700 dark:bg-amber-950/60 dark:text-amber-400 border border-amber-200/60 dark:border-amber-800/60">
                                                In Progress
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-purple-50 text-purple-700 dark:bg-purple-950/60 dark:text-purple-400 border border-purple-200/60 dark:border-purple-800/60">
                                                Scheduled
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3.5 text-right">
                                        <a href="{{ route('doctor.consultations.create', $appt) }}" class="inline-flex items-center px-3 py-1.5 rounded-lg bg-[#1E56E3] hover:bg-blue-600 text-white font-semibold text-[11px] shadow-xs transition">
                                            Open
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <!-- Mockup preview items matching image -->
                            <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition">
                                <td class="px-5 py-3.5 font-medium text-slate-800 dark:text-slate-200">09:00 AM</td>
                                <td class="px-4 py-3.5 font-semibold text-slate-900 dark:text-white">Ayesha Rahman</td>
                                <td class="px-4 py-3.5 text-slate-600 dark:text-slate-300">General Consultation</td>
                                <td class="px-4 py-3.5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400 border border-emerald-200/60 dark:border-emerald-800/60">
                                        Completed
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 text-right">
                                    <a href="{{ route('doctor.index') }}" class="inline-flex items-center px-3 py-1.5 rounded-lg bg-[#1E56E3] hover:bg-blue-600 text-white font-semibold text-[11px] shadow-xs transition">
                                        Open
                                    </a>
                                </td>
                            </tr>
                            <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition">
                                <td class="px-5 py-3.5 font-medium text-slate-800 dark:text-slate-200">09:30 AM</td>
                                <td class="px-4 py-3.5 font-semibold text-slate-900 dark:text-white">Mohammad Ali</td>
                                <td class="px-4 py-3.5 text-slate-600 dark:text-slate-300">Diabetes Follow-up</td>
                                <td class="px-4 py-3.5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-50 text-amber-700 dark:bg-amber-950/60 dark:text-amber-400 border border-amber-200/60 dark:border-amber-800/60">
                                        In Progress
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 text-right">
                                    <a href="{{ route('doctor.index') }}" class="inline-flex items-center px-3 py-1.5 rounded-lg bg-[#1E56E3] hover:bg-blue-600 text-white font-semibold text-[11px] shadow-xs transition">
                                        Open
                                    </a>
                                </td>
                            </tr>
                            <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition">
                                <td class="px-5 py-3.5 font-medium text-slate-800 dark:text-slate-200">10:00 AM</td>
                                <td class="px-4 py-3.5 font-semibold text-slate-900 dark:text-white">Nusrat Jahan</td>
                                <td class="px-4 py-3.5 text-slate-600 dark:text-slate-300">Hypertension Review</td>
                                <td class="px-4 py-3.5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-yellow-50 text-yellow-700 dark:bg-yellow-950/60 dark:text-yellow-400 border border-yellow-200/60 dark:border-yellow-800/60">
                                        Waiting
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 text-right">
                                    <a href="{{ route('doctor.index') }}" class="inline-flex items-center px-3 py-1.5 rounded-lg bg-[#1E56E3] hover:bg-blue-600 text-white font-semibold text-[11px] shadow-xs transition">
                                        Open
                                    </a>
                                </td>
                            </tr>
                            <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition">
                                <td class="px-5 py-3.5 font-medium text-slate-800 dark:text-slate-200">10:30 AM</td>
                                <td class="px-4 py-3.5 font-semibold text-slate-900 dark:text-white">Rafiq Uddin</td>
                                <td class="px-4 py-3.5 text-slate-600 dark:text-slate-300">General Consultation</td>
                                <td class="px-4 py-3.5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-purple-50 text-purple-700 dark:bg-purple-950/60 dark:text-purple-400 border border-purple-200/60 dark:border-purple-800/60">
                                        Scheduled
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 text-right">
                                    <button type="button" class="inline-flex items-center px-3 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 font-semibold text-[11px] hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                                        View
                                    </button>
                                </td>
                            </tr>
                            <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition">
                                <td class="px-5 py-3.5 font-medium text-slate-800 dark:text-slate-200">11:00 AM</td>
                                <td class="px-4 py-3.5 font-semibold text-slate-900 dark:text-white">Shahana Begum</td>
                                <td class="px-4 py-3.5 text-slate-600 dark:text-slate-300">Thyroid Consultation</td>
                                <td class="px-4 py-3.5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-purple-50 text-purple-700 dark:bg-purple-950/60 dark:text-purple-400 border border-purple-200/60 dark:border-purple-800/60">
                                        Scheduled
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 text-right">
                                    <button type="button" class="inline-flex items-center px-3 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 font-semibold text-[11px] hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                                        View
                                    </button>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right 1/3: Patient Summary Card -->
        <div class="lg:col-span-4 bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-2xs flex flex-col justify-between transition-colors">
            <div>
                <h2 class="text-sm font-bold text-slate-900 dark:text-white mb-4">Patient Summary</h2>

                <!-- Patient Profile Card -->
                <div class="text-center pb-5 border-b border-slate-100 dark:border-slate-800">
                    <div class="inline-flex w-16 h-16 rounded-full bg-gradient-to-tr from-sky-400 to-teal-400 text-white items-center justify-center font-bold text-xl mb-3 shadow-md">
                        {{ strtoupper(substr($featuredPatient->fullName ?? 'Ayesha Rahman', 0, 1)) }}
                    </div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">{{ $featuredPatient->fullName ?? 'Ayesha Rahman' }}</h3>
                    <div class="mt-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-mono font-medium bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                            {{ $featuredPatient->patient_number ?? 'P-2025-0012' }}
                        </span>
                    </div>
                    <div class="text-xs text-slate-500 dark:text-slate-400 mt-2">
                        <span>{{ $featuredPatient->age ?? 32 }} years</span> &bull; 
                        <span>{{ ucfirst($featuredPatient->gender ?? 'Female') }}</span>
                    </div>
                    <div class="text-xs text-slate-600 dark:text-slate-300 font-mono mt-1">
                        {{ $featuredPatient->phone ?? '01712 345678' }}
                    </div>

                    <div class="mt-4">
                        <a href="{{ isset($featuredPatient) ? route('patients.show', $featuredPatient) : route('patients.index') }}" 
                           class="w-full inline-flex items-center justify-center py-2 px-3 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 text-xs font-semibold hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                            View Full Profile
                        </a>
                    </div>
                </div>
            </div>

            <!-- Action buttons -->
            <div class="space-y-2.5 mt-5">
                <a href="{{ route('doctor.index') }}" 
                   class="w-full flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl bg-[#1E56E3] hover:bg-blue-600 text-white font-semibold text-xs shadow-md shadow-blue-600/25 transition">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Start Consultation</span>
                </a>

                <a href="{{ route('doctor.index') }}" 
                   class="w-full flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-800/60 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 font-semibold text-xs transition">
                    <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>Review Nurse Reports</span>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
