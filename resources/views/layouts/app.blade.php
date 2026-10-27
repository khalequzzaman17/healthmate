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
               class="fixed inset-y-0 left-0 z-50 w-64 bg-[#0A1629] text-slate-300 flex flex-col transition-transform duration-300 ease-in-out lg:static lg:w-64 border-r border-[#15233c]">
            <!-- Brand -->
            <div class="h-16 px-5 flex items-center justify-between border-b border-[#15233c] bg-[#071120]">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-blue-600 to-sky-400 flex items-center justify-center text-white shadow-md shadow-blue-600/30">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                        </svg>
                    </div>
                    <span class="text-base font-bold tracking-tight text-white">HealthMate</span>
                </a>
                <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-white">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto text-xs font-medium">
                <!-- Dashboard -->
                <a href="{{ route('dashboard') }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('dashboard') || request()->routeIs('home') ? 'bg-[#1E56E3] text-white font-semibold shadow-md shadow-blue-600/30' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>
                    <span>Dashboard</span>
                </a>

                <!-- Patients -->
                <a href="{{ route('patients.index') }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('patients.*') ? 'bg-[#1E56E3] text-white font-semibold shadow-md shadow-blue-600/30' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                    <span>Patients</span>
                </a>

                <!-- Appointments -->
                <a href="{{ route('appointments.index') }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('appointments.*') ? 'bg-[#1E56E3] text-white font-semibold shadow-md shadow-blue-600/30' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                    <span>Appointments</span>
                </a>

                <!-- Nurse Calls -->
                <a href="{{ route('nurse.index') }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('nurse.*') ? 'bg-[#1E56E3] text-white font-semibold shadow-md shadow-blue-600/30' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                    <span>Nurse Calls</span>
                </a>

                <!-- Doctors -->
                <a href="{{ route('doctor.index') }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('doctor.*') ? 'bg-[#1E56E3] text-white font-semibold shadow-md shadow-blue-600/30' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                    <span>Doctors</span>
                </a>

                <!-- Billing & Payments -->
                <a href="{{ route('billing.index') }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('billing.*') ? 'bg-[#1E56E3] text-white font-semibold shadow-md shadow-blue-600/30' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" /></svg>
                    <span>Billing & Payments</span>
                </a>

                <!-- Commissions -->
                <a href="{{ route('commissions.index') }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('commissions.*') ? 'bg-[#1E56E3] text-white font-semibold shadow-md shadow-blue-600/30' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span>Commissions</span>
                </a>

                <!-- Reports -->
                <a href="{{ route('doctor.index') }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition text-slate-300 hover:bg-white/5 hover:text-white">
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                    <span>Reports</span>
                </a>

                <!-- Staff -->
                <a href="{{ route('patients.index') }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition text-slate-300 hover:bg-white/5 hover:text-white">
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                    <span>Staff</span>
                </a>

                <!-- Settings -->
                <a href="{{ route('portal.dashboard') }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition text-slate-300 hover:bg-white/5 hover:text-white">
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                    <span>Settings</span>
                </a>
            </nav>

            <!-- Bottom minimal logout -->
            <div class="p-3 border-t border-[#15233c] bg-[#071120] flex items-center justify-between text-xs text-slate-400">
                <span class="text-[10px]">v1.0 HealthMate SaaS</span>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" title="Logout" class="hover:text-rose-400 transition p-1">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content Wrapper -->
        <div class="flex-1 flex flex-col min-w-0 bg-slate-50 dark:bg-slate-950">
            <!-- Top Navbar -->
            <header class="h-16 bg-white dark:bg-slate-900 border-b border-slate-200/80 dark:border-slate-800 px-4 sm:px-6 lg:px-8 flex items-center justify-between sticky top-0 z-30 transition-colors">
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-xl text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                    </button>

                    <!-- Clinic Dropdown Chip (matching the image) -->
                    @if(isset($activeClinic) && $activeClinic)
                        <div class="relative" x-data="{ clinicDropdown: false }">
                            <button @click="clinicDropdown = !clinicDropdown" 
                                    @click.outside="clinicDropdown = false"
                                    type="button" 
                                    class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold text-slate-800 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/60 shadow-2xs transition">
                                <span>{{ $activeClinic->name }}</span>
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                            </button>

                            @if(isset($userClinics) && $userClinics->count() > 1)
                                <div x-show="clinicDropdown" class="absolute left-0 mt-2 w-52 bg-white dark:bg-slate-900 rounded-xl shadow-xl border border-slate-200 dark:border-slate-800 py-1.5 z-50 text-xs" style="display: none;">
                                    <div class="px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-slate-400">Switch Clinic</div>
                                    @foreach($userClinics as $c)
                                        <form action="{{ route('clinic.switch', $c) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="w-full text-left px-3 py-2 flex items-center justify-between hover:bg-slate-50 dark:hover:bg-slate-800 {{ $c->id === $activeClinic->id ? 'text-blue-600 dark:text-blue-400 font-bold bg-blue-50/50 dark:bg-blue-950/30' : 'text-slate-700 dark:text-slate-300' }}">
                                                <span class="truncate">{{ $c->name }}</span>
                                                @if($c->id === $activeClinic->id)
                                                    <svg class="w-3.5 h-3.5 text-blue-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                                                @endif
                                            </button>
                                        </form>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

                <div class="flex items-center gap-3">
                    <!-- Notification Bell with indicator dot -->
                    <button type="button" class="relative p-2 rounded-xl text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        <span class="absolute top-2 right-2 w-2 h-2 rounded-full bg-rose-500 ring-2 ring-white dark:ring-slate-900"></span>
                    </button>
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

                    <div class="h-6 w-px bg-slate-200 dark:bg-slate-800 hidden sm:block"></div>

                    <!-- User Profile Dropdown Pill -->
                    <div class="relative" x-data="{ userMenu: false }">
                        <button @click="userMenu = !userMenu" @click.outside="userMenu = false" type="button" class="flex items-center gap-2.5 p-1 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-blue-600 to-sky-400 text-white font-bold text-xs flex items-center justify-center shadow-xs">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                            <div class="text-left hidden sm:block">
                                <div class="text-xs font-bold text-slate-800 dark:text-slate-100 leading-tight">{{ auth()->user()->name }}</div>
                                <div class="text-[10px] text-slate-400 font-medium leading-tight">
                                    {{ ucfirst($userRole ?? (auth()->user()->is_super_admin ? 'Platform Admin' : 'Staff')) }}
                                </div>
                            </div>
                            <svg class="w-3.5 h-3.5 text-slate-400 hidden sm:block" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                        </button>

                        <div x-show="userMenu" class="absolute right-0 mt-2 w-48 bg-white dark:bg-slate-900 rounded-xl shadow-xl border border-slate-200 dark:border-slate-800 py-1.5 z-50 text-xs" style="display: none;">
                            <div class="px-3 py-1.5 border-b border-slate-100 dark:border-slate-800 text-[11px] text-slate-500">
                                Signed in as <strong class="text-slate-800 dark:text-slate-200 block truncate">{{ auth()->user()->email }}</strong>
                            </div>
                            <a href="{{ route('dashboard') }}" class="block px-3 py-2 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800">My Workspace</a>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full text-left px-3 py-2 text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40">Sign Out</button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Alerts & Flash Messages -->
            <div class="px-4 sm:px-6 lg:px-8 pt-4">
                @if(session('success'))
                    <div class="mb-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-900/60 p-4 text-sm text-emerald-800 dark:text-emerald-300 flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <span>{{ session('success') }}</span>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-4 rounded-xl bg-rose-50 dark:bg-rose-950/50 border border-rose-200 dark:border-rose-900/60 p-4 text-sm text-rose-800 dark:text-rose-300 flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <svg class="w-5 h-5 text-rose-600 dark:text-rose-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <span>{{ session('error') }}</span>
                        </div>
                    </div>
                @endif

                @if(isset($errors) && $errors->any())
                    <div class="mb-4 rounded-xl bg-rose-50 dark:bg-rose-950/50 border border-rose-200 dark:border-rose-900/60 p-4 text-sm text-rose-800 dark:text-rose-300">
                        <div class="font-semibold mb-1">Please check the following errors:</div>
                        <ul class="list-disc list-inside space-y-0.5 text-xs text-rose-700 dark:text-rose-400">
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
