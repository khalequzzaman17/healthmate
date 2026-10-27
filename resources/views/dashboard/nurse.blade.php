@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Nurse Care Desk</h1>
            <p class="text-xs text-slate-500 mt-0.5">Welcome, Nurse {{ auth()->user()->name }} &bull; Scheduled follow-up calls, patient observations & vitals</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('nurse.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-sky-600 hover:bg-sky-500 text-white text-xs font-semibold shadow-sm transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                <span>Open Call Queue</span>
            </a>
        </div>
    </div>

    <!-- Nurse Metrics -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Follow-up Calls Due</span>
                <div class="text-2xl font-bold text-slate-900 mt-1">{{ $callsDueToday->count() }}</div>
                <div class="text-[11px] text-rose-600 font-medium mt-1">{{ $overdueFollowUpsCount }} overdue follow-ups</div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Assigned Chronic Care</span>
                <div class="text-2xl font-bold text-slate-900 mt-1">{{ $assignedPatientsCount }}</div>
                <div class="text-[11px] text-sky-600 font-medium mt-1">Active assigned patients</div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Earned Follow-up Commissions</span>
                <div class="text-2xl font-bold text-emerald-600 mt-1">BDT {{ number_format($nurseCommissionTotal, 2) }}</div>
                <div class="text-[11px] text-slate-500 font-medium mt-1">Approved & Payable</div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
        </div>
    </div>

    <!-- Active Tasks Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Calls Due Queue -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Scheduled Follow-up Calls</h2>
                    <p class="text-xs text-slate-500">Reach out to patients and record vitals / adherence</p>
                </div>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-sky-50 text-sky-700">
                    {{ $callsDueToday->count() }} Due
                </span>
            </div>

            <div class="divide-y divide-slate-100">
                @forelse($callsDueToday as $plan)
                    <div class="p-4 flex items-center justify-between hover:bg-slate-50/50 transition">
                        <div>
                            <div class="text-sm font-bold text-slate-900">{{ $plan->patient->fullName }}</div>
                            <div class="text-xs text-slate-500 mt-0.5">
                                Phone: <strong>{{ $plan->patient->phone }}</strong> &bull; Due: {{ $plan->due_date->format('M d') }}
                            </div>
                            <div class="text-xs text-slate-600 mt-1 italic">{{ $plan->instructions ?? 'Routine check-in' }}</div>
                        </div>
                        <div>
                            <a href="{{ route('nurse.call.create', [$plan->patient, 'follow_up_plan_id' => $plan->id]) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-teal-600 hover:bg-teal-500 text-white text-xs font-semibold shadow-xs">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                                <span>Record Call</span>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-slate-400 text-xs">No follow-up calls due today.</div>
                @endforelse
            </div>
        </div>

        <!-- Submitted Health Reports Status -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Your Submitted Reports</h2>
                    <p class="text-xs text-slate-500">Doctor review status for your health reports</p>
                </div>
            </div>

            <div class="divide-y divide-slate-100">
                @forelse($submittedReports as $rep)
                    <div class="p-4 flex items-center justify-between hover:bg-slate-50/50 transition">
                        <div>
                            <div class="text-xs font-bold text-slate-900">{{ $rep->title }}</div>
                            <div class="text-xs text-slate-500 mt-0.5">
                                Patient: {{ $rep->patient->fullName }} &bull; Submitted {{ $rep->submitted_at->diffForHumans() }}
                            </div>
                            @if($rep->reviewerDoctor)
                                <div class="text-[11px] text-emerald-600 font-medium mt-1">
                                    Reviewed by Dr. {{ $rep->reviewerDoctor->name }}: "{{ $rep->review_notes }}"
                                </div>
                            @endif
                        </div>
                        <div>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold
                                {{ $rep->status->value === 'reviewed' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                                {{ $rep->status->label() }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-slate-400 text-xs">No submitted reports yet.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
