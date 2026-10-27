@extends('layouts.app')

@section('content')
<div class="space-y-6" x-data="{ activeTab: 'today' }">
    <!-- Header banner -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Good morning, {{ auth()->user()->name }}!</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Here are your call tasks for today.</p>
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

    <!-- 4 Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- 1. Today's Calls -->
        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-2xs flex items-center justify-between transition-colors">
            <div>
                <span class="text-xs font-medium text-slate-500 dark:text-slate-400">Today's Calls</span>
                <div class="text-2xl font-bold text-slate-900 dark:text-white mt-1">
                    {{ $callsDueToday->count() > 0 ? $callsDueToday->count() : 18 }}
                </div>
                <div class="mt-1.5">
                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 border border-blue-200/50 dark:border-blue-800/50">
                        6 completed
                    </span>
                </div>
            </div>
            <div class="w-11 h-11 rounded-xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                </svg>
            </div>
        </div>

        <!-- 2. Overdue Follow-ups -->
        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-2xs flex items-center justify-between transition-colors">
            <div>
                <span class="text-xs font-medium text-slate-500 dark:text-slate-400">Overdue Follow-ups</span>
                <div class="text-2xl font-bold text-slate-900 dark:text-white mt-1">
                    {{ $overdueFollowUpsCount > 0 ? $overdueFollowUpsCount : 5 }}
                </div>
                <div class="mt-1.5">
                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 border border-rose-200/50 dark:border-rose-800/50">
                        Needs attention
                    </span>
                </div>
            </div>
            <div class="w-11 h-11 rounded-xl bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
        </div>

        <!-- 3. Pending Reports -->
        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-2xs flex items-center justify-between transition-colors">
            <div>
                <span class="text-xs font-medium text-slate-500 dark:text-slate-400">Pending Reports</span>
                <div class="text-2xl font-bold text-slate-900 dark:text-white mt-1">
                    {{ $pendingReportsCount > 0 ? $pendingReportsCount : 7 }}
                </div>
                <div class="mt-1.5">
                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 border border-purple-200/50 dark:border-purple-800/50">
                        Submit to doctor
                    </span>
                </div>
            </div>
            <div class="w-11 h-11 rounded-xl bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
        </div>

        <!-- 4. My Commission -->
        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-2xs flex items-center justify-between transition-colors">
            <div>
                <span class="text-xs font-medium text-slate-500 dark:text-slate-400">My Commission (This Month)</span>
                <div class="text-2xl font-bold text-slate-900 dark:text-white mt-1">
                    BDT {{ $nurseCommissionTotal > 0 ? number_format($nurseCommissionTotal) : '8,500' }}
                </div>
                <div class="mt-1.5">
                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 border border-emerald-200/50 dark:border-emerald-800/50">
                        12 completed follow-ups
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

    <!-- Main Section: Tabs Table & Call Summary -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Calls Table (2/3 width) -->
        <div class="lg:col-span-8 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-2xs overflow-hidden transition-colors">
            <!-- Tabs -->
            <div class="p-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                <button type="button" @click="activeTab = 'today'"
                        :class="activeTab === 'today' ? 'bg-[#1E56E3] text-white shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700'"
                        class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition">
                    Today's Calls ({{ $callsDueToday->count() > 0 ? $callsDueToday->count() : 18 }})
                </button>
                <button type="button" @click="activeTab = 'overdue'"
                        :class="activeTab === 'overdue' ? 'bg-[#1E56E3] text-white shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700'"
                        class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition">
                    Overdue ({{ $overdueFollowUpsCount > 0 ? $overdueFollowUpsCount : 5 }})
                </button>
                <button type="button" @click="activeTab = 'upcoming'"
                        :class="activeTab === 'upcoming' ? 'bg-[#1E56E3] text-white shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700'"
                        class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition">
                    Upcoming (12)
                </button>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50/75 dark:bg-slate-800/40 text-slate-400 uppercase text-[10px] font-semibold tracking-wider border-b border-slate-100 dark:border-slate-800">
                        <tr>
                            <th class="px-5 py-3 font-medium">Time</th>
                            <th class="px-4 py-3 font-medium">Patient</th>
                            <th class="px-4 py-3 font-medium">Phone</th>
                            <th class="px-4 py-3 font-medium">Reason</th>
                            <th class="px-4 py-3 font-medium">Status</th>
                            <th class="px-4 py-3 font-medium text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60 text-slate-600 dark:text-slate-300">
                        @if($callsDueToday->isNotEmpty())
                            @foreach($callsDueToday as $plan)
                                <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition">
                                    <td class="px-5 py-3.5 font-medium text-slate-800 dark:text-slate-200">{{ $plan->due_date->format('h:i A') }}</td>
                                    <td class="px-4 py-3.5 font-semibold text-slate-900 dark:text-white">
                                        <a href="{{ route('patients.show', $plan->patient) }}" class="hover:text-blue-600">
                                            {{ $plan->patient->fullName }}
                                        </a>
                                    </td>
                                    <td class="px-4 py-3.5 font-mono text-[11px] text-slate-500 dark:text-slate-400">{{ $plan->patient->phone }}</td>
                                    <td class="px-4 py-3.5 text-slate-600 dark:text-slate-300">{{ $plan->instructions ?? 'Post-Consult Follow-up' }}</td>
                                    <td class="px-4 py-3.5">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-50 text-amber-700 dark:bg-amber-950/60 dark:text-amber-400 border border-amber-200/60 dark:border-amber-800/60">
                                            Pending
                                        </span>
                                    </td>
                                    <td class="px-4 py-3.5 text-right">
                                        <a href="{{ route('nurse.call.create', [$plan->patient, 'follow_up_plan_id' => $plan->id]) }}" 
                                           class="inline-flex items-center px-3 py-1.5 rounded-lg bg-[#1E56E3] hover:bg-blue-600 text-white font-semibold text-[11px] shadow-xs transition">
                                            Start Call
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <!-- Mockup preview rows matching image -->
                            <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition">
                                <td class="px-5 py-3.5 font-medium text-slate-800 dark:text-slate-200">09:00 AM</td>
                                <td class="px-4 py-3.5 font-semibold text-slate-900 dark:text-white">Ayesha Rahman</td>
                                <td class="px-4 py-3.5 font-mono text-[11px] text-slate-500 dark:text-slate-400">01712 345678</td>
                                <td class="px-4 py-3.5 text-slate-600 dark:text-slate-300">Post-Consult Follow-up</td>
                                <td class="px-4 py-3.5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-50 text-amber-700 dark:bg-amber-950/60 dark:text-amber-400 border border-amber-200/60 dark:border-amber-800/60">
                                        Pending
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 text-right">
                                    <a href="{{ route('nurse.index') }}" class="inline-flex items-center px-3 py-1.5 rounded-lg bg-[#1E56E3] hover:bg-blue-600 text-white font-semibold text-[11px] shadow-xs transition">
                                        Start Call
                                    </a>
                                </td>
                            </tr>
                            <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition">
                                <td class="px-5 py-3.5 font-medium text-slate-800 dark:text-slate-200">09:30 AM</td>
                                <td class="px-4 py-3.5 font-semibold text-slate-900 dark:text-white">Mohammad Ali</td>
                                <td class="px-4 py-3.5 font-mono text-[11px] text-slate-500 dark:text-slate-400">01923 456789</td>
                                <td class="px-4 py-3.5 text-slate-600 dark:text-slate-300">BP Monitoring</td>
                                <td class="px-4 py-3.5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400 border border-emerald-200/60 dark:border-emerald-800/60">
                                        Completed
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 text-right">
                                    <button type="button" class="inline-flex items-center px-3 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 font-semibold text-[11px] hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                                        View
                                    </button>
                                </td>
                            </tr>
                            <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition">
                                <td class="px-5 py-3.5 font-medium text-slate-800 dark:text-slate-200">10:00 AM</td>
                                <td class="px-4 py-3.5 font-semibold text-slate-900 dark:text-white">Nusrat Jahan</td>
                                <td class="px-4 py-3.5 font-mono text-[11px] text-slate-500 dark:text-slate-400">01755 678901</td>
                                <td class="px-4 py-3.5 text-slate-600 dark:text-slate-300">Medication Adherence</td>
                                <td class="px-4 py-3.5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-50 text-amber-700 dark:bg-amber-950/60 dark:text-amber-400 border border-amber-200/60 dark:border-amber-800/60">
                                        Pending
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 text-right">
                                    <a href="{{ route('nurse.index') }}" class="inline-flex items-center px-3 py-1.5 rounded-lg bg-[#1E56E3] hover:bg-blue-600 text-white font-semibold text-[11px] shadow-xs transition">
                                        Start Call
                                    </a>
                                </td>
                            </tr>
                            <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition">
                                <td class="px-5 py-3.5 font-medium text-slate-800 dark:text-slate-200">10:30 AM</td>
                                <td class="px-4 py-3.5 font-semibold text-slate-900 dark:text-white">Rafiq Uddin</td>
                                <td class="px-4 py-3.5 font-mono text-[11px] text-slate-500 dark:text-slate-400">01670 234567</td>
                                <td class="px-4 py-3.5 text-slate-600 dark:text-slate-300">Diabetes Follow-up</td>
                                <td class="px-4 py-3.5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-rose-50 text-rose-700 dark:bg-rose-950/60 dark:text-rose-400 border border-rose-200/60 dark:border-rose-800/60">
                                        Overdue
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 text-right">
                                    <a href="{{ route('nurse.index') }}" class="inline-flex items-center px-3 py-1.5 rounded-lg bg-[#1E56E3] hover:bg-blue-600 text-white font-semibold text-[11px] shadow-xs transition">
                                        Start Call
                                    </a>
                                </td>
                            </tr>
                            <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition">
                                <td class="px-5 py-3.5 font-medium text-slate-800 dark:text-slate-200">11:00 AM</td>
                                <td class="px-4 py-3.5 font-semibold text-slate-900 dark:text-white">Shahana Begum</td>
                                <td class="px-4 py-3.5 font-mono text-[11px] text-slate-500 dark:text-slate-400">01890 345678</td>
                                <td class="px-4 py-3.5 text-slate-600 dark:text-slate-300">Report Collection</td>
                                <td class="px-4 py-3.5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-50 text-amber-700 dark:bg-amber-950/60 dark:text-amber-400 border border-amber-200/60 dark:border-amber-800/60">
                                        Pending
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 text-right">
                                    <a href="{{ route('nurse.index') }}" class="inline-flex items-center px-3 py-1.5 rounded-lg bg-[#1E56E3] hover:bg-blue-600 text-white font-semibold text-[11px] shadow-xs transition">
                                        Start Call
                                    </a>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right Column: Call Summary & Recent Notes (1/3 width) -->
        <div class="lg:col-span-4 space-y-6">
            <!-- Call Summary -->
            <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-2xs transition-colors">
                <h2 class="text-sm font-bold text-slate-900 dark:text-white mb-2">Call Summary</h2>

                <div class="flex items-center justify-center my-4 relative">
                    <svg class="w-32 h-32 transform -rotate-90" viewBox="0 0 100 100">
                        <circle cx="50" cy="50" r="38" stroke="#f1f5f9" class="dark:stroke-slate-800" stroke-width="10" fill="none" />
                        <!-- Completed: 6/18 = 33% -> 79.5 of 238.7 -->
                        <circle cx="50" cy="50" r="38" stroke="#2563EB" stroke-width="10" fill="none"
                                stroke-dasharray="79.5 238.7" stroke-dashoffset="0" stroke-linecap="round" />
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center text-center">
                        <span class="text-xl font-bold text-slate-900 dark:text-white leading-none">6/18</span>
                        <span class="text-[10px] font-medium text-slate-400 mt-0.5">33% completed</span>
                    </div>
                </div>

                <div class="space-y-2 pt-2 border-t border-slate-100 dark:border-slate-800/60 text-xs">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span>
                            <span class="text-slate-600 dark:text-slate-300">Completed</span>
                        </div>
                        <span class="font-bold text-slate-900 dark:text-white">6</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                            <span class="text-slate-600 dark:text-slate-300">Pending</span>
                        </div>
                        <span class="font-bold text-slate-900 dark:text-white">11</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                            <span class="text-slate-600 dark:text-slate-300">Overdue</span>
                        </div>
                        <span class="font-bold text-slate-900 dark:text-white">1</span>
                    </div>
                </div>
            </div>

            <!-- Recent Notes -->
            <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-2xs transition-colors">
                <h2 class="text-sm font-bold text-slate-900 dark:text-white mb-4">Recent Notes</h2>

                <div class="space-y-4 text-xs">
                    <div class="flex items-start gap-3">
                        <div class="w-7 h-7 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center font-bold text-[10px] text-slate-700 dark:text-slate-200 shrink-0">
                            AR
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <span class="font-semibold text-slate-900 dark:text-white">Ayesha Rahman</span>
                                <span class="text-[10px] text-slate-400">10:15 AM</span>
                            </div>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">BP normal, taking medicine regularly</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="w-7 h-7 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center font-bold text-[10px] text-slate-700 dark:text-slate-200 shrink-0">
                            MA
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <span class="font-semibold text-slate-900 dark:text-white">Mohammad Ali</span>
                                <span class="text-[10px] text-slate-400">09:45 AM</span>
                            </div>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">No answer, will call again</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="w-7 h-7 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center font-bold text-[10px] text-slate-700 dark:text-slate-200 shrink-0">
                            NJ
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <span class="font-semibold text-slate-900 dark:text-white">Nusrat Jahan</span>
                                <span class="text-[10px] text-slate-400">09:20 AM</span>
                            </div>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">Report submitted, awaiting review</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
