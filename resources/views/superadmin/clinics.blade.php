@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Clinic Tenants Directory</h1>
            <p class="text-xs text-slate-500 mt-0.5">Manage multi-tenant clinic instances, subscriptions, and operational statuses</p>
        </div>
    </div>

    <!-- Clinics Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-[10px] text-slate-500 uppercase font-semibold border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3">Clinic Name & Slug</th>
                        <th class="px-4 py-3">City / Contact</th>
                        <th class="px-4 py-3">Subscription</th>
                        <th class="px-4 py-3 text-center">Patients</th>
                        <th class="px-4 py-3 text-center">Appointments</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($clinics as $clinic)
                        <tr class="hover:bg-slate-50/50">
                            <td class="px-5 py-3.5">
                                <div class="font-bold text-slate-900">{{ $clinic->name }}</div>
                                <div class="font-mono text-[10px] text-slate-400">slug: {{ $clinic->slug }}</div>
                            </td>
                            <td class="px-4 py-3.5">
                                <div>{{ $clinic->city }}</div>
                                <div class="text-[11px] text-slate-400">{{ $clinic->phone }}</div>
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="font-semibold text-slate-800">{{ $clinic->activeSubscription?->plan?->name ?? 'None' }}</span>
                                <div class="text-[10px] text-emerald-600 font-bold uppercase">{{ $clinic->activeSubscription?->status ?? 'Inactive' }}</div>
                            </td>
                            <td class="px-4 py-3.5 text-center font-bold text-slate-900">{{ $clinic->patients_count }}</td>
                            <td class="px-4 py-3.5 text-center font-bold text-slate-900">{{ $clinic->appointments_count }}</td>
                            <td class="px-4 py-3.5">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold
                                    {{ $clinic->status === 'active' ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                                    {{ ucfirst($clinic->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-right">
                                <form action="{{ route('superadmin.clinics.toggle', $clinic) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-2.5 py-1 rounded-lg text-xs font-semibold
                                        {{ $clinic->status === 'active' ? 'text-rose-600 hover:bg-rose-50' : 'text-emerald-600 hover:bg-emerald-50' }}">
                                        {{ $clinic->status === 'active' ? 'Suspend' : 'Reactivate' }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-8 text-center text-slate-400">No clinics registered on platform.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-5 py-3 border-t border-slate-100">
            {{ $clinics->links() }}
        </div>
    </div>
</div>
@endsection
