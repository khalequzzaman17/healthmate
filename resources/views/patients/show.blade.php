@extends('layouts.app')

@section('content')
<div class="space-y-6" x-data="{ activeTab: 'timeline' }">
    <!-- Patient Profile Header -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div class="flex items-start gap-4">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-sky-500 to-teal-400 text-white flex items-center justify-center font-bold text-xl shadow-md shadow-sky-500/20 shrink-0">
                    {{ strtoupper(substr($patient->first_name, 0, 1)) }}{{ strtoupper(substr($patient->last_name ?? '', 0, 1)) }}
                </div>
                <div>
                    <div class="flex items-center gap-3">
                        <h1 class="text-2xl font-bold text-slate-900 tracking-tight">{{ $patient->fullName }}</h1>
                        <span class="font-mono px-2.5 py-0.5 rounded-md text-xs font-semibold bg-sky-50 text-sky-700 border border-sky-200">
                            {{ $patient->patient_number }}
                        </span>
                        @if($patient->blood_group)
                            <span class="px-2 py-0.5 rounded-md text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                Blood: {{ $patient->blood_group }}
                            </span>
                        @endif
                    </div>
                    
                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-slate-500 mt-2">
                        <span>{{ ucfirst($patient->gender) }} &bull; {{ $patient->age ? "{$patient->age} years" : 'DOB not recorded' }}</span>
                        <span>Phone: <strong class="text-slate-800">{{ $patient->phone }}</strong></span>
                        @if($patient->email)
                            <span>Email: <strong class="text-slate-800">{{ $patient->email }}</strong></span>
                        @endif
                        @if($patient->address)
                            <span>Address: {{ $patient->address }}</span>
                        @endif
                    </div>

                    @if($patient->emergency_contact_name)
                        <div class="text-[11px] text-slate-500 mt-1">
                            Emergency Contact: <strong>{{ $patient->emergency_contact_name }}</strong> ({{ $patient->emergency_contact_relation ?? 'Guardian' }}) &bull; {{ $patient->emergency_contact_phone }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Action Buttons -->
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('appointments.create', ['patient_id' => $patient->id]) }}" class="px-3.5 py-2 rounded-xl bg-sky-600 hover:bg-sky-500 text-white text-xs font-semibold shadow-xs">
                    Book Appointment
                </a>
                <a href="{{ route('nurse.call.create', $patient) }}" class="px-3.5 py-2 rounded-xl bg-teal-600 hover:bg-teal-500 text-white text-xs font-semibold shadow-xs">
                    Log Nurse Call
                </a>
                <a href="{{ route('billing.create', ['patient_id' => $patient->id]) }}" class="px-3.5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold shadow-xs">
                    Create Invoice
                </a>
            </div>
        </div>

        <!-- Clinical Badges: Allergies, Conditions & Nurse Assignment -->
        <div class="mt-6 pt-5 border-t border-slate-100 grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Allergies -->
            <div class="p-3.5 rounded-xl bg-rose-50/60 border border-rose-100">
                <div class="text-[11px] font-bold uppercase tracking-wider text-rose-800 mb-1.5 flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                    <span>Known Allergies</span>
                </div>
                <div class="flex flex-wrap gap-1.5">
                    @forelse($patient->allergies as $allergy)
                        <span class="px-2 py-0.5 rounded text-xs font-semibold bg-white text-rose-700 border border-rose-200">
                            {{ $allergy->allergen }} ({{ $allergy->severity }})
                        </span>
                    @empty
                        <span class="text-xs text-rose-600/70 italic">No allergies recorded</span>
                    @endforelse
                </div>
            </div>

            <!-- Chronic Conditions -->
            <div class="p-3.5 rounded-xl bg-amber-50/60 border border-amber-100">
                <div class="text-[11px] font-bold uppercase tracking-wider text-amber-800 mb-1.5 flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                    <span>Chronic Conditions</span>
                </div>
                <div class="flex flex-wrap gap-1.5">
                    @forelse($patient->conditions as $cond)
                        <span class="px-2 py-0.5 rounded text-xs font-semibold bg-white text-amber-800 border border-amber-200">
                            {{ $cond->condition_name }}
                        </span>
                    @empty
                        <span class="text-xs text-amber-700/70 italic">No chronic conditions listed</span>
                    @endforelse
                </div>
            </div>

            <!-- Primary Care Nurse -->
            <div class="p-3.5 rounded-xl bg-teal-50/60 border border-teal-100">
                <div class="text-[11px] font-bold uppercase tracking-wider text-teal-800 mb-1.5 flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                    <span>Primary Care Nurse</span>
                </div>
                <div>
                    @if($patient->activeAssignment)
                        <div class="text-xs font-bold text-teal-900">{{ $patient->activeAssignment->nurse->name }}</div>
                        <div class="text-[11px] text-teal-700">Assigned for follow-ups & vitals monitoring</div>
                    @else
                        <span class="text-xs text-teal-700/70 italic">No nurse currently assigned</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Timeline Navigation Tabs -->
    <div class="flex items-center gap-2 border-b border-slate-200 text-xs font-semibold">
        <button @click="activeTab = 'timeline'" :class="activeTab === 'timeline' ? 'border-sky-600 text-sky-600 border-b-2 font-bold' : 'text-slate-500 hover:text-slate-800'" class="pb-3 px-3 transition">
            Unified Patient Timeline
        </button>
        <button @click="activeTab = 'prescriptions'" :class="activeTab === 'prescriptions' ? 'border-sky-600 text-sky-600 border-b-2 font-bold' : 'text-slate-500 hover:text-slate-800'" class="pb-3 px-3 transition">
            Prescriptions ({{ $patient->prescriptions->count() }})
        </button>
        <button @click="activeTab = 'vitals'" :class="activeTab === 'vitals' ? 'border-sky-600 text-sky-600 border-b-2 font-bold' : 'text-slate-500 hover:text-slate-800'" class="pb-3 px-3 transition">
            Nurse Calls & Vitals ({{ $patient->calls->count() }})
        </button>
        <button @click="activeTab = 'billing'" :class="activeTab === 'billing' ? 'border-sky-600 text-sky-600 border-b-2 font-bold' : 'text-slate-500 hover:text-slate-800'" class="pb-3 px-3 transition">
            Invoices & Payments ({{ $patient->invoices->count() }})
        </button>
    </div>

    <!-- TAB 1: Unified Timeline -->
    <div x-show="activeTab === 'timeline'" class="space-y-4">
        <!-- Chronological Feed -->
        <div class="relative pl-6 space-y-6 before:absolute before:left-2.5 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-200">
            <!-- Consultations -->
            @foreach($patient->consultations as $consult)
                <div class="relative">
                    <div class="absolute -left-6 top-1.5 w-5 h-5 rounded-full bg-sky-500 border-4 border-white shadow-xs"></div>
                    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-sky-700 uppercase tracking-wider">Doctor Consultation</span>
                            <span class="text-xs text-slate-400">{{ $consult->created_at->format('M d, Y &bull; h:i A') }}</span>
                        </div>
                        <div class="text-sm font-bold text-slate-900 mt-1">Conducted by Dr. {{ $consult->doctor->name }}</div>

                        @if($consult->chief_complaints)
                            <div class="mt-2 text-xs text-slate-700">
                                <strong>Chief Complaints:</strong> {{ $consult->chief_complaints }}
                            </div>
                        @endif

                        @if($consult->clinical_notes)
                            <div class="mt-1 text-xs text-slate-600">
                                <strong>Clinical Notes:</strong> {{ $consult->clinical_notes }}
                            </div>
                        @endif

                        @if($consult->diagnoses->isNotEmpty())
                            <div class="mt-3 pt-2 border-t border-slate-100 flex flex-wrap gap-2">
                                @foreach($consult->diagnoses as $diag)
                                    <span class="px-2 py-1 rounded-md text-xs font-semibold bg-sky-50 text-sky-800 border border-sky-100">
                                        Diagnosis: {{ $diag->diagnosis_name }} ({{ $diag->diagnosis_code ?? 'Clinical' }})
                                    </span>
                                @endforeach
                            </div>
                        @endif

                        @if($consult->prescription)
                            <div class="mt-3 p-3 rounded-xl bg-slate-50 border border-slate-100">
                                <div class="text-xs font-bold text-slate-900 mb-1">Prescription Issued (#{{ $consult->prescription->prescription_number }})</div>
                                <div class="space-y-1">
                                    @foreach($consult->prescription->items as $item)
                                        <div class="text-xs text-slate-700 flex justify-between">
                                            <span><strong>{{ $item->medicine_name }}</strong> ({{ $item->strength }}) &bull; {{ $item->dosage_instructions }}</span>
                                            <span class="text-slate-500">{{ $item->duration_days }} days ({{ $item->special_instructions }})</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach

            <!-- Nurse Calls & Health Reports -->
            @foreach($patient->healthReports as $report)
                <div class="relative">
                    <div class="absolute -left-6 top-1.5 w-5 h-5 rounded-full bg-teal-500 border-4 border-white shadow-xs"></div>
                    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-teal-700 uppercase tracking-wider">Nurse Follow-up Report</span>
                            <span class="text-xs text-slate-400">{{ $report->created_at->format('M d, Y &bull; h:i A') }}</span>
                        </div>
                        <div class="text-sm font-bold text-slate-900 mt-1">{{ $report->title }}</div>
                        <div class="text-xs text-slate-500 mt-0.5">Submitted by Nurse {{ $report->nurse->name }}</div>
                        <p class="text-xs text-slate-700 mt-2 bg-slate-50 p-3 rounded-xl border border-slate-100">{{ $report->summary }}</p>

                        @if($report->reviewerDoctor)
                            <div class="mt-3 text-xs text-emerald-700 font-medium bg-emerald-50 p-2.5 rounded-lg border border-emerald-200">
                                Reviewed by Dr. {{ $report->reviewerDoctor->name }}: "{{ $report->review_notes }}"
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach

            <!-- Empty Timeline fallback -->
            @if($patient->consultations->isEmpty() && $patient->healthReports->isEmpty())
                <div class="bg-white p-8 rounded-2xl border border-slate-200/80 text-center text-xs text-slate-400">
                    No clinical encounters or nurse reports recorded yet.
                </div>
            @endif
        </div>
    </div>

    <!-- TAB 2: Prescriptions -->
    <div x-show="activeTab === 'prescriptions'" style="display: none;" class="space-y-4">
        <div class="grid grid-cols-1 gap-4">
            @forelse($patient->prescriptions as $rx)
                <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div>
                            <span class="text-xs font-bold text-sky-700 font-mono">RX #{{ $rx->prescription_number }}</span>
                            <div class="text-sm font-bold text-slate-900 mt-0.5">Attending Physician: Dr. {{ $rx->doctor->name }}</div>
                        </div>
                        <span class="text-xs text-slate-500 font-medium">{{ $rx->issued_at->format('F d, Y') }}</span>
                    </div>

                    <div class="mt-3 overflow-x-auto">
                        <table class="w-full text-left text-xs text-slate-700">
                            <thead class="text-[10px] text-slate-400 uppercase font-semibold border-b border-slate-100">
                                <tr>
                                    <th class="py-2">Medicine Name</th>
                                    <th class="py-2">Dosage Instructions</th>
                                    <th class="py-2">Route</th>
                                    <th class="py-2">Duration</th>
                                    <th class="py-2">Special Advice</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($rx->items as $item)
                                    <tr>
                                        <td class="py-2 font-bold text-slate-900">{{ $item->medicine_name }} ({{ $item->strength }})</td>
                                        <td class="py-2">{{ $item->dosage_instructions }}</td>
                                        <td class="py-2">{{ ucfirst($item->route) }}</td>
                                        <td class="py-2">{{ $item->duration_days }} days</td>
                                        <td class="py-2 text-slate-500">{{ $item->special_instructions ?? '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @empty
                <div class="bg-white p-8 rounded-2xl border border-slate-200/80 text-center text-xs text-slate-400">
                    No prescriptions on record for this patient.
                </div>
            @endforelse
        </div>
    </div>

    <!-- TAB 3: Nurse Calls & Vitals History -->
    <div x-show="activeTab === 'vitals'" style="display: none;" class="space-y-4">
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100">
                <h3 class="text-sm font-bold text-slate-900">Recorded Health Observations & Vitals</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-[10px] text-slate-500 uppercase font-semibold border-b border-slate-100">
                        <tr>
                            <th class="px-5 py-3">Date</th>
                            <th class="px-4 py-3">Blood Pressure</th>
                            <th class="px-4 py-3">Blood Glucose</th>
                            <th class="px-4 py-3">Temp / Weight</th>
                            <th class="px-4 py-3">Medication Adherence</th>
                            <th class="px-4 py-3">Escalated?</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($patient->healthObservations as $obs)
                            <tr class="hover:bg-slate-50/50">
                                <td class="px-5 py-3 font-medium text-slate-900">{{ $obs->created_at->format('M d, Y h:i A') }}</td>
                                <td class="px-4 py-3 font-bold text-slate-800">
                                    {{ $obs->systolic_bp ? "{$obs->systolic_bp}/{$obs->diastolic_bp} mmHg" : '—' }}
                                </td>
                                <td class="px-4 py-3 font-bold text-slate-800">
                                    {{ $obs->blood_glucose ? "{$obs->blood_glucose} mmol/L ({$obs->glucose_type})" : '—' }}
                                </td>
                                <td class="px-4 py-3">
                                    {{ $obs->temperature_f ? "{$obs->temperature_f}°F" : '—' }} / {{ $obs->weight_kg ? "{$obs->weight_kg} kg" : '—' }}
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold
                                        {{ $obs->medication_adherence === 'good' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                                        {{ ucfirst($obs->medication_adherence) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    @if($obs->escalation_needed)
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                            Yes (Doctor Alert)
                                        </span>
                                    @else
                                        <span class="text-slate-400">Normal</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-6 text-center text-slate-400">No vitals observations recorded yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TAB 4: Invoices & Payments -->
    <div x-show="activeTab === 'billing'" style="display: none;" class="space-y-4">
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-[10px] text-slate-500 uppercase font-semibold border-b border-slate-100">
                        <tr>
                            <th class="px-5 py-3">Invoice #</th>
                            <th class="px-4 py-3">Date</th>
                            <th class="px-4 py-3">Total Amount</th>
                            <th class="px-4 py-3">Paid Amount</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($patient->invoices as $inv)
                            <tr class="hover:bg-slate-50/50">
                                <td class="px-5 py-3 font-mono font-semibold text-sky-700">{{ $inv->invoice_number }}</td>
                                <td class="px-4 py-3">{{ $inv->created_at->format('M d, Y') }}</td>
                                <td class="px-4 py-3 font-bold text-slate-900">BDT {{ number_format($inv->total_amount, 2) }}</td>
                                <td class="px-4 py-3 text-emerald-700 font-semibold">BDT {{ number_format($inv->paid_amount, 2) }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold
                                        {{ $inv->status->value === 'paid' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                                        {{ $inv->status->label() }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('billing.show', $inv) }}" class="px-2.5 py-1 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-[11px]">
                                        View Invoice
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-6 text-center text-slate-400">No invoices recorded for this patient.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
