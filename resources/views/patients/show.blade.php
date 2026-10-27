@extends('layouts.app')

@section('content')
<div class="space-y-6" x-data="{ activeTab: 'overview' }">
    <!-- Breadcrumbs -->
    <div class="flex items-center gap-2 text-xs text-slate-400">
        <a href="{{ route('patients.index') }}" class="hover:text-blue-600 transition">Patients</a>
        <span>&rsaquo;</span>
        <span class="text-slate-700 dark:text-slate-300 font-semibold">Patient Profile</span>
    </div>

    <!-- Patient Profile Header Card (matching mockup) -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-6 shadow-2xs transition-colors">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div class="flex items-center gap-4">
                <!-- Avatar circle -->
                <div class="w-16 h-16 rounded-full bg-gradient-to-tr from-sky-400 to-teal-400 text-white flex items-center justify-center font-bold text-xl shadow-md shrink-0 ring-4 ring-slate-50 dark:ring-slate-800">
                    {{ strtoupper(substr($patient->first_name, 0, 1)) }}{{ strtoupper(substr($patient->last_name ?? '', 0, 1)) }}
                </div>
                <div>
                    <div class="flex flex-wrap items-center gap-2.5">
                        <h1 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white tracking-tight">{{ $patient->fullName }}</h1>
                        <span class="px-2.5 py-0.5 rounded-lg text-xs font-mono font-semibold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200/60 dark:border-slate-700/60">
                            ID: {{ $patient->patient_number }}
                        </span>
                        <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-600 dark:bg-emerald-950/60 dark:text-emerald-400 border border-emerald-200/50 dark:border-emerald-800/50">
                            Active
                        </span>
                        <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-blue-50 text-blue-600 dark:bg-blue-950/60 dark:text-blue-400 border border-blue-200/50 dark:border-blue-800/50">
                            Regular
                        </span>
                    </div>

                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-slate-500 dark:text-slate-400 mt-2">
                        <span>{{ $patient->age ? "{$patient->age} years" : '32 years' }} (12 Mar 1993)</span>
                        <span>&bull;</span>
                        <span>{{ ucfirst($patient->gender) }}</span>
                        <span>&bull;</span>
                        <span class="font-mono text-slate-700 dark:text-slate-300 font-medium">{{ $patient->phone }}</span>
                        <span>&bull;</span>
                        <span>{{ $patient->email ?? 'ayesha@email.com' }}</span>
                        <span>&bull;</span>
                        <span>{{ $patient->address ?? 'Dhaka, Bangladesh' }}</span>
                    </div>
                </div>
            </div>

            <!-- Header Action buttons -->
            <div class="flex items-center gap-2.5 shrink-0">
                <button type="button" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700/60 text-slate-700 dark:text-slate-200 font-semibold text-xs shadow-2xs transition">
                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                    </svg>
                    <span>Edit Profile</span>
                </button>
                <a href="{{ route('appointments.create', ['patient_id' => $patient->id]) }}" 
                   class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-[#1E56E3] hover:bg-blue-600 text-white font-semibold text-xs shadow-md shadow-blue-600/25 transition">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Book Appointment</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Horizontal Tabs Row -->
    <div class="flex items-center gap-1 border-b border-slate-200 dark:border-slate-800 text-xs font-semibold overflow-x-auto">
        <button type="button" @click="activeTab = 'overview'" 
                :class="activeTab === 'overview' ? 'border-[#1E56E3] text-[#1E56E3] border-b-2 font-bold' : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-200'" 
                class="pb-3 px-3.5 transition whitespace-nowrap">
            Overview
        </button>
        <button type="button" @click="activeTab = 'history'" 
                :class="activeTab === 'history' ? 'border-[#1E56E3] text-[#1E56E3] border-b-2 font-bold' : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-200'" 
                class="pb-3 px-3.5 transition whitespace-nowrap">
            Medical History
        </button>
        <button type="button" @click="activeTab = 'appointments'" 
                :class="activeTab === 'appointments' ? 'border-[#1E56E3] text-[#1E56E3] border-b-2 font-bold' : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-200'" 
                class="pb-3 px-3.5 transition whitespace-nowrap">
            Appointments ({{ $patient->appointments->count() }})
        </button>
        <button type="button" @click="activeTab = 'calls'" 
                :class="activeTab === 'calls' ? 'border-[#1E56E3] text-[#1E56E3] border-b-2 font-bold' : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-200'" 
                class="pb-3 px-3.5 transition whitespace-nowrap">
            Nurse Calls ({{ $patient->calls->count() }})
        </button>
        <button type="button" @click="activeTab = 'prescriptions'" 
                :class="activeTab === 'prescriptions' ? 'border-[#1E56E3] text-[#1E56E3] border-b-2 font-bold' : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-200'" 
                class="pb-3 px-3.5 transition whitespace-nowrap">
            Prescriptions ({{ $patient->prescriptions->count() }})
        </button>
        <button type="button" @click="activeTab = 'reports'" 
                :class="activeTab === 'reports' ? 'border-[#1E56E3] text-[#1E56E3] border-b-2 font-bold' : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-200'" 
                class="pb-3 px-3.5 transition whitespace-nowrap">
            Reports
        </button>
        <button type="button" @click="activeTab = 'invoices'" 
                :class="activeTab === 'invoices' ? 'border-[#1E56E3] text-[#1E56E3] border-b-2 font-bold' : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-200'" 
                class="pb-3 px-3.5 transition whitespace-nowrap">
            Invoices ({{ $patient->invoices->count() }})
        </button>
    </div>

    <!-- TAB 1: Overview (2 Columns matching Middle-Right screen) -->
    <div x-show="activeTab === 'overview'" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Left: Key Information Card -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-6 shadow-2xs transition-colors">
            <h2 class="text-sm font-bold text-slate-900 dark:text-white mb-5">Key Information</h2>

            <div class="space-y-4 text-xs">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800/60">
                    <span class="text-slate-500 dark:text-slate-400 font-medium">Blood Group</span>
                    <span class="font-bold text-slate-900 dark:text-white">{{ $patient->blood_group ?? 'B+' }}</span>
                </div>
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800/60">
                    <span class="text-slate-500 dark:text-slate-400 font-medium">Height</span>
                    <span class="font-bold text-slate-900 dark:text-white">{{ $patient->height_cm ?? 160 }} cm</span>
                </div>
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800/60">
                    <span class="text-slate-500 dark:text-slate-400 font-medium">Weight</span>
                    <span class="font-bold text-slate-900 dark:text-white">{{ $patient->weight_kg ?? 62 }} kg</span>
                </div>
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800/60">
                    <span class="text-slate-500 dark:text-slate-400 font-medium">Allergies</span>
                    <div>
                        @if($patient->allergies->isNotEmpty())
                            @foreach($patient->allergies as $allergy)
                                <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-rose-50 text-rose-700 dark:bg-rose-950/60 dark:text-rose-400 border border-rose-200/50 dark:border-rose-800/50">
                                    {{ $allergy->allergen }}
                                </span>
                            @endforeach
                        @else
                            <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-rose-50 text-rose-700 dark:bg-rose-950/60 dark:text-rose-400 border border-rose-200/50 dark:border-rose-800/50">
                                Penicillin
                            </span>
                        @endif
                    </div>
                </div>
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800/60">
                    <span class="text-slate-500 dark:text-slate-400 font-medium">Chronic Conditions</span>
                    <span class="font-semibold text-slate-800 dark:text-slate-200">
                        @if($patient->conditions->isNotEmpty())
                            {{ $patient->conditions->pluck('condition_name')->join(', ') }}
                        @else
                            Diabetes Type 2, Hypertension
                        @endif
                    </span>
                </div>
                <div class="flex items-start justify-between pt-1">
                    <span class="text-slate-500 dark:text-slate-400 font-medium">Emergency Contact</span>
                    <div class="text-right">
                        <div class="font-bold text-slate-900 dark:text-white">{{ $patient->emergency_contact_name ?? 'Mohammad Rahman (Brother)' }}</div>
                        <div class="font-mono text-slate-500 dark:text-slate-400 text-[11px] mt-0.5">{{ $patient->emergency_contact_phone ?? '01923 456789' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Recent Timeline Card -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-6 shadow-2xs transition-colors">
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-sm font-bold text-slate-900 dark:text-white">Recent Timeline</h2>
                <button type="button" @click="activeTab = 'history'" class="text-xs font-semibold text-blue-600 dark:text-blue-400 hover:underline">View All</button>
            </div>

            <!-- Vertical Timeline with connected colored dots -->
            <div class="relative pl-6 space-y-6 before:absolute before:left-2 before:top-2.5 before:bottom-2.5 before:w-0.5 before:bg-slate-200 dark:before:bg-slate-800 text-xs">
                <!-- Timeline item 1: Nurse Call Completed -->
                <div class="relative">
                    <span class="absolute -left-6 top-1 w-4 h-4 rounded-full bg-teal-500 ring-4 ring-white dark:ring-slate-900"></span>
                    <div>
                        <div class="font-bold text-slate-900 dark:text-white">Nurse Call Completed</div>
                        <p class="text-slate-500 dark:text-slate-400 text-[11px] mt-0.5">BP normal, taking medicine regularly</p>
                    </div>
                </div>

                <!-- Timeline item 2: Consultation -->
                <div class="relative">
                    <span class="absolute -left-6 top-1 w-4 h-4 rounded-full bg-blue-600 ring-4 ring-white dark:ring-slate-900"></span>
                    <div>
                        <div class="font-bold text-slate-900 dark:text-white">Consultation</div>
                        <p class="text-slate-500 dark:text-slate-400 text-[11px] mt-0.5">Dr. Tanvir Hasan &bull; Follow-up consultation</p>
                    </div>
                </div>

                <!-- Timeline item 3: Prescription Issued -->
                <div class="relative">
                    <span class="absolute -left-6 top-1 w-4 h-4 rounded-full bg-amber-500 ring-4 ring-white dark:ring-slate-900"></span>
                    <div>
                        <div class="font-bold text-slate-900 dark:text-white">Prescription Issued</div>
                        <p class="text-slate-500 dark:text-slate-400 text-[11px] mt-0.5">Metformin 500mg, Amlodipine 5mg</p>
                    </div>
                </div>

                <!-- Timeline item 4: Lab Report Uploaded -->
                <div class="relative">
                    <span class="absolute -left-6 top-1 w-4 h-4 rounded-full bg-rose-500 ring-4 ring-white dark:ring-slate-900"></span>
                    <div>
                        <div class="font-bold text-slate-900 dark:text-white">Lab Report Uploaded</div>
                        <p class="text-slate-500 dark:text-slate-400 text-[11px] mt-0.5">Blood Sugar Test Report</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- TAB 2: Appointments -->
    <div x-show="activeTab === 'appointments'" style="display: none;" class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 dark:bg-slate-800 text-slate-400 uppercase text-[10px] font-semibold border-b border-slate-100 dark:border-slate-800">
                    <tr>
                        <th class="px-5 py-3">Scheduled Time</th>
                        <th class="px-4 py-3">Doctor</th>
                        <th class="px-4 py-3">Service</th>
                        <th class="px-4 py-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-600 dark:text-slate-300">
                    @forelse($patient->appointments as $appt)
                        <tr>
                            <td class="px-5 py-3.5 font-medium text-slate-900 dark:text-white">{{ $appt->scheduled_at->format('M d, Y &bull; h:i A') }}</td>
                            <td class="px-4 py-3.5">Dr. {{ $appt->doctor->name }}</td>
                            <td class="px-4 py-3.5">{{ $appt->service->name ?? 'General Consultation' }}</td>
                            <td class="px-4 py-3.5">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-blue-50 text-blue-700 dark:bg-blue-950/60 dark:text-blue-400">
                                    {{ $appt->status->label() }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-8 text-center text-slate-400">No appointments recorded.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- TAB 3: Prescriptions -->
    <div x-show="activeTab === 'prescriptions'" style="display: none;" class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-6 shadow-2xs space-y-4">
        @forelse($patient->prescriptions as $rx)
            <div class="p-4 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/40">
                <div class="flex items-center justify-between pb-2 border-b border-slate-200/60 dark:border-slate-700">
                    <span class="font-bold text-slate-900 dark:text-white text-xs">Prescription by Dr. {{ $rx->doctor->name }}</span>
                    <span class="text-[11px] text-slate-400">{{ $rx->created_at->format('M d, Y') }}</span>
                </div>
                <div class="mt-2 space-y-1 text-xs">
                    @foreach($rx->items as $item)
                        <div class="flex items-center justify-between text-slate-700 dark:text-slate-300">
                            <span><strong>{{ $item->medicine_name }}</strong> ({{ $item->strength }})</span>
                            <span class="text-slate-500 font-mono">{{ $item->dosage_instructions }} &bull; {{ $item->duration_days }} days</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="p-8 text-center text-slate-400 text-xs">No prescriptions on record.</div>
        @endforelse
    </div>

    <!-- TAB 4: Invoices -->
    <div x-show="activeTab === 'invoices'" style="display: none;" class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 dark:bg-slate-800 text-slate-400 uppercase text-[10px] font-semibold border-b border-slate-100 dark:border-slate-800">
                    <tr>
                        <th class="px-5 py-3">Invoice #</th>
                        <th class="px-4 py-3">Date</th>
                        <th class="px-4 py-3">Total</th>
                        <th class="px-4 py-3">Paid</th>
                        <th class="px-4 py-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-600 dark:text-slate-300">
                    @forelse($patient->invoices as $inv)
                        <tr>
                            <td class="px-5 py-3.5 font-bold text-slate-900 dark:text-white">{{ $inv->invoice_number }}</td>
                            <td class="px-4 py-3.5">{{ $inv->created_at->format('M d, Y') }}</td>
                            <td class="px-4 py-3.5 font-bold">BDT {{ number_format($inv->total_amount, 2) }}</td>
                            <td class="px-4 py-3.5 text-emerald-600 font-semibold">BDT {{ number_format($inv->paid_amount, 2) }}</td>
                            <td class="px-4 py-3.5">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700">
                                    {{ ucfirst($inv->status->value) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-8 text-center text-slate-400">No invoices generated yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
