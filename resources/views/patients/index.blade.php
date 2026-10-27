@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Patient Directory</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Manage patient demographics, chronic care assignments, and clinical history</p>
        </div>
        <div>
            <a href="{{ route('patients.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-[#1E56E3] hover:bg-blue-600 text-white text-xs font-semibold shadow-md shadow-blue-600/25 transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                <span>Register New Patient</span>
            </a>
        </div>
    </div>

    <!-- Search & Filter bar -->
    <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-2xs transition-colors">
        <form method="GET" action="{{ route('patients.index') }}" class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <input type="text" name="search" value="{{ $search }}" placeholder="Search by patient name, phone number, or patient ID..."
                       class="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 placeholder-slate-400 text-xs focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
            </div>
            <button type="submit" class="px-4 py-2 rounded-xl bg-[#0A1629] dark:bg-slate-800 hover:bg-slate-800 dark:hover:bg-slate-700 text-white text-xs font-semibold shadow-2xs transition">
                Search
            </button>
            @if($search)
                <a href="{{ route('patients.index') }}" class="px-3 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-semibold text-center transition">
                    Clear
                </a>
            @endif
        </form>
    </div>

    <!-- Patients Table -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-2xs overflow-hidden transition-colors">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50/75 dark:bg-slate-800/40 text-slate-400 uppercase text-[10px] font-semibold tracking-wider border-b border-slate-100 dark:border-slate-800">
                    <tr>
                        <th class="px-5 py-3 font-medium">Patient ID</th>
                        <th class="px-4 py-3 font-medium">Full Name</th>
                        <th class="px-4 py-3 font-medium">Phone & Email</th>
                        <th class="px-4 py-3 font-medium">Gender / Age</th>
                        <th class="px-4 py-3 font-medium">Assigned Nurse</th>
                        <th class="px-4 py-3 font-medium text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60 text-slate-600 dark:text-slate-300">
                    @forelse($patients as $patient)
                        <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition">
                            <td class="px-5 py-3.5 font-mono font-semibold text-blue-600 dark:text-blue-400">{{ $patient->patient_number }}</td>
                            <td class="px-4 py-3.5 font-bold text-slate-900 dark:text-white">
                                <a href="{{ route('patients.show', $patient) }}" class="hover:text-blue-600 dark:hover:text-blue-400 transition">
                                    {{ $patient->fullName }}
                                </a>
                                @if($patient->blood_group)
                                    <span class="ml-1.5 px-1.5 py-0.5 text-[9px] rounded font-bold bg-rose-50 text-rose-700 dark:bg-rose-950/60 dark:text-rose-400 border border-rose-200/60 dark:border-rose-800/60">
                                        {{ $patient->blood_group }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="text-slate-900 dark:text-slate-200 font-medium">{{ $patient->phone }}</div>
                                <div class="text-[11px] text-slate-400 dark:text-slate-500">{{ $patient->email ?? 'No email' }}</div>
                            </td>
                            <td class="px-4 py-3.5 text-slate-700 dark:text-slate-300">
                                {{ ucfirst($patient->gender) }} {{ $patient->age ? "({$patient->age} yrs)" : '' }}
                            </td>
                            <td class="px-4 py-3.5">
                                @if($patient->activeAssignment)
                                    <span class="inline-flex items-center gap-1 font-medium text-slate-800 dark:text-slate-200">
                                        <svg class="w-3.5 h-3.5 text-teal-600 dark:text-teal-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                        {{ $patient->activeAssignment->nurse->name }}
                                    </span>
                                @else
                                    <span class="text-slate-400 dark:text-slate-500 italic">Unassigned</span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 text-right space-x-2">
                                <a href="{{ route('patients.show', $patient) }}" class="px-2.5 py-1.5 rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-100 dark:bg-blue-950/50 dark:text-blue-300 dark:hover:bg-blue-900/60 font-semibold text-[11px] transition">
                                    Timeline
                                </a>
                                <a href="{{ route('appointments.create', ['patient_id' => $patient->id]) }}" class="px-2.5 py-1.5 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 font-semibold text-[11px] transition">
                                    Book
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-8 text-center text-slate-400 dark:text-slate-500">
                                No patients found matching your search.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-5 py-3 border-t border-slate-100 dark:border-slate-800">
            {{ $patients->links() }}
        </div>
    </div>
</div>
@endsection
