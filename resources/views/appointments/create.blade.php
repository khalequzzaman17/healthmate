@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Book Appointment</h1>
            <p class="text-xs text-slate-500 mt-0.5">Select doctor, service, and allocate time slot</p>
        </div>
        <a href="{{ route('appointments.index') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900">&larr; Back to schedule</a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 sm:p-8">
        <form action="{{ route('appointments.store') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Select Patient *</label>
                <select name="patient_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-sky-500">
                    <option value="">-- Choose Patient --</option>
                    @foreach($patients as $p)
                        <option value="{{ $p->id }}" {{ (old('patient_id', $preselectedPatientId) == $p->id) ? 'selected' : '' }}>
                            {{ $p->fullName }} ({{ $p->patient_number }}) &bull; {{ $p->phone }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Select Doctor *</label>
                    <select name="doctor_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-sky-500">
                        <option value="">-- Choose Doctor --</option>
                        @foreach($doctors as $d)
                            <option value="{{ $d->id }}" {{ old('doctor_id') == $d->id ? 'selected' : '' }}>
                                Dr. {{ $d->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Clinical Service</label>
                    <select name="service_id" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-sky-500">
                        <option value="">-- General / Standard Consult --</option>
                        @foreach($services as $s)
                            <option value="{{ $s->id }}" {{ old('service_id') == $s->id ? 'selected' : '' }}>
                                {{ $s->name }} (BDT {{ number_format($s->price) }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Date *</label>
                    <input type="date" name="scheduled_date" value="{{ old('scheduled_date', now()->toDateString()) }}" required
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-sky-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Time Slot *</label>
                    <input type="time" name="scheduled_time" value="{{ old('scheduled_time', '11:00') }}" required
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-sky-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Duration (Minutes)</label>
                <select name="duration_minutes" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-sky-500">
                    <option value="15">15 minutes</option>
                    <option value="20" selected>20 minutes</option>
                    <option value="30">30 minutes</option>
                    <option value="45">45 minutes</option>
                    <option value="60">60 minutes</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Notes / Reason for Visit</label>
                <textarea name="notes" rows="2" placeholder="e.g. Follow-up on blood glucose levels and dizziness"
                          class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-sky-500">{{ old('notes') }}</textarea>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('appointments.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 hover:bg-slate-50">Cancel</a>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-sky-600 hover:bg-sky-500 text-white font-semibold text-xs shadow-sm transition">
                    Book & Confirm Slot
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
