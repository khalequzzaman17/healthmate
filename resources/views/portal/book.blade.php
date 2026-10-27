@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="{
    step: 1,
    selectedService: '{{ $services->first()?->id ?? 2 }}',
    selectedDoctor: '{{ $doctors->first()?->id ?? 1 }}'
}">
    <!-- Top Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Book an Appointment</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Select a clinical service, choose your doctor and preferred consultation slot.</p>
        </div>
        <a href="{{ route('portal.dashboard') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-900 dark:hover:text-white transition">
            &larr; Back to Portal
        </a>
    </div>

    <!-- 3-Step Progress Stepper (matching the image) -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-4 shadow-2xs flex items-center justify-between sm:justify-start gap-4 sm:gap-8 text-xs font-semibold">
        <!-- Step 1 -->
        <button type="button" @click="step = 1" class="flex items-center gap-2 text-left focus:outline-none">
            <span :class="step === 1 ? 'bg-[#1E56E3] text-white shadow-xs' : (step > 1 ? 'bg-emerald-500 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-400')" 
                  class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold transition">
                1
            </span>
            <span :class="step === 1 ? 'text-[#1E56E3] font-bold' : 'text-slate-600 dark:text-slate-300'">Select Service</span>
        </button>

        <div class="w-8 h-0.5 bg-slate-200 dark:bg-slate-800 hidden sm:block"></div>

        <!-- Step 2 -->
        <button type="button" @click="step = 2" class="flex items-center gap-2 text-left focus:outline-none">
            <span :class="step === 2 ? 'bg-[#1E56E3] text-white shadow-xs' : (step > 2 ? 'bg-emerald-500 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-400')" 
                  class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold transition">
                2
            </span>
            <span :class="step === 2 ? 'text-[#1E56E3] font-bold' : 'text-slate-600 dark:text-slate-300'">Choose Date & Time</span>
        </button>

        <div class="w-8 h-0.5 bg-slate-200 dark:bg-slate-800 hidden sm:block"></div>

        <!-- Step 3 -->
        <button type="button" @click="step = 3" class="flex items-center gap-2 text-left focus:outline-none">
            <span :class="step === 3 ? 'bg-[#1E56E3] text-white shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-400'" 
                  class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold transition">
                3
            </span>
            <span :class="step === 3 ? 'text-[#1E56E3] font-bold' : 'text-slate-600 dark:text-slate-300'">Confirm</span>
        </button>
    </div>

    <!-- Booking Form -->
    <form action="{{ route('portal.book.store') }}" method="POST" class="space-y-6">
        @csrf

        <!-- STEP 1: Select Service & Doctor -->
        <div x-show="step === 1" class="space-y-6">
            <!-- 1. Select Service Cards -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-6 shadow-2xs space-y-4">
                <h2 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                    <span>Available Clinical Services</span>
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    @forelse($services as $s)
                        <label @click="selectedService = '{{ $s->id }}'" 
                               :class="selectedService == '{{ $s->id }}' ? 'border-[#1E56E3] ring-2 ring-[#1E56E3]/20 bg-blue-50/20 dark:bg-blue-950/20' : 'border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-700 bg-white dark:bg-slate-900'"
                               class="relative p-4 rounded-2xl border flex flex-col justify-between cursor-pointer transition shadow-2xs">
                            <input type="radio" name="service_id" value="{{ $s->id }}" :checked="selectedService == '{{ $s->id }}'" class="sr-only">
                            <div>
                                <div class="flex items-start justify-between">
                                    <div class="w-2 h-2 rounded-full" :class="selectedService == '{{ $s->id }}' ? 'bg-[#1E56E3]' : 'bg-slate-300 dark:bg-slate-700'"></div>
                                    <span class="text-[10px] font-medium text-slate-400">{{ $s->duration_minutes ?? 30 }} mins</span>
                                </div>
                                <div class="font-bold text-slate-900 dark:text-white text-xs mt-3">{{ $s->name }}</div>
                            </div>
                            <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800/60 flex items-center justify-between">
                                <span class="text-xs font-bold text-[#1E56E3] dark:text-blue-400">BDT {{ number_format($s->price) }}</span>
                                <span class="text-[10px] font-medium text-slate-400">Fixed</span>
                            </div>
                        </label>
                    @empty
                        <!-- Default mock services matching the image -->
                        <div class="p-4 rounded-2xl border border-[#1E56E3] bg-blue-50/30 dark:bg-blue-950/20">
                            <div class="font-bold text-slate-900 dark:text-white text-xs">Diabetes Consultation</div>
                            <div class="text-xs font-bold text-[#1E56E3] mt-2">BDT 800 &bull; 30 mins</div>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- 2. Select Doctor Cards -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-6 shadow-2xs space-y-4">
                <h2 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                    <span>Select Attending Doctor</span>
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    @forelse($doctors as $d)
                        <label @click="selectedDoctor = '{{ $d->id }}'"
                               :class="selectedDoctor == '{{ $d->id }}' ? 'border-[#1E56E3] ring-2 ring-[#1E56E3]/20 bg-blue-50/20 dark:bg-blue-950/20' : 'border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-700 bg-white dark:bg-slate-900'"
                               class="relative p-4 rounded-2xl border flex items-center gap-3.5 cursor-pointer transition shadow-2xs">
                            <input type="radio" name="doctor_id" value="{{ $d->id }}" :checked="selectedDoctor == '{{ $d->id }}'" class="sr-only">
                            <div class="w-11 h-11 rounded-full bg-gradient-to-tr from-sky-400 to-blue-600 text-white flex items-center justify-center font-bold text-sm shrink-0">
                                {{ strtoupper(substr($d->name, 0, 1)) }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="font-bold text-slate-900 dark:text-white text-xs truncate">Dr. {{ $d->name }}</div>
                                <div class="text-[11px] text-slate-500 dark:text-slate-400 truncate">{{ $d->specialty ?? 'General Physician' }}</div>
                            </div>
                            <div class="w-3.5 h-3.5 rounded-full border border-slate-300 dark:border-slate-600 flex items-center justify-center"
                                 :class="selectedDoctor == '{{ $d->id }}' ? 'border-[#1E56E3] bg-[#1E56E3]' : ''">
                                <div x-show="selectedDoctor == '{{ $d->id }}'" class="w-1.5 h-1.5 rounded-full bg-white"></div>
                            </div>
                        </label>
                    @empty
                        <div class="text-xs text-slate-400">No doctors available.</div>
                    @endforelse
                </div>
            </div>

            <div class="flex justify-end">
                <button type="button" @click="step = 2" class="px-6 py-2.5 rounded-xl bg-[#1E56E3] hover:bg-blue-600 text-white font-semibold text-xs shadow-md shadow-blue-600/25 transition">
                    Continue to Date & Time &rarr;
                </button>
            </div>
        </div>

        <!-- STEP 2: Choose Date & Time -->
        <div x-show="step === 2" style="display: none;" class="space-y-6">
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-6 shadow-2xs space-y-4">
                <h2 class="text-sm font-bold text-slate-900 dark:text-white">Choose Preferred Schedule</h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Appointment Date *</label>
                        <input type="date" name="scheduled_date" value="{{ old('scheduled_date', now()->addDay()->toDateString()) }}" required min="{{ now()->toDateString() }}"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs focus:ring-2 focus:ring-[#1E56E3]">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Appointment Time *</label>
                        <input type="time" name="scheduled_time" value="{{ old('scheduled_time', '10:30') }}" required
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs focus:ring-2 focus:ring-[#1E56E3]">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Reason for Visit / Symptoms</label>
                    <textarea name="notes" rows="3" placeholder="Briefly describe what symptoms or checkup you need"
                              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs focus:ring-2 focus:ring-[#1E56E3]">{{ old('notes') }}</textarea>
                </div>
            </div>

            <div class="flex justify-between items-center">
                <button type="button" @click="step = 1" class="px-5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-semibold hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                    &larr; Back to Services
                </button>
                <button type="button" @click="step = 3" class="px-6 py-2.5 rounded-xl bg-[#1E56E3] hover:bg-blue-600 text-white font-semibold text-xs shadow-md shadow-blue-600/25 transition">
                    Review & Confirm &rarr;
                </button>
            </div>
        </div>

        <!-- STEP 3: Confirm & Submit -->
        <div x-show="step === 3" style="display: none;" class="space-y-6">
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-6 shadow-2xs space-y-4">
                <h2 class="text-sm font-bold text-slate-900 dark:text-white">Review Booking Summary</h2>

                <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/60 dark:border-slate-700/60 space-y-3 text-xs">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Workspace / Clinic</span>
                        <span class="font-bold text-slate-900 dark:text-white">{{ $activeClinic->name ?? 'Sunrise Clinic' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Service Fee</span>
                        <span class="font-bold text-[#1E56E3]">Payable at Clinic Desk</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Booking Policy</span>
                        <span class="text-slate-600 dark:text-slate-300">Free cancellation up to 2 hours before slot</span>
                    </div>
                </div>
            </div>

            <div class="flex justify-between items-center">
                <button type="button" @click="step = 2" class="px-5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-semibold hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                    &larr; Back to Schedule
                </button>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#1E56E3] hover:bg-blue-600 text-white font-semibold text-xs shadow-md shadow-blue-600/25 transition">
                    Confirm & Book Appointment
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
