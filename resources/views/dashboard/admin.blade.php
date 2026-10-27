@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header banner -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 dark:bg-blue-950/70 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800">Clinic Administration</span>
                <span class="text-xs text-slate-400 dark:text-slate-500 font-medium">{{ $activeClinic->name ?? 'Sunrise Clinic' }}</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Good morning, Admin!</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Here's what's happening at {{ $activeClinic->name ?? 'Sunrise Clinic' }} today.</p>
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
        <!-- 1. Total Patients -->
        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-2xs flex items-center justify-between transition-colors">
            <div>
                <span class="text-xs font-medium text-slate-500 dark:text-slate-400">Total Patients</span>
                <div class="text-2xl font-bold text-slate-900 dark:text-white mt-1">
                    {{ $totalPatients > 0 ? number_format($totalPatients) : '1,248' }}
                </div>
                <div class="mt-1.5">
                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 border border-emerald-200/50 dark:border-emerald-800/50">
                        +12% this month
                    </span>
                </div>
            </div>
            <div class="w-11 h-11 rounded-xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
        </div>

        <!-- 2. Today's Appointments -->
        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-2xs flex items-center justify-between transition-colors">
            <div>
                <span class="text-xs font-medium text-slate-500 dark:text-slate-400">Today's Appointments</span>
                <div class="text-2xl font-bold text-slate-900 dark:text-white mt-1">
                    {{ $todayAppointmentsCount > 0 ? $todayAppointmentsCount : '28' }}
                </div>
                <div class="mt-1.5">
                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 border border-purple-200/50 dark:border-purple-800/50">
                        +3 from yesterday
                    </span>
                </div>
            </div>
            <div class="w-11 h-11 rounded-xl bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
        </div>

        <!-- 3. Completed Consultations -->
        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-2xs flex items-center justify-between transition-colors">
            <div>
                <span class="text-xs font-medium text-slate-500 dark:text-slate-400">Completed Consultations</span>
                <div class="text-2xl font-bold text-slate-900 dark:text-white mt-1">
                    {{ $completedConsultationsCount > 0 ? $completedConsultationsCount : '22' }}
                </div>
                <div class="mt-1.5">
                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-teal-50 dark:bg-teal-950/60 text-teal-600 dark:text-teal-400 border border-teal-200/50 dark:border-teal-800/50">
                        78% completion rate
                    </span>
                </div>
            </div>
            <div class="w-11 h-11 rounded-xl bg-teal-50 dark:bg-teal-950/60 text-teal-600 dark:text-teal-400 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
        </div>

        <!-- 4. Pending Nurse Follow-ups -->
        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-2xs flex items-center justify-between transition-colors">
            <div>
                <span class="text-xs font-medium text-slate-500 dark:text-slate-400">Pending Nurse Follow-ups</span>
                <div class="text-2xl font-bold text-slate-900 dark:text-white mt-1">
                    {{ $pendingNurseFollowUpsCount > 0 ? $pendingNurseFollowUpsCount : '16' }}
                </div>
                <div class="mt-1.5">
                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 border border-rose-200/50 dark:border-rose-800/50">
                        {{ $overdueFollowUpsCount > 0 ? $overdueFollowUpsCount : 5 }} overdue
                    </span>
                </div>
            </div>
            <div class="w-11 h-11 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Analytics Row: Revenue Overview & Patient Flow -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Revenue Overview (2/3 width) -->
        <div class="lg:col-span-8 bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-2xs flex flex-col justify-between transition-colors">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-sm font-bold text-slate-900 dark:text-white">Revenue Overview</h2>
                    <div class="flex items-center gap-2.5 mt-1">
                        <span class="text-2xl font-bold text-slate-900 dark:text-white">
                            BDT {{ $totalRevenue > 0 ? number_format($totalRevenue) : '1,24,000' }}
                        </span>
                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[11px] font-semibold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/60">
                            ↗ 18% from last month
                        </span>
                    </div>
                </div>

                <div class="relative">
                    <select class="text-xs font-medium border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-1.5 bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-200 focus:outline-none">
                        <option>Last 6 months</option>
                        <option>Last 30 days</option>
                        <option>This Year</option>
                    </select>
                </div>
            </div>

            <!-- SVG Smooth Area Chart -->
            <div class="w-full mt-2">
                <svg viewBox="0 0 600 160" class="w-full h-44 overflow-visible" preserveAspectRatio="none">
                    <defs>
                        <linearGradient id="revenueGradient" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#2563EB" stop-opacity="0.25" />
                            <stop offset="100%" stop-color="#2563EB" stop-opacity="0.0" />
                        </linearGradient>
                    </defs>
                    <!-- Horizontal grid lines -->
                    <line x1="0" y1="20" x2="600" y2="20" stroke="#f1f5f9" class="dark:stroke-slate-800" stroke-width="1" />
                    <line x1="0" y1="60" x2="600" y2="60" stroke="#f1f5f9" class="dark:stroke-slate-800" stroke-width="1" />
                    <line x1="0" y1="100" x2="600" y2="100" stroke="#f1f5f9" class="dark:stroke-slate-800" stroke-width="1" />
                    <line x1="0" y1="140" x2="600" y2="140" stroke="#f1f5f9" class="dark:stroke-slate-800" stroke-width="1" />

                    <!-- Area fill -->
                    <path d="M 0,140 Q 60,110 120,125 T 240,95 T 360,75 T 480,50 T 600,30 L 600,140 L 0,140 Z" fill="url(#revenueGradient)" />
                    
                    <!-- Line path -->
                    <path d="M 0,140 Q 60,110 120,125 T 240,95 T 360,75 T 480,50 T 600,30" fill="none" stroke="#2563EB" stroke-width="3" stroke-linecap="round" />

                    <!-- Highlight points -->
                    <circle cx="120" cy="125" r="4" fill="#ffffff" stroke="#2563EB" stroke-width="2.5" />
                    <circle cx="240" cy="95" r="4" fill="#ffffff" stroke="#2563EB" stroke-width="2.5" />
                    <circle cx="360" cy="75" r="4" fill="#ffffff" stroke="#2563EB" stroke-width="2.5" />
                    <circle cx="480" cy="50" r="4" fill="#ffffff" stroke="#2563EB" stroke-width="2.5" />
                    <circle cx="600" cy="30" r="5" fill="#2563EB" stroke="#ffffff" stroke-width="2" />
                </svg>

                <!-- X Axis Labels -->
                <div class="flex justify-between text-[11px] font-medium text-slate-400 mt-2 px-1">
                    <span>Jan</span>
                    <span>Feb</span>
                    <span>Mar</span>
                    <span>Apr</span>
                    <span>May</span>
                    <span>Jun</span>
                </div>
            </div>
        </div>

        <!-- Patient Flow (1/3 width) -->
        <div class="lg:col-span-4 bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-2xs flex flex-col justify-between transition-colors">
            <h2 class="text-sm font-bold text-slate-900 dark:text-white mb-2">Patient Flow</h2>
            
            <!-- Donut chart -->
            <div class="flex items-center justify-center my-3 relative">
                <svg class="w-36 h-36 transform -rotate-90" viewBox="0 0 100 100">
                    <!-- Background circle -->
                    <circle cx="50" cy="50" r="38" stroke="#f1f5f9" class="dark:stroke-slate-800" stroke-width="12" fill="none" />
                    
                    <!-- Completed: 22/28 = 78% -> 186.2 of 238.7 -->
                    <circle cx="50" cy="50" r="38" stroke="#2563EB" stroke-width="12" fill="none"
                            stroke-dasharray="186.2 238.7" stroke-dashoffset="0" stroke-linecap="round" />
                    
                    <!-- In Progress: 4/28 = 14% -> 33.4 of 238.7 -->
                    <circle cx="50" cy="50" r="38" stroke="#06B6D4" stroke-width="12" fill="none"
                            stroke-dasharray="33.4 238.7" stroke-dashoffset="-188" stroke-linecap="round" />
                    
                    <!-- Waiting: 2/28 = 7% -> 16.7 of 238.7 -->
                    <circle cx="50" cy="50" r="38" stroke="#F59E0B" stroke-width="12" fill="none"
                            stroke-dasharray="16.7 238.7" stroke-dashoffset="-223" stroke-linecap="round" />
                </svg>
                <!-- Centered Counter -->
                <div class="absolute inset-0 flex flex-col items-center justify-center text-center">
                    <span class="text-2xl font-bold text-slate-900 dark:text-white leading-none">
                        {{ $todayAppointmentsCount > 0 ? $todayAppointmentsCount : '28' }}
                    </span>
                    <span class="text-[10px] font-medium text-slate-400 mt-0.5">Today</span>
                </div>
            </div>

            <!-- Legend with counts -->
            <div class="space-y-2 pt-2 border-t border-slate-100 dark:border-slate-800/60 text-xs">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span>
                        <span class="text-slate-600 dark:text-slate-300">Completed</span>
                    </div>
                    <span class="font-bold text-slate-900 dark:text-white">{{ $patientFlow['completed'] ?? 22 }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-cyan-500"></span>
                        <span class="text-slate-600 dark:text-slate-300">In Progress</span>
                    </div>
                    <span class="font-bold text-slate-900 dark:text-white">{{ $patientFlow['in_progress'] ?? 4 }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                        <span class="text-slate-600 dark:text-slate-300">Waiting</span>
                    </div>
                    <span class="font-bold text-slate-900 dark:text-white">{{ $patientFlow['waiting'] ?? 2 }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                        <span class="text-slate-600 dark:text-slate-300">Cancelled</span>
                    </div>
                    <span class="font-bold text-slate-900 dark:text-white">{{ $patientFlow['cancelled'] ?? 0 }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Section: Recent Appointments & Quick Actions -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Recent Appointments (2/3 width) -->
        <div class="lg:col-span-8 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-2xs overflow-hidden transition-colors">
            <div class="px-6 py-4.5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                <h2 class="text-sm font-bold text-slate-900 dark:text-white">Recent Appointments</h2>
                <a href="{{ route('appointments.index') }}" class="text-xs font-semibold text-blue-600 dark:text-blue-400 hover:underline">View All</a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50/75 dark:bg-slate-800/40 text-slate-400 uppercase text-[10px] font-semibold tracking-wider border-b border-slate-100 dark:border-slate-800">
                        <tr>
                            <th class="px-6 py-3 font-medium">Time</th>
                            <th class="px-4 py-3 font-medium">Patient</th>
                            <th class="px-4 py-3 font-medium">Doctor</th>
                            <th class="px-4 py-3 font-medium">Service</th>
                            <th class="px-4 py-3 font-medium">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60 text-slate-600 dark:text-slate-300">
                        @if(isset($recentAppointments) && $recentAppointments->isNotEmpty())
                            @foreach($recentAppointments as $appt)
                                <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition">
                                    <td class="px-6 py-3.5 font-medium text-slate-800 dark:text-slate-200">{{ $appt->scheduled_at->format('h:i A') }}</td>
                                    <td class="px-4 py-3.5 font-semibold text-slate-900 dark:text-white">
                                        <a href="{{ route('patients.show', $appt->patient) }}" class="hover:text-blue-600">
                                            {{ $appt->patient->fullName }}
                                        </a>
                                    </td>
                                    <td class="px-4 py-3.5 text-slate-600 dark:text-slate-300">Dr. {{ $appt->doctor->name }}</td>
                                    <td class="px-4 py-3.5 text-slate-500 dark:text-slate-400">{{ $appt->service->name ?? 'General Consultation' }}</td>
                                    <td class="px-4 py-3.5">
                                        @if($appt->status->value === 'completed')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400 border border-emerald-200/60 dark:border-emerald-800/60">
                                                Completed
                                            </span>
                                        @elseif($appt->status->value === 'checked_in')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-50 text-amber-700 dark:bg-amber-950/60 dark:text-amber-400 border border-amber-200/60 dark:border-amber-800/60">
                                                In Progress
                                            </span>
                                        @elseif($appt->status->value === 'scheduled')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-yellow-50 text-yellow-700 dark:bg-yellow-950/60 dark:text-yellow-400 border border-yellow-200/60 dark:border-yellow-800/60">
                                                Waiting
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-purple-50 text-purple-700 dark:bg-purple-950/60 dark:text-purple-400 border border-purple-200/60 dark:border-purple-800/60">
                                                Scheduled
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <!-- Default design preview items from mockup -->
                            <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition">
                                <td class="px-6 py-3.5 font-medium text-slate-800 dark:text-slate-200">09:00 AM</td>
                                <td class="px-4 py-3.5 font-semibold text-slate-900 dark:text-white">Ayesha Rahman</td>
                                <td class="px-4 py-3.5 text-slate-600 dark:text-slate-300">Dr. Tanvir Hasan</td>
                                <td class="px-4 py-3.5 text-slate-500 dark:text-slate-400">General Consultation</td>
                                <td class="px-4 py-3.5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400 border border-emerald-200/60 dark:border-emerald-800/60">
                                        Completed
                                    </span>
                                </td>
                            </tr>
                            <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition">
                                <td class="px-6 py-3.5 font-medium text-slate-800 dark:text-slate-200">09:30 AM</td>
                                <td class="px-4 py-3.5 font-semibold text-slate-900 dark:text-white">Mohammad Ali</td>
                                <td class="px-4 py-3.5 text-slate-600 dark:text-slate-300">Dr. Samina Akter</td>
                                <td class="px-4 py-3.5 text-slate-500 dark:text-slate-400">Follow-up</td>
                                <td class="px-4 py-3.5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-50 text-amber-700 dark:bg-amber-950/60 dark:text-amber-400 border border-amber-200/60 dark:border-amber-800/60">
                                        In Progress
                                    </span>
                                </td>
                            </tr>
                            <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition">
                                <td class="px-6 py-3.5 font-medium text-slate-800 dark:text-slate-200">10:00 AM</td>
                                <td class="px-4 py-3.5 font-semibold text-slate-900 dark:text-white">Nusrat Jahan</td>
                                <td class="px-4 py-3.5 text-slate-600 dark:text-slate-300">Dr. Tanvir Hasan</td>
                                <td class="px-4 py-3.5 text-slate-500 dark:text-slate-400">Diabetes Consultation</td>
                                <td class="px-4 py-3.5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-yellow-50 text-yellow-700 dark:bg-yellow-950/60 dark:text-yellow-400 border border-yellow-200/60 dark:border-yellow-800/60">
                                        Waiting
                                    </span>
                                </td>
                            </tr>
                            <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition">
                                <td class="px-6 py-3.5 font-medium text-slate-800 dark:text-slate-200">10:30 AM</td>
                                <td class="px-4 py-3.5 font-semibold text-slate-900 dark:text-white">Rafiq Uddin</td>
                                <td class="px-4 py-3.5 text-slate-600 dark:text-slate-300">Dr. Samina Akter</td>
                                <td class="px-4 py-3.5 text-slate-500 dark:text-slate-400">Hypertension Review</td>
                                <td class="px-4 py-3.5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-purple-50 text-purple-700 dark:bg-purple-950/60 dark:text-purple-400 border border-purple-200/60 dark:border-purple-800/60">
                                        Scheduled
                                    </span>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Quick Actions (1/3 width) -->
        <div class="lg:col-span-4 bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-2xs flex flex-col justify-between transition-colors">
            <h2 class="text-sm font-bold text-slate-900 dark:text-white mb-4">Quick Actions</h2>
            
            <div class="space-y-3">
                <!-- Register Patient (Solid Primary Blue) -->
                <a href="{{ route('patients.create') }}" 
                   class="w-full flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl bg-[#1E56E3] hover:bg-blue-600 text-white font-semibold text-xs shadow-md shadow-blue-600/25 transition">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                    <span>Register Patient</span>
                </a>

                <!-- Book Appointment -->
                <a href="{{ route('appointments.create') }}" 
                   class="w-full flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-800/60 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 font-semibold text-xs transition">
                    <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span>Book Appointment</span>
                </a>

                <!-- Create Invoice -->
                <a href="{{ route('billing.create') }}" 
                   class="w-full flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-800/60 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 font-semibold text-xs transition">
                    <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>Create Invoice</span>
                </a>

                <!-- View Reports -->
                <a href="{{ route('doctor.index') }}" 
                   class="w-full flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-800/60 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 font-semibold text-xs transition">
                    <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    <span>View Reports</span>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
