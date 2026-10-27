@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto space-y-6" x-data="{ recordVitals: true, submitReport: true, reached: true }">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Record Call Outcome</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Log patient communication, chronic follow-up observations and vitals.</p>
        </div>
        <a href="{{ route('nurse.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-900 dark:hover:text-white transition">
            &larr; Back to Call Queue
        </a>
    </div>

    <!-- Patient Summary Banner (matching mockup) -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-4 shadow-2xs flex flex-wrap items-center justify-between gap-4 transition-colors">
        <div class="flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-full bg-gradient-to-tr from-sky-400 to-teal-400 text-white flex items-center justify-center font-bold text-sm shadow-xs shrink-0">
                {{ strtoupper(substr($patient->first_name, 0, 1)) }}{{ strtoupper(substr($patient->last_name ?? '', 0, 1)) }}
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <span class="font-bold text-slate-900 dark:text-white text-sm">{{ $patient->fullName }}</span>
                    <span class="font-mono px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                        {{ $patient->patient_number }}
                    </span>
                </div>
                <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400 mt-1">
                    <span>&#128222; {{ $patient->phone }}</span>
                    <span>&bull;</span>
                    <span>{{ $patient->age ? "{$patient->age} years" : '32 years' }}</span>
                    <span>&bull;</span>
                    <span class="text-blue-600 dark:text-blue-400 font-medium">{{ $followUpPlan->instructions ?? 'Diabetes Follow-up' }}</span>
                </div>
            </div>
        </div>

        @if($patient->allergies->isNotEmpty())
            <span class="px-2 py-1 rounded-lg text-xs font-semibold bg-rose-50 text-rose-700 dark:bg-rose-950/60 dark:text-rose-400 border border-rose-200/60 dark:border-rose-800/60">
                Allergic: {{ $patient->allergies->first()->allergen }}
            </span>
        @endif
    </div>

    <!-- Main Call Outcome Form -->
    <form action="{{ route('nurse.call.store', $patient) }}" method="POST" class="space-y-6">
        @csrf
        @if($followUpPlan)
            <input type="hidden" name="follow_up_plan_id" value="{{ $followUpPlan->id }}">
        @endif

        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-6 shadow-2xs space-y-4 transition-colors">
            <!-- Row 1: Outcome, Start Time, End Time -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Call Outcome *</label>
                    <select name="outcome" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-[#1E56E3]">
                        <option value="completed" selected>Completed</option>
                        <option value="no_answer">No Answer</option>
                        <option value="busy">Line Busy</option>
                        <option value="callback_requested">Callback Requested</option>
                        <option value="wrong_number">Wrong Number</option>
                        <option value="declined">Patient Declined</option>
                        <option value="escalated">Escalated Urgently</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Call Start Time</label>
                    <input type="time" name="start_time" value="10:00"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-[#1E56E3]">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Call End Time</label>
                    <input type="time" name="end_time" value="10:06"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-[#1E56E3]">
                </div>
            </div>

            <!-- Hidden duration calculation for model -->
            <input type="hidden" name="duration_seconds" value="360">
            <input type="hidden" name="reached" value="1">

            <!-- Row 2: Call Notes (Textarea matching the image) -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Call Notes *</label>
                <textarea name="notes" rows="3" required placeholder="Patient is feeling better, BP normal. Taking medicines regularly."
                          class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-[#1E56E3]">Patient is feeling better, BP normal. Taking medicines regularly.</textarea>
            </div>

            <!-- Optional Structured Observations Toggle -->
            <div class="pt-2 border-t border-slate-100 dark:border-slate-800">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold text-slate-800 dark:text-slate-200">Patient Vitals & Adherence</span>
                    <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-blue-600 dark:text-blue-400">
                        <input type="checkbox" name="record_vitals" value="1" x-model="recordVitals" class="rounded text-[#1E56E3]">
                        <span>Include Vitals</span>
                    </label>
                </div>

                <div x-show="recordVitals" class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                    <div>
                        <label class="block text-[11px] text-slate-500 mb-1">Systolic BP (mmHg)</label>
                        <input type="number" name="systolic_bp" value="120" placeholder="120"
                               class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
                    </div>
                    <div>
                        <label class="block text-[11px] text-slate-500 mb-1">Diastolic BP (mmHg)</label>
                        <input type="number" name="diastolic_bp" value="80" placeholder="80"
                               class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
                    </div>
                    <div>
                        <label class="block text-[11px] text-slate-500 mb-1">Blood Sugar (mmol/L)</label>
                        <input type="number" step="0.1" name="blood_glucose" value="6.5" placeholder="6.5"
                               class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
                    </div>
                    <div>
                        <label class="block text-[11px] text-slate-500 mb-1">Next Follow-up</label>
                        <input type="date" name="next_follow_up_date" value="{{ now()->addDays(7)->toDateString() }}"
                               class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('nurse.index') }}" 
               class="px-5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/60 transition">
                Cancel
            </a>
            <button type="submit" 
                    class="px-6 py-2.5 rounded-xl bg-[#1E56E3] hover:bg-blue-600 text-white font-semibold text-xs shadow-md shadow-blue-600/25 transition">
                Save & Complete Call
            </button>
        </div>
    </form>
</div>
@endsection
