@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">SaaS Subscription Plans</h1>
            <p class="text-xs text-slate-500 mt-0.5">Tiered pricing, features, and doctor/patient quotas</p>
        </div>
    </div>

    <!-- Plans Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @foreach($plans as $plan)
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">{{ $plan->name }}</h2>
                        <span class="text-xs text-slate-400 font-mono">{{ $plan->slug }}</span>
                    </div>
                    <div class="text-right">
                        <span class="text-2xl font-bold text-sky-700">BDT {{ number_format($plan->price) }}</span>
                        <span class="text-xs text-slate-400">/ {{ $plan->interval }}</span>
                    </div>
                </div>

                <div class="py-3 border-y border-slate-100 grid grid-cols-2 gap-2 text-xs">
                    <div>
                        <span class="text-slate-400 block">Max Doctors</span>
                        <strong class="text-slate-800">{{ $plan->max_doctors }} practitioners</strong>
                    </div>
                    <div>
                        <span class="text-slate-400 block">Max Patients</span>
                        <strong class="text-slate-800">{{ $plan->max_patients }} patients</strong>
                    </div>
                </div>

                <div class="text-xs text-slate-600">
                    <span class="font-bold block text-slate-800 mb-1">Included Capabilities:</span>
                    <ul class="list-disc list-inside space-y-0.5 text-slate-500">
                        @foreach($plan->features ?? [] as $feature)
                            <li>{{ ucfirst(str_replace('_', ' ', $feature)) }}</li>
                        @endforeach
                    </ul>
                </div>

                <div class="pt-2 text-xs font-semibold text-sky-600">
                    {{ $plan->subscriptions_count }} active clinic subscriptions
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
