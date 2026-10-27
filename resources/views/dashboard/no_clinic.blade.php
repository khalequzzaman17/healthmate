@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto my-12 text-center bg-white p-8 rounded-2xl border border-slate-200/80 shadow-xs">
    <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center mx-auto mb-4">
        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
    </div>
    <h2 class="text-lg font-bold text-slate-900">No Active Clinic Workspace</h2>
    <p class="text-xs text-slate-500 mt-2">You currently do not have an active membership in any clinic. Please contact your clinic administrator or register a new clinic.</p>
    <div class="mt-6">
        <a href="{{ route('register') }}" class="inline-flex items-center px-4 py-2 rounded-xl bg-sky-600 text-white text-xs font-semibold hover:bg-sky-500">
            Create Clinic Workspace
        </a>
    </div>
</div>
@endsection
