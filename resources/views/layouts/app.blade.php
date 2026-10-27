<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'HealthMate') }} — Healthcare Management SaaS</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    <!-- Pre-render Theme Application (Zero FOUC) -->
    <script data-cfasync="false">
        (function() {
            try {
                const stored = localStorage.getItem('theme') || 'system';
                const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                if (stored === 'dark' || (stored === 'system' && prefersDark)) {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            } catch (e) {}
        })();
    </script>

    <!-- Tailwind CSS CDN (with Rocket Loader bypass) -->
    <script data-cfasync="false" src="https://cdn.tailwindcss.com"></script>
    <script data-cfasync="false">
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Instrument Sans', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <!-- Alpine.js CDN (with Rocket Loader bypass) -->
    <script data-cfasync="false" src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script data-cfasync="false">
        document.addEventListener('alpine:init', () => {
            Alpine.store('theme', {
                current: localStorage.getItem('theme') || 'system',
                systemDark: window.matchMedia('(prefers-color-scheme: dark)').matches,
                
                init() {
                    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
                        this.systemDark = e.matches;
                        if (this.current === 'system') {
                            this.apply();
                        }
                    });
                    this.apply();
                },

                set(val) {
                    this.current = val;
                    localStorage.setItem('theme', val);
                    this.apply();
                },

                apply() {
                    const isDark = this.current === 'dark' || (this.current === 'system' && this.systemDark);
                    if (isDark) {
                        document.documentElement.classList.add('dark');
                    } else {
                        document.documentElement.classList.remove('dark');
                    }
                }
            });
        });
    </script>

    <style>
        html {
            color-scheme: light dark;
        }
        html.dark {
            color-scheme: dark;
        }
        .dark .bg-white {
            background-color: #0f172a !important; /* slate-900 */
        }
        .dark .bg-slate-50 {
            background-color: #020617 !important; /* slate-950 */
        }
        .dark .border-slate-200, 
        .dark .border-slate-200\/80,
        .dark .border-slate-100 {
            border-color: #1e293b !important; /* slate-800 */
        }
        .dark .text-slate-900 {
            color: #f8fafc !important; /* slate-50 */
        }
        .dark .text-slate-800 {
            color: #f1f5f9 !important; /* slate-100 */
        }
        .dark .text-slate-700 {
            color: #e2e8f0 !important; /* slate-200 */
        }
        .dark .text-slate-600 {
            color: #cbd5e1 !important; /* slate-300 */
        }
        .dark .text-slate-500 {
            color: #94a3b8 !important; /* slate-400 */
        }
        .dark .hover\:bg-slate-50:hover,
        .dark .hover\:bg-slate-100:hover {
            background-color: #1e293b !important; /* slate-800 */
        }
        .dark .divide-slate-100 > :not([hidden]) ~ :not([hidden]),
        .dark .divide-slate-200 > :not([hidden]) ~ :not([hidden]) {
            border-color: #1e293b !important;
        }
        .dark input, 
        .dark select, 
        .dark textarea {
            background-color: #0f172a !important;
            color: #f8fafc !important;
            border-color: #334155 !important;
        }
        .dark input::placeholder, 
        .dark textarea::placeholder {
            color: #64748b !important;
        }
        .dark .bg-sky-50 {
            background-color: rgba(14, 165, 233, 0.12) !important;
        }
        .dark .bg-teal-50 {
            background-color: rgba(20, 184, 166, 0.12) !important;
        }
        .dark .bg-emerald-50 {
            background-color: rgba(16, 185, 129, 0.12) !important;
        }
        .dark .bg-amber-50 {
            background-color: rgba(245, 158, 11, 0.12) !important;
        }
        .dark .bg-rose-50 {
            background-color: rgba(244, 63, 94, 0.12) !important;
        }
        .dark .bg-purple-50 {
            background-color: rgba(168, 85, 247, 0.12) !important;
        }
    </style>
</head>
<body class="h-full antialiased font-sans text-slate-800 dark:text-slate-100 bg-slate-50 dark:bg-slate-950" x-data="{ sidebarOpen: false, clinicMenuOpen: false, userMenuOpen: false }">
    <div class="min-h-full flex flex-col lg:flex-row">
        <!-- Mobile Sidebar Backdrop -->
        <div x-show="sidebarOpen" 
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="sidebarOpen = false" 
             class="fixed inset-0 bg-slate-900/80 z-40 lg:hidden" style="display: none;"></div>

        <!-- Sidebar Navigation -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
               class="fixed inset-y-0 left-0 z-50 w-72 bg-slate-900 text-slate-300 flex flex-col transition-transform duration-300 ease-in-out lg:static lg:w-64 xl:w-72">
            <!-- Brand -->
            <div class="h-16 px-6 flex items-center justify-between border-b border-slate-800 bg-slate-950">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-sky-500 to-teal-400 flex items-center justify-center text-white shadow-md shadow-sky-500/20">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                        </svg>
                    </div>
                    <div>
                        <span class="text-lg font-bold tracking-tight text-white">HealthMate</span>
                        <span class="block text-[10px] uppercase font-semibold text-teal-400 tracking-wider">Multi-Clinic SaaS</span>
                    </div>
                </a>
                <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-white">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <!-- Active Clinic Workspace Selector -->
            @if(isset($activeClinic) && $activeClinic)
                <div class="p-4 border-b border-slate-800 bg-slate-900/60">
                    <div class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-1.5 flex items-center justify-between">
                        <span>Workspace</span>
                        <span class="px-1.5 py-0.5 rounded text-[9px] font-bold uppercase bg-sky-500/10 text-sky-400 border border-sky-500/20">
                            {{ ucfirst($userRole ?? 'staff') }}
                        </span>
                    </div>
                    <div class="relative">
                        <button @click="clinicMenuOpen = !clinicMenuOpen" class="w-full flex items-center justify-between gap-2 px-3 py-2 rounded-lg bg-slate-800/80 hover:bg-slate-800 text-left text-sm font-medium text-white transition border border-slate-700/60">
                            <span class="truncate">{{ $activeClinic->name }}</span>
                            <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                        </button>
                        
                        <!-- Dropdown for multiple clinic memberships -->
                        @if(isset($userClinics) && $userClinics->count() > 1)
                            <div x-show="clinicMenuOpen" @click.away="clinicMenuOpen = false" class="absolute left-0 right-0 mt-1 bg-slate-800 rounded-lg shadow-xl border border-slate-700 py-1 z-30" style="display: none;">
                                <div class="px-3 py-1.5 text-[11px] text-slate-400 font-semibold border-b border-slate-700">Switch Clinic</div>
                                @foreach($userClinics as $c)
                                    <form action="{{ route('clinic.switch', $c) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="w-full text-left px-3 py-2 text-xs flex items-center justify-between hover:bg-slate-700/70 {{ $c->id === $activeClinic->id ? 'text-teal-300 font-bold' : 'text-slate-300' }}">
                                            <span class="truncate">{{ $c->name }}</span>
                                            @if($c->id === $activeClinic->id)
                                                <svg class="w-3.5 h-3.5 text-teal-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                                            @endif
                                        </button>
                                    </form>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Navigation Links -->
            <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto text-sm">
                <!-- Dashboard -->
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('dashboard') || request()->routeIs('home') ? 'bg-sky-600 text-white font-medium shadow-sm' : 'hover:bg-slate-800 text-slate-300' }}">
                    <svg class="w-5 h-5 shrink-0 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>
                    <span>Dashboard</span>
                </a>

                <!-- Clinical Care (for staff) -->
                @if(in_array($userRole ?? '', ['admin', 'doctor', 'nurse', 'receptionist', 'accountant', 'super_admin']))
                    <div class="pt-4 pb-1 px-3 text-[11px] font-bold uppercase tracking-wider text-slate-500">Clinical Operations</div>

                    <!-- Patients -->
                    <a href="{{ route('patients.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('patients.*') ? 'bg-sky-600 text-white font-medium shadow-sm' : 'hover:bg-slate-800 text-slate-300' }}">
                        <svg class="w-5 h-5 shrink-0 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                        <span>Patients</span>
                    </a>

                    <!-- Appointments -->
                    <a href="{{ route('appointments.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('appointments.*') ? 'bg-sky-600 text-white font-medium shadow-sm' : 'hover:bg-slate-800 text-slate-300' }}">
                        <svg class="w-5 h-5 shrink-0 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                        <span>Appointments</span>
                    </a>
                @endif

                <!-- Nurse Workspace -->
                @if(in_array($userRole ?? '', ['nurse', 'admin', 'super_admin']))
                    <div class="pt-4 pb-1 px-3 text-[11px] font-bold uppercase tracking-wider text-slate-500">Nurse Care Desk</div>
                    <a href="{{ route('nurse.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('nurse.*') ? 'bg-sky-600 text-white font-medium shadow-sm' : 'hover:bg-slate-800 text-slate-300' }}">
                        <svg class="w-5 h-5 shrink-0 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                        <span>Call Queue & Vitals</span>
                    </a>
                @endif

                <!-- Doctor Workspace -->
                @if(in_array($userRole ?? '', ['doctor', 'admin', 'super_admin']))
                    <div class="pt-4 pb-1 px-3 text-[11px] font-bold uppercase tracking-wider text-slate-500">Doctor Desk</div>
                    <a href="{{ route('doctor.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('doctor.*') ? 'bg-sky-600 text-white font-medium shadow-sm' : 'hover:bg-slate-800 text-slate-300' }}">
                        <svg class="w-5 h-5 shrink-0 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                        <span>Review & Consult</span>
                    </a>
                @endif

                <!-- Finance & Billing -->
                @if(in_array($userRole ?? '', ['admin', 'accountant', 'receptionist', 'doctor', 'nurse', 'super_admin']))
                    <div class="pt-4 pb-1 px-3 text-[11px] font-bold uppercase tracking-wider text-slate-500">Finance & Ledgers</div>
                    
                    @if(in_array($userRole ?? '', ['admin', 'receptionist', 'accountant', 'super_admin']))
                        <a href="{{ route('billing.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('billing.*') ? 'bg-sky-600 text-white font-medium shadow-sm' : 'hover:bg-slate-800 text-slate-300' }}">
                            <svg class="w-5 h-5 shrink-0 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <span>Billing & Invoices</span>
                        </a>
                    @endif

                    <a href="{{ route('commissions.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('commissions.*') ? 'bg-sky-600 text-white font-medium shadow-sm' : 'hover:bg-slate-800 text-slate-300' }}">
                        <svg class="w-5 h-5 shrink-0 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" /></svg>
                        <span>Commissions & Payouts</span>
                    </a>
                @endif

                <!-- Patient Portal Links -->
                @if(($userRole ?? '') === 'patient')
                    <div class="pt-4 pb-1 px-3 text-[11px] font-bold uppercase tracking-wider text-slate-500">My Health Portal</div>
                    <a href="{{ route('portal.dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('portal.dashboard') ? 'bg-sky-600 text-white font-medium shadow-sm' : 'hover:bg-slate-800 text-slate-300' }}">
                        <svg class="w-5 h-5 shrink-0 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                        <span>Portal Home</span>
                    </a>
                    <a href="{{ route('portal.book') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('portal.book') ? 'bg-sky-600 text-white font-medium shadow-sm' : 'hover:bg-slate-800 text-slate-300' }}">
                        <svg class="w-5 h-5 shrink-0 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                        <span>Book Appointment</span>
                    </a>
                    <a href="{{ route('portal.records') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('portal.records') ? 'bg-sky-600 text-white font-medium shadow-sm' : 'hover:bg-slate-800 text-slate-300' }}">
                        <svg class="w-5 h-5 shrink-0 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                        <span>My Medical Records</span>
                    </a>
                @endif

                <!-- Super Admin Platform Links -->
                @if(auth()->user()->is_super_admin)
                    <div class="pt-4 pb-1 px-3 text-[11px] font-bold uppercase tracking-wider text-amber-500/90">Platform Administration</div>
                    <a href="{{ route('superadmin.clinics') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('superadmin.clinics') ? 'bg-amber-600 text-white font-medium shadow-sm' : 'hover:bg-slate-800 text-slate-300' }}">
                        <svg class="w-5 h-5 shrink-0 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                        <span>All Clinic Tenants</span>
                    </a>
                    <a href="{{ route('superadmin.plans') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('superadmin.plans') ? 'bg-amber-600 text-white font-medium shadow-sm' : 'hover:bg-slate-800 text-slate-300' }}">
                        <svg class="w-5 h-5 shrink-0 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" /></svg>
                        <span>SaaS Plans & Limits</span>
                    </a>
                @endif
            </nav>

            <!-- User profile footer in sidebar -->
            <div class="p-4 border-t border-slate-800 bg-slate-950 flex items-center justify-between">
                <div class="flex items-center gap-3 truncate">
                    <div class="w-8 h-8 rounded-full bg-slate-700 flex items-center justify-center font-bold text-xs text-white shrink-0">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="truncate">
                        <div class="text-xs font-semibold text-white truncate">{{ auth()->user()->name }}</div>
                        <div class="text-[11px] text-slate-400 truncate">{{ auth()->user()->email }}</div>
                    </div>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" title="Logout" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-400 hover:bg-slate-800 transition">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content Wrapper -->
        <div class="flex-1 flex flex-col min-w-0 bg-slate-50 dark:bg-slate-950">
            <!-- Top Navbar -->
            <header class="h-16 bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 px-4 sm:px-6 lg:px-8 flex items-center justify-between sticky top-0 z-30">
                <div class="flex items-center gap-4">
                    <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-md text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                    </button>
                    @if(isset($activeClinic) && $activeClinic)
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-semibold text-slate-800 dark:text-slate-100">{{ $activeClinic->name }}</span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                                {{ $activeClinic->city ?? 'Dhaka' }} &bull; {{ $activeClinic->timezone ?? 'Asia/Dhaka' }}
                            </span>
                        </div>
                    @endif
                </div>

                <div class="flex items-center gap-3">
                    <span class="text-xs text-slate-500 dark:text-slate-400 hidden sm:inline-block">Currency: <strong>{{ $activeClinic->currency ?? 'BDT' }}</strong></span>
                    <div class="h-5 w-px bg-slate-200 dark:bg-slate-800 hidden sm:block"></div>

                    <!-- Theme Mode Switcher (Light / Dark / Auto) -->
                    <div class="relative" x-data="{ themeMenuOpen: false }">
                        <button @click="themeMenuOpen = !themeMenuOpen" 
                                @click.outside="themeMenuOpen = false"
                                type="button" 
                                title="Change Color Theme" 
                                class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/80 text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                            <!-- Sun icon for light -->
                            <template x-if="$store.theme.current === 'light'">
                                <svg class="w-4 h-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </template>
                            <!-- Moon icon for dark -->
                            <template x-if="$store.theme.current === 'dark'">
                                <svg class="w-4 h-4 text-sky-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                                </svg>
                            </template>
                            <!-- Monitor icon for auto / system -->
                            <template x-if="$store.theme.current === 'system'">
                                <svg class="w-4 h-4 text-slate-500 dark:text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </template>
                            <span class="text-xs font-semibold capitalize hidden md:inline" x-text="$store.theme.current === 'system' ? 'Auto' : $store.theme.current"></span>
                            <svg class="w-3 h-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="themeMenuOpen" 
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-44 bg-white dark:bg-slate-900 rounded-xl shadow-xl border border-slate-200 dark:border-slate-800 py-1.5 z-50 text-xs" 
                             style="display: none;">
                            <div class="px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Theme Appearance</div>
                            <!-- Light option -->
                            <button type="button" 
                                    @click="$store.theme.set('light'); themeMenuOpen = false" 
                                    :class="$store.theme.current === 'light' ? 'bg-sky-50 dark:bg-sky-950/60 text-sky-600 dark:text-sky-400 font-semibold' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800'"
                                    class="w-full flex items-center justify-between px-3 py-2 transition text-left">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                    <span>White / Light</span>
                                </div>
                                <template x-if="$store.theme.current === 'light'">
                                    <svg class="w-4 h-4 text-sky-600 dark:text-sky-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                </template>
                            </button>
                            <!-- Dark option -->
                            <button type="button" 
                                    @click="$store.theme.set('dark'); themeMenuOpen = false" 
                                    :class="$store.theme.current === 'dark' ? 'bg-sky-50 dark:bg-sky-950/60 text-sky-600 dark:text-sky-400 font-semibold' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800'"
                                    class="w-full flex items-center justify-between px-3 py-2 transition text-left">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-sky-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                                    </svg>
                                    <span>Dark Mode</span>
                                </div>
                                <template x-if="$store.theme.current === 'dark'">
                                    <svg class="w-4 h-4 text-sky-600 dark:text-sky-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                </template>
                            </button>
                            <!-- System Auto option -->
                            <button type="button" 
                                    @click="$store.theme.set('system'); themeMenuOpen = false" 
                                    :class="$store.theme.current === 'system' ? 'bg-sky-50 dark:bg-sky-950/60 text-sky-600 dark:text-sky-400 font-semibold' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800'"
                                    class="w-full flex items-center justify-between px-3 py-2 transition text-left">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-slate-500 dark:text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    <span>Auto (System)</span>
                                </div>
                                <template x-if="$store.theme.current === 'system'">
                                    <svg class="w-4 h-4 text-sky-600 dark:text-sky-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                </template>
                            </button>
                        </div>
                    </div>

                    <div class="h-5 w-px bg-slate-200 dark:bg-slate-800 hidden sm:block"></div>
                    <span class="text-xs text-slate-700 dark:text-slate-300 font-medium">{{ auth()->user()->name }}</span>
                </div>
            </header>

            <!-- Alerts & Flash Messages -->
            <div class="px-4 sm:px-6 lg:px-8 pt-4">
                @if(session('success'))
                    <div class="mb-4 rounded-xl bg-emerald-50 border border-emerald-200 p-4 text-sm text-emerald-800 flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <span>{{ session('success') }}</span>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-4 rounded-xl bg-rose-50 border border-rose-200 p-4 text-sm text-rose-800 flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <span>{{ session('error') }}</span>
                        </div>
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-4 rounded-xl bg-rose-50 border border-rose-200 p-4 text-sm text-rose-800">
                        <div class="font-semibold mb-1">Please check the following errors:</div>
                        <ul class="list-disc list-inside space-y-0.5 text-xs text-rose-700">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>

            <!-- Page Content -->
            <main class="flex-1 px-4 sm:px-6 lg:px-8 py-6">
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
