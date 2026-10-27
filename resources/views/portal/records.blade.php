@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">My Medical Records</h1>
            <p class="text-xs text-slate-500 mt-0.5">Diagnoses, consultation notes, and health observations history</p>
        </div>
        <a href="{{ route('portal.dashboard') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900">&larr; Back to portal</a>
    </div>

    <!-- Conditions & Allergies Card -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5">
            <h2 class="text-xs font-bold uppercase tracking-wider text-rose-800 mb-2">My Allergies</h2>
            <div class="space-y-1">
                @forelse($patient->allergies as $allergy)
                    <div class="text-xs text-slate-800 font-medium">
                        &bull; {{ $allergy->allergen }} (Severity: {{ $allergy->severity }})
                    </div>
                @empty
                    <div class="text-xs text-slate-400">No allergies listed.</div>
                @endforelse
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5">
            <h2 class="text-xs font-bold uppercase tracking-wider text-amber-800 mb-2">My Chronic Conditions</h2>
            <div class="space-y-1">
                @forelse($patient->conditions as $cond)
                    <div class="text-xs text-slate-800 font-medium">
                        &bull; {{ $cond->condition_name }}
                    </div>
                @empty
                    <div class="text-xs text-slate-400">No chronic conditions listed.</div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Past Consultations & Doctor Notes -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 space-y-4">
        <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider pb-2 border-b border-slate-100">
            Consultation History & Doctor Advice
        </h2>

        <div class="space-y-4">
            @forelse($patient->consultations as $consult)
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-100 space-y-2">
                    <div class="flex items-center justify-between text-xs font-bold text-slate-900">
                        <span>Consultation with Dr. {{ $consult->doctor->name }}</span>
                        <span class="text-slate-500 font-normal">{{ $consult->created_at->format('M d, Y') }}</span>
                    </div>

                    @if($consult->advice)
                        <div class="text-xs text-slate-700 bg-white p-3 rounded-lg border border-slate-200/60">
                            <strong>Doctor's Advice:</strong> {{ $consult->advice }}
                        </div>
                    @endif

                    @if($consult->diagnoses->isNotEmpty())
                        <div class="flex flex-wrap gap-1.5 pt-1">
                            @foreach($consult->diagnoses as $diag)
                                <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-sky-50 text-sky-800 border border-sky-100">
                                    {{ $diag->diagnosis_name }}
                                </span>
                            @endforeach
                        </div>
                    @endif
                </div>
            @empty
                <div class="py-6 text-center text-xs text-slate-400">No past consultations recorded.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
