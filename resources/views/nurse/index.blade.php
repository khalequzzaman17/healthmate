@extends('layouts.app')

@section('content')
<div class="space-y-6" x-data="{ tab: 'due' }">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Nurse Care Desk & Call Queue</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Scheduled chronic follow-up calls, observation recording, and report submission</p>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="flex items-center gap-2 border-b border-slate-200 dark:border-slate-800 text-xs font-semibold">
        <button type="button" @click="tab = 'due'" 
                :class="tab === 'due' ? 'border-[#1E56E3] text-[#1E56E3] border-b-2 font-bold' : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-200'" 
                class="pb-3 px-3 transition flex items-center gap-1.5">
            <span>Calls Due Today</span>
            <span class="px-1.5 py-0.5 rounded-full text-[10px] bg-blue-100 text-blue-800 dark:bg-blue-950/60 dark:text-blue-400 font-bold">
                {{ $dueToday->count() }}
            </span>
        </button>
        <button type="button" @click="tab = 'overdue'" 
                :class="tab === 'overdue' ? 'border-rose-600 text-rose-600 border-b-2 font-bold' : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-200'" 
                class="pb-3 px-3 transition flex items-center gap-1.5">
            <span>Overdue Follow-ups</span>
            @if($overdue->count() > 0)
                <span class="px-1.5 py-0.5 rounded-full text-[10px] bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-400 font-bold">
                    {{ $overdue->count() }}
                </span>
            @endif
        </button>
        <button type="button" @click="tab = 'assigned'" 
                :class="tab === 'assigned' ? 'border-[#1E56E3] text-[#1E56E3] border-b-2 font-bold' : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-200'" 
                class="pb-3 px-3 transition">
            My Assigned Patients ({{ $assignedPatients->count() }})
        </button>
        <button type="button" @click="tab = 'reports'" 
                :class="tab === 'reports' ? 'border-[#1E56E3] text-[#1E56E3] border-b-2 font-bold' : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-200'" 
                class="pb-3 px-3 transition">
            Submitted Health Reports ({{ $submittedReports->count() }})
        </button>
    </div>

    <!-- TAB 1: Calls Due Today -->
    <div x-show="tab === 'due'" class="space-y-4">
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-2xs overflow-hidden transition-colors">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600 dark:text-slate-300">
                    <thead class="bg-slate-50/75 dark:bg-slate-800/40 text-[10px] text-slate-400 uppercase font-semibold border-b border-slate-100 dark:border-slate-800">
                        <tr>
                            <th class="px-5 py-3 font-medium">Patient</th>
                            <th class="px-4 py-3 font-medium">Phone</th>
                            <th class="px-4 py-3 font-medium">Due Date</th>
                            <th class="px-4 py-3 font-medium">Priority</th>
                            <th class="px-4 py-3 font-medium">Instructions</th>
                            <th class="px-4 py-3 font-medium text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60">
                        @forelse($dueToday as $plan)
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition">
                                <td class="px-5 py-3.5 font-bold text-slate-900 dark:text-white">
                                    <a href="{{ route('patients.show', $plan->patient) }}" class="hover:text-[#1E56E3]">
                                        {{ $plan->patient->fullName }}
                                    </a>
                                </td>
                                <td class="px-4 py-3.5 font-semibold text-slate-800 dark:text-slate-200 font-mono">{{ $plan->patient->phone }}</td>
                                <td class="px-4 py-3.5">{{ $plan->due_date->format('M d, Y') }}</td>
                                <td class="px-4 py-3.5">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold
                                        {{ $plan->priority === 'high' || $plan->priority === 'urgent' ? 'bg-rose-50 text-rose-700 dark:bg-rose-950/60 dark:text-rose-400' : 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300' }}">
                                        {{ ucfirst($plan->priority) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 max-w-xs truncate">{{ $plan->instructions ?? 'Check on vitals' }}</td>
                                <td class="px-4 py-3.5 text-right">
                                    <a href="{{ route('nurse.call.create', [$plan->patient, 'follow_up_plan_id' => $plan->id]) }}" 
                                       class="px-3.5 py-1.5 rounded-lg bg-[#1E56E3] hover:bg-blue-600 text-white font-semibold text-[11px] shadow-xs transition">
                                        Record Call & Vitals
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-8 text-center text-slate-400">No scheduled calls due today.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TAB 2: Overdue Follow-ups -->
    <div x-show="tab === 'overdue'" style="display: none;" class="space-y-4">
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-2xs overflow-hidden transition-colors">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600 dark:text-slate-300">
                    <thead class="bg-rose-50/60 dark:bg-rose-950/40 text-[10px] text-rose-800 dark:text-rose-300 uppercase font-semibold border-b border-rose-100 dark:border-rose-900/40">
                        <tr>
                            <th class="px-5 py-3 font-medium">Patient</th>
                            <th class="px-4 py-3 font-medium">Phone</th>
                            <th class="px-4 py-3 font-medium">Due Date</th>
                            <th class="px-4 py-3 font-medium">Instructions</th>
                            <th class="px-4 py-3 font-medium text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60">
                        @forelse($overdue as $plan)
                            <tr class="hover:bg-rose-50/20 dark:hover:bg-rose-950/20">
                                <td class="px-5 py-3.5 font-bold text-slate-900 dark:text-white">{{ $plan->patient->fullName }}</td>
                                <td class="px-4 py-3.5 font-mono">{{ $plan->patient->phone }}</td>
                                <td class="px-4 py-3.5 font-bold text-rose-600 dark:text-rose-400">{{ $plan->due_date->format('M d, Y') }} (Overdue)</td>
                                <td class="px-4 py-3.5">{{ $plan->instructions }}</td>
                                <td class="px-4 py-3.5 text-right">
                                    <a href="{{ route('nurse.call.create', [$plan->patient, 'follow_up_plan_id' => $plan->id]) }}" 
                                       class="px-3.5 py-1.5 rounded-lg bg-rose-600 hover:bg-rose-500 text-white font-semibold text-[11px] shadow-xs transition">
                                        Record Call
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-8 text-center text-slate-400">No overdue follow-up calls!</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
