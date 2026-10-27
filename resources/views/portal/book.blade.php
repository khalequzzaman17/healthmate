@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Book Doctor Consultation</h1>
            <p class="text-xs text-slate-500 mt-0.5">Select a doctor and schedule an available consultation slot</p>
        </div>
        <a href="{{ route('portal.dashboard') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900">&larr; Back to portal</a>
    </div>

    <form action="{{ route('portal.book.store') }}" method="POST" class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 sm:p-8 space-y-4">
        @csrf

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
                <option value="">-- Standard Consultation --</option>
                @foreach($services as $s)
                    <option value="{{ $s->id }}" {{ old('service_id') == $s->id ? 'selected' : '' }}>
                        {{ $s->name }} (BDT {{ number_format($s->price) }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Preferred Date *</label>
                <input type="date" name="scheduled_date" value="{{ old('scheduled_date', now()->addDay()->toDateString()) }}" required min="{{ now()->toDateString() }}"
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-sky-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Preferred Time *</label>
                <input type="time" name="scheduled_time" value="{{ old('scheduled_time', '10:30') }}" required
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-sky-500">
            </div>
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Reason for Visit / Symptoms</label>
            <textarea name="notes" rows="3" placeholder="Briefly explain symptoms or reason for appointment"
                      class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-sky-500">{{ old('notes') }}</textarea>
        </div>

        <div class="pt-2">
            <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-sky-600 hover:bg-sky-500 text-white font-semibold text-xs shadow-md shadow-sky-600/20 transition">
                Confirm & Request Booking
            </button>
        </div>
    </form>
</div>
@endsection
