@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="{
    diagnoses: [
        { diagnosis_name: '', diagnosis_code: '', type: 'primary' }
    ],
    items: [
        { medicine_name: '', strength: '500mg', dosage_form: 'tablet', dosage_instructions: '1+0+1', duration_days: 7, special_instructions: 'After meals' }
    ],
    scheduleFollowUp: false,
    addDiagnosis() {
        this.diagnoses.push({ diagnosis_name: '', diagnosis_code: '', type: 'secondary' });
    },
    removeDiagnosis(index) {
        if (this.diagnoses.length > 1) {
            this.diagnoses.splice(index, 1);
        }
    },
    addItem() {
        this.items.push({ medicine_name: '', strength: '', dosage_form: 'tablet', dosage_instructions: '1+0+0', duration_days: 7, special_instructions: 'After meals' });
    },
    removeItem(index) {
        if (this.items.length > 1) {
            this.items.splice(index, 1);
        }
    }
}">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Clinical Consultation Desk</h1>
            <p class="text-xs text-slate-500 mt-0.5">
                Patient: <strong>{{ $appointment->patient->fullName }}</strong> ({{ $appointment->patient->patient_number }}) &bull; Attending: Dr. {{ auth()->user()->name }}
            </p>
        </div>
        <a href="{{ route('doctor.index') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900">&larr; Back to schedule</a>
    </div>

    <!-- Patient Alerts Header -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-sky-50 text-sky-700 flex items-center justify-center font-bold text-sm">
                    {{ ucfirst(substr($appointment->patient->gender, 0, 1)) }}
                </div>
                <div>
                    <div class="text-sm font-bold text-slate-900">{{ $appointment->patient->fullName }}</div>
                    <div class="text-xs text-slate-500">
                        {{ ucfirst($appointment->patient->gender) }} &bull; {{ $appointment->patient->age ? "{$appointment->patient->age} yrs" : 'Age not recorded' }} &bull; Blood: <strong>{{ $appointment->patient->blood_group ?? 'Unknown' }}</strong>
                    </div>
                </div>
            </div>

            <!-- Allergies & Conditions -->
            <div class="flex flex-wrap items-center gap-2">
                @if($appointment->patient->allergies->isNotEmpty())
                    <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                        Allergies: {{ $appointment->patient->allergies->pluck('allergen')->join(', ') }}
                    </span>
                @endif
                @if($appointment->patient->conditions->isNotEmpty())
                    <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200">
                        Conditions: {{ $appointment->patient->conditions->pluck('condition_name')->join(', ') }}
                    </span>
                @endif
            </div>
        </div>
    </div>

    <!-- Consultation Form -->
    <form action="{{ route('doctor.consultations.store', $appointment) }}" method="POST" class="space-y-6">
        @csrf

        <!-- 1. Clinical Examination -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 space-y-4">
            <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider pb-2 border-b border-slate-100 flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-sky-50 text-sky-600 flex items-center justify-center text-xs font-bold">1</span>
                <span>Clinical Notes & Examination</span>
            </h2>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Chief Complaints / Presenting Symptoms *</label>
                <textarea name="chief_complaints" rows="2" required placeholder="e.g. Headache for 3 days, elevated morning blood sugar, fatigue"
                          class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-sky-500">{{ old('chief_complaints', $appointment->notes) }}</textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Physical Examination Findings</label>
                    <textarea name="examination_findings" rows="3" placeholder="e.g. BP 130/80, S1 S2 heard, Chest clear, No pedal edema"
                              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-sky-500"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Doctor Clinical Observations</label>
                    <textarea name="clinical_notes" rows="3" placeholder="e.g. Vitals stable, advised diet control and lifestyle modifications"
                              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-sky-500"></textarea>
                </div>
            </div>
        </div>

        <!-- 2. Diagnoses (Dynamic Builder) -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 space-y-4">
            <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full bg-sky-50 text-sky-600 flex items-center justify-center text-xs font-bold">2</span>
                    <span>Diagnoses</span>
                </h2>
                <button type="button" @click="addDiagnosis()" class="px-2.5 py-1 rounded-lg bg-sky-50 text-sky-700 hover:bg-sky-100 font-semibold text-xs">
                    + Add Diagnosis
                </button>
            </div>

            <div class="space-y-3">
                <template x-for="(diag, index) in diagnoses" :key="index">
                    <div class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 border border-slate-100">
                        <div class="flex-1">
                            <input type="text" :name="'diagnoses[' + index + '][diagnosis_name]'" x-model="diag.diagnosis_name" required placeholder="Diagnosis name (e.g. Type 2 Diabetes)"
                                   class="w-full px-3 py-1.5 rounded-lg border border-slate-200 text-xs focus:ring-2 focus:ring-sky-500 bg-white">
                        </div>
                        <div class="w-32">
                            <input type="text" :name="'diagnoses[' + index + '][diagnosis_code]'" x-model="diag.diagnosis_code" placeholder="Code (e.g. E11)"
                                   class="w-full px-3 py-1.5 rounded-lg border border-slate-200 text-xs focus:ring-2 focus:ring-sky-500 bg-white">
                        </div>
                        <div class="w-28">
                            <select :name="'diagnoses[' + index + '][type]'" x-model="diag.type" class="w-full px-2 py-1.5 rounded-lg border border-slate-200 text-xs bg-white">
                                <option value="primary">Primary</option>
                                <option value="secondary">Secondary</option>
                            </select>
                        </div>
                        <button type="button" @click="removeDiagnosis(index)" class="p-1.5 text-slate-400 hover:text-rose-600">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                        </button>
                    </div>
                </template>
            </div>
        </div>

        <!-- 3. Prescriptions (Dynamic Medicine Items Builder) -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 space-y-4">
            <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full bg-sky-50 text-sky-600 flex items-center justify-center text-xs font-bold">3</span>
                    <span>Prescription Items (Rx)</span>
                </h2>
                <button type="button" @click="addItem()" class="px-2.5 py-1 rounded-lg bg-teal-50 text-teal-700 hover:bg-teal-100 font-semibold text-xs">
                    + Add Medicine
                </button>
            </div>

            <div class="space-y-3">
                <template x-for="(item, idx) in items" :key="idx">
                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100 space-y-2">
                        <div class="grid grid-cols-1 sm:grid-cols-4 gap-2">
                            <div class="sm:col-span-2">
                                <input type="text" :name="'prescription_items[' + idx + '][medicine_name]'" x-model="item.medicine_name" required placeholder="Medicine brand/generic name (e.g. Tab. Metformin)"
                                       class="w-full px-3 py-1.5 rounded-lg border border-slate-200 text-xs bg-white focus:ring-2 focus:ring-sky-500">
                            </div>
                            <div>
                                <input type="text" :name="'prescription_items[' + idx + '][strength]'" x-model="item.strength" placeholder="Strength (e.g. 500mg)"
                                       class="w-full px-3 py-1.5 rounded-lg border border-slate-200 text-xs bg-white focus:ring-2 focus:ring-sky-500">
                            </div>
                            <div>
                                <select :name="'prescription_items[' + idx + '][dosage_form]'" x-model="item.dosage_form" class="w-full px-2 py-1.5 rounded-lg border border-slate-200 text-xs bg-white">
                                    <option value="tablet">Tablet</option>
                                    <option value="capsule">Capsule</option>
                                    <option value="syrup">Syrup</option>
                                    <option value="injection">Injection</option>
                                    <option value="drops">Drops</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-4 gap-2 items-center">
                            <div>
                                <input type="text" :name="'prescription_items[' + idx + '][dosage_instructions]'" x-model="item.dosage_instructions" placeholder="Dosage (e.g. 1+0+1)"
                                       class="w-full px-3 py-1.5 rounded-lg border border-slate-200 text-xs bg-white focus:ring-2 focus:ring-sky-500">
                            </div>
                            <div>
                                <input type="number" :name="'prescription_items[' + idx + '][duration_days]'" x-model="item.duration_days" placeholder="Days (e.g. 7)"
                                       class="w-full px-3 py-1.5 rounded-lg border border-slate-200 text-xs bg-white focus:ring-2 focus:ring-sky-500">
                            </div>
                            <div class="flex-1">
                                <input type="text" :name="'prescription_items[' + idx + '][special_instructions]'" x-model="item.special_instructions" placeholder="Instructions (e.g. After meal)"
                                       class="w-full px-3 py-1.5 rounded-lg border border-slate-200 text-xs bg-white focus:ring-2 focus:ring-sky-500">
                            </div>
                            <div class="text-right">
                                <button type="button" @click="removeItem(idx)" class="p-1 text-slate-400 hover:text-rose-600">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- 4. Patient Advice & Follow-up Scheduling -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 space-y-4">
            <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider pb-2 border-b border-slate-100 flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-sky-50 text-sky-600 flex items-center justify-center text-xs font-bold">4</span>
                <span>General Advice & Follow-up</span>
            </h2>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">General Advice / Instructions for Patient</label>
                <textarea name="advice" rows="2" placeholder="Dietary restrictions, fluid intake, exercise, warning signs..."
                          class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-sky-500">Drink plenty of fluids. Brisk walk 30 minutes daily. Avoid refined sugar.</textarea>
            </div>

            <div class="pt-2">
                <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-slate-800">
                    <input type="checkbox" name="schedule_follow_up" value="1" x-model="scheduleFollowUp" class="rounded text-sky-600">
                    <span>Schedule Nurse Call Follow-up</span>
                </label>

                <div x-show="scheduleFollowUp" class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 rounded-xl bg-slate-50 border border-slate-100">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Due Date</label>
                        <input type="date" name="next_follow_up_date" value="{{ now()->addDays(14)->toDateString() }}"
                               class="w-full px-3 py-2 rounded-lg border border-slate-200 text-xs bg-white">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Assign Nurse</label>
                        <select name="assigned_nurse_id" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-xs bg-white">
                            @foreach($nurses as $nurse)
                                <option value="{{ $nurse->id }}">{{ $nurse->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="pt-2 flex items-center justify-end gap-3">
            <a href="{{ route('doctor.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 hover:bg-slate-50">Cancel</a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-sky-600 hover:bg-sky-500 text-white font-semibold text-xs shadow-md shadow-sky-600/20 transition">
                Complete Consultation & Issue Prescription
            </button>
        </div>
    </form>
</div>
@endsection
