@extends('layouts.app')

@section('content')
<div class="space-y-6" x-data="{ tab: 'due' }">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Nurse Care Desk & Call Queue</h1>
            <p class="text-xs text-slate-500 mt-0.5">Scheduled chronic follow-up calls, observation recording, and report submission</p>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="flex items-center gap-2 border-b border-slate-200 text-xs font-semibold">
        <button @click="tab = 'due'" :class="tab === 'due' ? 'border-sky-600 text-sky-600 border-b-2 font-bold' : 'text-slate-500 hover:text-slate-800'" class="pb-3 px-3 transition flex items-center gap-1.5">
            <span>Calls Due Today</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-sky-100 text-sky-800 font-bold">{{ $dueToday->count() }}</span>
        </button>
        <button @click="tab = 'overdue'" :class="tab === 'overdue' ? 'border-rose-600 text-rose-600 border-b-2 font-bold' : 'text-slate-500 hover:text-slate-800'" class="pb-3 px-3 transition flex items-center gap-1.5">
            <span>Overdue Follow-ups</span>
            @if($overdue->count() > 0)
                <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-rose-100 text-rose-800 font-bold">{{ $overdue->count() }}</span>
            @endif
        </button>
        <button @click="tab = 'assigned'" :class="tab === 'assigned' ? 'border-sky-600 text-sky-600 border-b-2 font-bold' : 'text-slate-500 hover:text-slate-800'" class="pb-3 px-3 transition">
            My Assigned Patients ({{ $assignedPatients->count() }})
        </button>
        <button @click="tab = 'reports'" :class="tab === 'reports' ? 'border-sky-600 text-sky-600 border-b-2 font-bold' : 'text-slate-500 hover:text-slate-800'" class="pb-3 px-3 transition">
            Submitted Health Reports ({{ $submittedReports->count() }})
        </button>
    </div>

    <!-- TAB 1: Calls Due Today -->
    <div x-show="tab === 'due'" class="space-y-4">
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-[10px] text-slate-500 uppercase font-semibold border-b border-slate-100">
                        <tr>
                            <th class="px-5 py-3">Patient</th>
                            <th class="px-4 py-3">Phone</th>
                            <th class="px-4 py-3">Due Date</th>
                            <th class="px-4 py-3">Priority</th>
                            <th class="px-4 py-3">Instructions</th>
                            <th class="px-4 py-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($dueToday as $plan)
                            <tr class="hover:bg-slate-50/50">
                                <td class="px-5 py-3.5 font-bold text-slate-900">
                                    <a href="{{ route('patients.show', $plan->patient) }}" class="hover:text-sky-600">
                                        {{ $plan->patient->fullName }}
                                    </a>
                                </td>
                                <td class="px-4 py-3.5 font-semibold text-slate-800">{{ $plan->patient->phone }}</td>
                                <td class="px-4 py-3.5">{{ $plan->due_date->format('M d, Y') }}</td>
                                <td class="px-4 py-3.5">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold
                                        {{ $plan->priority === 'high' || $plan->priority === 'urgent' ? 'bg-rose-50 text-rose-700' : 'bg-slate-100 text-slate-700' }}">
                                        {{ ucfirst($plan->priority) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 max-w-xs truncate">{{ $plan->instructions ?? 'Check on vitals' }}</td>
                                <td class="px-4 py-3.5 text-right">
                                    <a href="{{ route('nurse.call.create', [$plan->patient, 'follow_up_plan_id' => $plan->id]) }}" class="px-3 py-1.5 rounded-lg bg-teal-600 hover:bg-teal-500 text-white font-semibold text-[11px] shadow-xs">
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
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-rose-50/60 text-[10px] text-rose-800 uppercase font-semibold border-b border-rose-100">
                        <tr>
                            <th class="px-5 py-3">Patient</th>
                            <th class="px-4 py-3">Phone</th>
                            <th class="px-4 py-3">Due Date</th>
                            <th class="px-4 py-3">Instructions</th>
                            <th class="px-4 py-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($overdue as $plan)
                            <tr class="hover:bg-rose-50/20">
                                <td class="px-5 py-3.5 font-bold text-slate-900">{{ $plan->patient->fullName }}</td>
                                <td class="px-4 py-3.5">{{ $plan->patient->phone }}</td>
                                <td class="px-4 py-3.5 font-bold text-rose-600">{{ $plan->due_date->format('M d, Y') }} (Overdue)</td>
                                <td class="px-4 py-3.5">{{ $plan->instructions }}</td>
                                <td class="px-4 py-3.5 text-right">
                                    <a href="{{ route('nurse.call.create', [$plan->patient, 'follow_up_plan_id' => $plan->id]) }}" class="px-3 py-1.5 rounded-lg bg-rose-600 hover:bg-rose-500 text-white font-semibold text-[11px]">
                                        Execute Overdue Call
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-8 text-center text-slate-400">No overdue follow-up tasks!</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TAB 3: Assigned Patients -->
    <div x-show="tab === 'assigned'" style="display: none;" class="space-y-4">
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-[10px] text-slate-500 uppercase font-semibold border-b border-slate-100">
                        <tr>
                            <th class="px-5 py-3">Patient ID</th>
                            <th class="px-4 py-3">Patient Name</th>
                            <th class="px-4 py-3">Phone</th>
                            <th class="px-4 py-3">Gender / Age</th>
                            <th class="px-4 py-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($assignedPatients as $assignment)
                            <tr class="hover:bg-slate-50/50">
                                <td class="px-5 py-3.5 font-mono font-semibold text-sky-700">{{ $assignment->patient->patient_number }}</td>
                                <td class="px-4 py-3.5 font-bold text-slate-900">{{ $assignment->patient->fullName }}</td>
                                <td class="px-4 py-3.5">{{ $assignment->patient->phone }}</td>
                                <td class="px-4 py-3.5">{{ ucfirst($assignment->patient->gender) }} {{ $assignment->patient->age ? "({$assignment->patient->age} yrs)" : '' }}</td>
                                <td class="px-4 py-3.5 text-right space-x-2">
                                    <a href="{{ route('nurse.call.create', $assignment->patient) }}" class="px-2.5 py-1.5 rounded-lg bg-teal-50 text-teal-700 hover:bg-teal-100 font-semibold text-[11px]">
                                        Log Call
                                    </a>
                                    <a href="{{ route('patients.show', $assignment->patient) }}" class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-[11px]">
                                        View Timeline
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-8 text-center text-slate-400">No patients assigned to you currently.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TAB 4: Submitted Health Reports -->
    <div x-show="tab === 'reports'" style="display: none;" class="space-y-4">
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-[10px] text-slate-500 uppercase font-semibold border-b border-slate-100">
                        <tr>
                            <th class="px-5 py-3">Patient</th>
                            <th class="px-4 py-3">Report Title</th>
                            <th class="px-4 py-3">Submitted At</th>
                            <th class="px-4 py-3">Doctor Review</th>
                            <th class="px-4 py-3 text-right">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($submittedReports as $rep)
                            <tr class="hover:bg-slate-50/50">
                                <td class="px-5 py-3.5 font-bold text-slate-900">{{ $rep->patient->fullName }}</td>
                                <td class="px-4 py-3.5 font-semibold text-slate-800">{{ $rep->title }}</td>
                                <td class="px-4 py-3.5 text-slate-500">{{ $rep->submitted_at->format('M d, Y h:i A') }}</td>
                                <td class="px-4 py-3.5">
                                    @if($rep->reviewerDoctor)
                                        <div class="text-xs text-emerald-700 font-medium">Reviewed by Dr. {{ $rep->reviewerDoctor->name }}</div>
                                        <div class="text-[11px] text-slate-500 italic">"{{ $rep->review_notes }}"</div>
                                    @else
                                        <span class="text-amber-600 font-medium">Awaiting doctor review</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5 text-right">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold
                                        {{ $rep->status->value === 'reviewed' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                                        {{ $rep->status->label() }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-8 text-center text-slate-400">No reports submitted yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
