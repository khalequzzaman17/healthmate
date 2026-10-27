@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Clinical Review: Health Report</h1>
            <p class="text-xs text-slate-500 mt-0.5">Submitted by Nurse {{ $report->nurse->name }} on {{ $report->submitted_at->format('M d, Y h:i A') }}</p>
        </div>
        <a href="{{ route('doctor.index') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900">&larr; Back to queue</a>
    </div>

    <!-- Patient & Report Overview Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 space-y-4">
        <div class="flex items-start justify-between border-b border-slate-100 pb-4">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Patient File</span>
                <div class="text-lg font-bold text-slate-900">{{ $report->patient->fullName }}</div>
                <div class="text-xs text-slate-500 mt-0.5">
                    {{ $report->patient->patient_number }} &bull; {{ ucfirst($report->patient->gender) }} &bull; Phone: {{ $report->patient->phone }}
                </div>
            </div>
            <a href="{{ route('patients.show', $report->patient) }}" target="_blank" class="text-xs font-semibold text-sky-600 hover:text-sky-700">
                View Full Patient Record &rarr;
            </a>
        </div>

        <div>
            <h2 class="text-sm font-bold text-slate-900 mb-1">{{ $report->title }}</h2>
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100 text-xs text-slate-700 leading-relaxed">
                {{ $report->summary }}
            </div>
        </div>

        <!-- Recorded Vitals from Call -->
        @if($report->call && $report->call->healthObservations->isNotEmpty())
            <div class="pt-2">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block mb-2">Nurse Recorded Vitals & Compliance</span>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    @foreach($report->call->healthObservations as $obs)
                        <div class="p-3 rounded-xl bg-sky-50/50 border border-sky-100">
                            <span class="text-[10px] text-slate-500 block uppercase font-semibold">Blood Pressure</span>
                            <span class="text-sm font-bold text-slate-900">{{ $obs->systolic_bp ? "{$obs->systolic_bp}/{$obs->diastolic_bp}" : '—' }} mmHg</span>
                        </div>
                        <div class="p-3 rounded-xl bg-sky-50/50 border border-sky-100">
                            <span class="text-[10px] text-slate-500 block uppercase font-semibold">Blood Glucose</span>
                            <span class="text-sm font-bold text-slate-900">{{ $obs->blood_glucose ?? '—' }} mmol/L</span>
                        </div>
                        <div class="p-3 rounded-xl bg-sky-50/50 border border-sky-100">
                            <span class="text-[10px] text-slate-500 block uppercase font-semibold">Temperature / Pulse</span>
                            <span class="text-sm font-bold text-slate-900">{{ $obs->temperature_f ?? '—' }}°F / {{ $obs->pulse_rate ?? '—' }}</span>
                        </div>
                        <div class="p-3 rounded-xl bg-sky-50/50 border border-sky-100">
                            <span class="text-[10px] text-slate-500 block uppercase font-semibold">Med Compliance</span>
                            <span class="text-xs font-bold text-slate-900">{{ ucfirst($obs->medication_adherence) }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <!-- Doctor Review Action Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6">
        <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4 pb-2 border-b border-slate-100">
            Doctor Clinical Assessment & Direction
        </h2>

        <form action="{{ route('doctor.reports.review', $report) }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Doctor Review Notes & Recommendations *</label>
                <textarea name="review_notes" rows="4" required placeholder="Record your clinical review findings, instructions for the nurse, or medication titration directions..."
                          class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-sky-500">{{ old('review_notes', $report->review_notes) }}</textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('doctor.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 hover:bg-slate-50">Cancel</a>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-sky-600 hover:bg-sky-500 text-white font-semibold text-xs shadow-sm transition">
                    Submit Clinical Review
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
