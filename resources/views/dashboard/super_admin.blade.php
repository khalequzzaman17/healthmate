@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">HealthMate Platform Administration</h1>
            <p class="text-xs text-slate-500 mt-0.5">Global SaaS infrastructure, clinic tenants, and subscription controls</p>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Clinic Workspaces</span>
                <div class="text-2xl font-bold text-slate-900 mt-1">{{ $totalClinics }}</div>
                <div class="text-[11px] text-emerald-600 font-medium mt-1">Multi-tenant instances</div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Active Subscriptions</span>
                <div class="text-2xl font-bold text-emerald-600 mt-1">{{ $activeSubscriptions }}</div>
                <div class="text-[11px] text-slate-500 font-medium mt-1">Paying clinic accounts</div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Platform Health</span>
                <div class="text-2xl font-bold text-teal-600 mt-1">Operational</div>
                <div class="text-[11px] text-slate-500 font-medium mt-1">Tenant isolation enforced</div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
            </div>
        </div>
    </div>

    <!-- Recent Clinics -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-sm font-bold text-slate-900">Registered Clinics</h2>
            <a href="{{ route('superadmin.clinics') }}" class="text-xs font-semibold text-sky-600 hover:text-sky-700">Manage all clinics &rarr;</a>
        </div>

        <div class="divide-y divide-slate-100">
            @foreach($recentClinics as $c)
                <div class="p-4 flex items-center justify-between hover:bg-slate-50/50">
                    <div>
                        <div class="text-sm font-bold text-slate-900">{{ $c->name }}</div>
                        <div class="text-xs text-slate-500 mt-0.5">
                            Slug: <span class="font-mono text-slate-700">{{ $c->slug }}</span> &bull; {{ $c->city }} &bull; {{ $c->activeSubscription?->plan?->name ?? 'Free Plan' }}
                        </div>
                    </div>
                    <div>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $c->status === 'active' ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                            {{ ucfirst($c->status) }}
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
