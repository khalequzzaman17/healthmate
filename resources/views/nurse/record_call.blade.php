@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto space-y-6" x-data="{ recordVitals: true, submitReport: true, reached: true }">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Record Follow-up Call & Vitals</h1>
            <p class="text-xs text-slate-500 mt-0.5">Patient: <strong>{{ $patient->fullName }}</strong> ({{ $patient->patient_number }}) &bull; Phone: {{ $patient->phone }}</p>
        </div>
        <a href="{{ route('nurse.index') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900">&larr; Back to queue</a>
    </div>

    <form action="{{ route('nurse.call.store', $patient) }}" method="POST" class="space-y-6">
        @csrf
        @if($followUpPlan)
            <input type="hidden" name="follow_up_plan_id" value="{{ $followUpPlan->id }}">
        @endif

        <!-- Card 1: Call Details -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6">
            <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4 pb-2 border-b border-slate-100 flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-teal-50 text-teal-600 flex items-center justify-center text-xs font-bold">1</span>
                <span>Call Attempt & Outcome</span>
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Call Outcome *</label>
                    <select name="outcome" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-teal-500">
                        <option value="completed">Completed (Patient Reached)</option>
                        <option value="no_answer">No Answer</option>
                        <option value="busy">Line Busy</option>
                        <option value="callback_requested">Callback Requested</option>
                        <option value="wrong_number">Wrong Number</option>
                        <option value="declined">Patient Declined</option>
                        <option value="escalated">Escalated Urgently</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Call Duration (Seconds) *</label>
                    <input type="number" name="duration_seconds" value="180" min="0" required
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-teal-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Patient Reached? *</label>
                    <select name="reached" x-model="reached" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-teal-500">
                        <option value="1">Yes, Spoke with Patient/Family</option>
                        <option value="0">No, Could Not Reach</option>
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Call Notes</label>
                    <input type="text" name="notes" placeholder="e.g. Patient answered warmly, confirmed taking morning dose"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-teal-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Schedule Next Call</label>
                    <input type="date" name="next_follow_up_date" value="{{ now()->addDays(7)->toDateString() }}"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-teal-500">
                </div>
            </div>
        </div>

        <!-- Card 2: Structured Health Observations -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6" x-show="reached">
            <div class="flex items-center justify-between pb-2 mb-4 border-b border-slate-100">
                <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full bg-teal-50 text-teal-600 flex items-center justify-center text-xs font-bold">2</span>
                    <span>Structured Vitals & Patient Observations</span>
                </h2>
                <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-teal-700">
                    <input type="checkbox" name="record_vitals" value="1" x-model="recordVitals" class="rounded text-teal-600">
                    <span>Include Vitals</span>
                </label>
            </div>

            <div x-show="recordVitals" class="space-y-4">
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Systolic BP (mmHg)</label>
                        <input type="number" name="systolic_bp" placeholder="e.g. 130"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-teal-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Diastolic BP (mmHg)</label>
                        <input type="number" name="diastolic_bp" placeholder="e.g. 85"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-teal-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Blood Glucose (mmol/L)</label>
                        <input type="number" step="0.1" name="blood_glucose" placeholder="e.g. 6.8"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-teal-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Glucose Type</label>
                        <select name="glucose_type" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-teal-500">
                            <option value="fasting">Fasting</option>
                            <option value="random">Random</option>
                            <option value="2h_postprandial">2h Post-meal</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Temperature (°F)</label>
                        <input type="number" step="0.1" name="temperature_f" placeholder="98.6"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-teal-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Weight (kg)</label>
                        <input type="number" step="0.1" name="weight_kg" placeholder="72.0"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-teal-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Pulse Rate (bpm)</label>
                        <input type="number" name="pulse_rate" placeholder="75"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-teal-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Medication Adherence</label>
                        <select name="medication_adherence" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-teal-500">
                            <option value="good">Good (100% compliant)</option>
                            <option value="partial">Partial (Missed doses)</option>
                            <option value="poor">Poor (Stopped medication)</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Symptoms or Patient Concerns</label>
                    <textarea name="patient_concerns" rows="2" placeholder="Patient reported symptoms, side-effects, or wellness notes"
                              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-teal-500"></textarea>
                </div>

                <div class="pt-2">
                    <label class="flex items-center gap-2 cursor-pointer p-3 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-xs font-semibold">
                        <input type="checkbox" name="escalation_needed" value="1" class="rounded text-amber-600">
                        <span>Flag for Urgent Clinical Escalation (Abnormal readings or concerning symptoms)</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Card 3: Submit Health Report for Doctor Review -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6" x-show="reached">
            <div class="flex items-center justify-between pb-2 mb-4 border-b border-slate-100">
                <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full bg-teal-50 text-teal-600 flex items-center justify-center text-xs font-bold">3</span>
                    <span>Submit Health Report to Doctor Review Queue</span>
                </h2>
                <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-teal-700">
                    <input type="checkbox" name="submit_report" value="1" x-model="submitReport" class="rounded text-teal-600">
                    <span>Submit Formal Report</span>
                </label>
            </div>

            <div x-show="submitReport" class="space-y-4">
                <div class="p-3 rounded-xl bg-sky-50 border border-sky-100 text-xs text-sky-800">
                    <strong>Nurse Follow-up Commission:</strong> Submitting this formal health report completes the follow-up task and generates your BDT 50.00 commission entry.
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Report Title *</label>
                    <input type="text" name="report_title" value="Routine Follow-up Review for {{ $patient->fullName }}"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-teal-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Clinical Summary for Attending Doctor *</label>
                    <textarea name="report_summary" rows="3" placeholder="Summarize patient compliance, readings, and any requested medication review"
                              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-teal-500">Patient reached via telephone follow-up. Vital signs and adherence reviewed. No immediate distress noted.</textarea>
                </div>
            </div>
        </div>

        <div class="pt-2 flex items-center justify-end gap-3">
            <a href="{{ route('nurse.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 hover:bg-slate-50">Cancel</a>
            <button type="submit" class="px-5 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-500 text-white font-semibold text-xs shadow-sm transition">
                Save Call & Submit to Doctor
            </button>
        </div>
    </form>
</div>
@endsection
