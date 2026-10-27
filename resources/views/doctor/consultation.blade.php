@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="{
    activeTab: 'consultation',
    diagnoses: [
        { diagnosis_name: 'Diabetes Type 2', diagnosis_code: 'E11.9', type: 'primary' }
    ],
    items: [
        { medicine_name: 'Metformin', strength: '500mg', dosage_form: 'tablet', dosage_instructions: '1+0+1', duration_days: 30, special_instructions: 'After meals' },
        { medicine_name: 'Amlodipine', strength: '5mg', dosage_form: 'tablet', dosage_instructions: '0+0+1', duration_days: 30, special_instructions: 'At bedtime' }
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
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">New Consultation</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Record diagnosis, clinical examination and issue digital prescriptions.</p>
        </div>
        <a href="{{ route('doctor.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-900 dark:hover:text-white transition">
            &larr; Back to Doctor Desk
        </a>
    </div>

    <!-- Patient Summary Banner (matching mockup) -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-4 shadow-2xs flex flex-wrap items-center justify-between gap-4 transition-colors">
        <div class="flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-full bg-gradient-to-tr from-sky-400 to-teal-400 text-white flex items-center justify-center font-bold text-sm shadow-xs shrink-0">
                {{ strtoupper(substr($appointment->patient->first_name, 0, 1)) }}{{ strtoupper(substr($appointment->patient->last_name ?? '', 0, 1)) }}
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <span class="font-bold text-slate-900 dark:text-white text-sm">{{ $appointment->patient->fullName }}</span>
                    <span class="font-mono px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                        {{ $appointment->patient->patient_number }}
                    </span>
                </div>
                <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400 mt-1">
                    <span>{{ ucfirst($appointment->patient->gender) }}</span>
                    <span>&bull;</span>
                    <span>{{ $appointment->patient->age ? "{$appointment->patient->age} years" : '32 years' }}</span>
                    <span>&bull;</span>
                    <span>Blood: <strong class="text-slate-800 dark:text-slate-200">{{ $appointment->patient->blood_group ?? 'B+' }}</strong></span>
                </div>
            </div>
        </div>

        @if($appointment->patient->allergies->isNotEmpty())
            <span class="px-2 py-1 rounded-lg text-xs font-semibold bg-rose-50 text-rose-700 dark:bg-rose-950/60 dark:text-rose-400 border border-rose-200/60 dark:border-rose-800/60">
                Allergies: {{ $appointment->patient->allergies->pluck('allergen')->join(', ') }}
            </span>
        @endif
    </div>

    <!-- Main Consultation Card with Tabs -->
    <form action="{{ route('doctor.consultations.store', $appointment) }}" method="POST" class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-2xs overflow-hidden transition-colors">
        @csrf

        <!-- Tabs Header (matching mockup: Consultation, Diagnosis, Prescription, Follow-up) -->
        <div class="p-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2 overflow-x-auto text-xs font-semibold">
            <button type="button" @click="activeTab = 'consultation'"
                    :class="activeTab === 'consultation' ? 'bg-[#1E56E3] text-white shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700'"
                    class="px-3.5 py-1.5 rounded-xl transition">
                Consultation
            </button>
            <button type="button" @click="activeTab = 'diagnosis'"
                    :class="activeTab === 'diagnosis' ? 'bg-[#1E56E3] text-white shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700'"
                    class="px-3.5 py-1.5 rounded-xl transition">
                Diagnosis
            </button>
            <button type="button" @click="activeTab = 'prescription'"
                    :class="activeTab === 'prescription' ? 'bg-[#1E56E3] text-white shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700'"
                    class="px-3.5 py-1.5 rounded-xl transition">
                Prescription
            </button>
            <button type="button" @click="activeTab = 'followup'"
                    :class="activeTab === 'followup' ? 'bg-[#1E56E3] text-white shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700'"
                    class="px-3.5 py-1.5 rounded-xl transition">
                Follow-up
            </button>
        </div>

        <div class="p-6 space-y-6">
            <!-- TAB 1: Consultation -->
            <div x-show="activeTab === 'consultation'" class="space-y-4">
                <!-- Chief Complaint -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Chief Complaint *</label>
                    <input type="text" name="chief_complaints" required
                           value="{{ old('chief_complaints', 'Follow-up for diabetes management') }}"
                           placeholder="Follow-up for diabetes management"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-[#1E56E3]">
                </div>

                <!-- Clinical Notes with Toolbar (matching mockup) -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Clinical Notes *</label>
                    
                    <!-- Formatting Toolbar -->
                    <div class="rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                        <div class="px-3 py-1.5 bg-slate-50 dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 flex items-center gap-1 text-slate-600 dark:text-slate-300 text-xs">
                            <button type="button" class="p-1 hover:bg-slate-200 dark:hover:bg-slate-700 rounded font-bold">B</button>
                            <button type="button" class="p-1 hover:bg-slate-200 dark:hover:bg-slate-700 rounded italic">I</button>
                            <button type="button" class="p-1 hover:bg-slate-200 dark:hover:bg-slate-700 rounded underline">U</button>
                            <div class="h-3.5 w-px bg-slate-300 dark:bg-slate-600 mx-1"></div>
                            <button type="button" class="p-1 hover:bg-slate-200 dark:hover:bg-slate-700 rounded">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h10M4 18h16" /></svg>
                            </button>
                            <button type="button" class="p-1 hover:bg-slate-200 dark:hover:bg-slate-700 rounded">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                            </button>
                        </div>
                        <textarea name="clinical_notes" rows="4" required placeholder="Patient reports feeling better. Blood sugar levels are stable."
                                  class="w-full p-3.5 bg-white dark:bg-slate-800 text-xs text-slate-800 dark:text-slate-200 focus:outline-none resize-y">Patient reports feeling better. Blood sugar levels are stable.</textarea>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Physical Examination Findings</label>
                    <textarea name="examination_findings" rows="2" placeholder="e.g. BP 120/80, pulse 72 bpm, chest clear"
                              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-[#1E56E3]">BP 120/80, pulse 72 bpm, chest clear, no pedal edema.</textarea>
                </div>
            </div>

            <!-- TAB 2: Diagnosis -->
            <div x-show="activeTab === 'diagnosis'" style="display: none;" class="space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-800 dark:text-slate-200">ICD / Clinical Diagnoses</span>
                    <button type="button" @click="addDiagnosis()" class="text-xs text-blue-600 dark:text-blue-400 font-semibold hover:underline">
                        + Add Another Diagnosis
                    </button>
                </div>

                <template x-for="(diag, index) in diagnoses" :key="index">
                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 p-3 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/40 items-center">
                        <div class="sm:col-span-6">
                            <input type="text" :name="`diagnoses[${index}][diagnosis_name]`" x-model="diag.diagnosis_name" placeholder="Condition name"
                                   class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs" required>
                        </div>
                        <div class="sm:col-span-3">
                            <input type="text" :name="`diagnoses[${index}][diagnosis_code]`" x-model="diag.diagnosis_code" placeholder="ICD code"
                                   class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
                        </div>
                        <div class="sm:col-span-2">
                            <select :name="`diagnoses[${index}][type]`" x-model="diag.type" class="w-full px-2 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
                                <option value="primary">Primary</option>
                                <option value="secondary">Secondary</option>
                            </select>
                        </div>
                        <div class="sm:col-span-1 text-center">
                            <button type="button" @click="removeDiagnosis(index)" class="text-rose-500 hover:text-rose-700 text-xs font-bold">&times;</button>
                        </div>
                    </div>
                </template>
            </div>

            <!-- TAB 3: Prescription -->
            <div x-show="activeTab === 'prescription'" style="display: none;" class="space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-800 dark:text-slate-200">Prescription Medications</span>
                    <button type="button" @click="addItem()" class="text-xs text-blue-600 dark:text-blue-400 font-semibold hover:underline">
                        + Add Medicine
                    </button>
                </div>

                <template x-for="(item, index) in items" :key="index">
                    <div class="p-3.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/40 space-y-3">
                        <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
                            <div class="sm:col-span-5">
                                <input type="text" :name="`items[${index}][medicine_name]`" x-model="item.medicine_name" placeholder="Medicine Name (e.g. Metformin)"
                                       class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs" required>
                            </div>
                            <div class="sm:col-span-3">
                                <input type="text" :name="`items[${index}][strength]`" x-model="item.strength" placeholder="Strength (e.g. 500mg)"
                                       class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
                            </div>
                            <div class="sm:col-span-3">
                                <input type="text" :name="`items[${index}][dosage_instructions]`" x-model="item.dosage_instructions" placeholder="Dosage (1+0+1)"
                                       class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs" required>
                            </div>
                            <div class="sm:col-span-1 text-center">
                                <button type="button" @click="removeItem(index)" class="text-rose-500 hover:text-rose-700 text-xs font-bold">&times;</button>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                            <input type="number" :name="`items[${index}][duration_days]`" x-model="item.duration_days" placeholder="Duration (days)"
                                   class="w-full px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
                            <input type="text" :name="`items[${index}][special_instructions]`" x-model="item.special_instructions" placeholder="Instructions (e.g. After meals)"
                                   class="w-full px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
                        </div>
                    </div>
                </template>
            </div>

            <!-- TAB 4: Follow-up -->
            <div x-show="activeTab === 'followup'" style="display: none;" class="space-y-4">
                <div class="flex items-center gap-2">
                    <input type="checkbox" id="scheduleFollowUp" name="schedule_follow_up" value="1" x-model="scheduleFollowUp" class="rounded text-[#1E56E3]">
                    <label for="scheduleFollowUp" class="text-xs font-bold text-slate-800 dark:text-slate-200">Assign Nurse Follow-up Call Plan</label>
                </div>

                <div x-show="scheduleFollowUp" class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Follow-up Due Date</label>
                        <input type="date" name="follow_up_due_date" value="{{ now()->addDays(7)->toDateString() }}"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Priority</label>
                        <select name="follow_up_priority" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
                            <option value="routine">Routine</option>
                            <option value="high">High Priority</option>
                            <option value="urgent">Urgent</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Footer Actions -->
        <div class="p-6 bg-slate-50/75 dark:bg-slate-800/40 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-3">
            <a href="{{ route('doctor.index') }}" 
               class="px-5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/60 transition">
                Cancel
            </a>
            <button type="submit" 
                    class="px-6 py-2.5 rounded-xl bg-[#1E56E3] hover:bg-blue-600 text-white font-semibold text-xs shadow-md shadow-blue-600/25 transition">
                Save & Complete Consultation
            </button>
        </div>
    </form>
</div>
@endsection
