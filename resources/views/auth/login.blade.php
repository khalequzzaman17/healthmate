<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login — HealthMate Multi-Clinic SaaS</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    <!-- Zero-FOUC Theme Initialization -->
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
    </style>
</head>
<body class="min-h-full flex flex-col items-center justify-center p-4 font-sans text-slate-800 dark:text-slate-100 bg-slate-50 dark:bg-slate-950 transition-colors duration-200" 
      x-data="{ email: 'admin@carepoint.test', password: 'password', themeOpen: false }">

    <!-- Top floating Theme Switcher -->
    <div class="fixed top-4 right-4 z-50">
        <div class="relative">
            <button @click="themeOpen = !themeOpen" 
                    @click.outside="themeOpen = false"
                    type="button" 
                    title="Change Theme (White, Dark, Auto)" 
                    class="flex items-center gap-1.5 px-3 py-1.5 rounded-full border border-slate-200 dark:border-slate-800 bg-white/90 dark:bg-slate-900/90 backdrop-blur shadow-sm text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition text-xs font-medium">
                <template x-if="$store.theme.current === 'light'">
                    <svg class="w-4 h-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </template>
                <template x-if="$store.theme.current === 'dark'">
                    <svg class="w-4 h-4 text-sky-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                </template>
                <template x-if="$store.theme.current === 'system'">
                    <svg class="w-4 h-4 text-slate-500 dark:text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </template>
                <span class="capitalize" x-text="$store.theme.current === 'system' ? 'Auto' : $store.theme.current"></span>
                <svg class="w-3 h-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            <div x-show="themeOpen" 
                 x-transition
                 class="absolute right-0 mt-2 w-40 bg-white dark:bg-slate-900 rounded-xl shadow-xl border border-slate-200 dark:border-slate-800 py-1 z-50 text-xs" 
                 style="display: none;">
                <button type="button" @click="$store.theme.set('light'); themeOpen = false" 
                        :class="$store.theme.current === 'light' ? 'bg-sky-50 dark:bg-sky-950 text-sky-600 dark:text-sky-400 font-semibold' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800'"
                        class="w-full flex items-center gap-2 px-3 py-2 transition text-left">
                    <svg class="w-4 h-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                    <span>White / Light</span>
                </button>
                <button type="button" @click="$store.theme.set('dark'); themeOpen = false" 
                        :class="$store.theme.current === 'dark' ? 'bg-sky-50 dark:bg-sky-950 text-sky-600 dark:text-sky-400 font-semibold' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800'"
                        class="w-full flex items-center gap-2 px-3 py-2 transition text-left">
                    <svg class="w-4 h-4 text-sky-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
                    <span>Dark Mode</span>
                </button>
                <button type="button" @click="$store.theme.set('system'); themeOpen = false" 
                        :class="$store.theme.current === 'system' ? 'bg-sky-50 dark:bg-sky-950 text-sky-600 dark:text-sky-400 font-semibold' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800'"
                        class="w-full flex items-center gap-2 px-3 py-2 transition text-left">
                    <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                    <span>Auto (System)</span>
                </button>
            </div>
        </div>
    </div>

    <div class="w-full max-w-md my-8">
        <!-- Logo -->
        <div class="text-center mb-6">
            <div class="inline-flex w-12 h-12 rounded-2xl bg-gradient-to-tr from-sky-500 to-teal-400 items-center justify-center text-white shadow-xl shadow-sky-500/20 mb-3">
                <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">HealthMate</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Multi-Clinic Healthcare Management SaaS</p>
        </div>

        <!-- Login Card -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-xl p-6 sm:p-8 border border-slate-200 dark:border-slate-800 transition-colors">
            <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-1">Sign in to your account</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mb-6">Access your clinic workspace, patient care desks, or portal</p>

            @if($errors->any())
                <div class="mb-4 rounded-xl bg-rose-50 dark:bg-rose-950/60 border border-rose-200 dark:border-rose-800 p-3 text-xs text-rose-700 dark:text-rose-300">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Email Address</label>
                    <input type="email" name="email" x-model="email" required autofocus
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent transition">
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300">Password</label>
                    </div>
                    <input type="password" name="password" x-model="password" required
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent transition">
                </div>

                <div class="flex items-center justify-between text-xs">
                    <label class="flex items-center gap-2 cursor-pointer text-slate-600 dark:text-slate-400">
                        <input type="checkbox" name="remember" class="rounded border-slate-300 dark:border-slate-700 text-sky-600 focus:ring-sky-500">
                        <span>Remember this device</span>
                    </label>
                </div>

                <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-sky-600 hover:bg-sky-500 text-white font-semibold text-sm shadow-md shadow-sky-600/25 transition">
                    Sign In
                </button>
            </form>

            <div class="mt-6 pt-6 border-t border-slate-100 dark:border-slate-800 text-center text-xs text-slate-500 dark:text-slate-400">
                Want to register a new clinic?
                <a href="{{ route('register') }}" class="font-semibold text-sky-600 dark:text-sky-400 hover:underline">Create Workspace</a>
            </div>
        </div>

        <!-- Quick Demo Switcher -->
        <div class="mt-6 bg-slate-100 dark:bg-slate-900/80 rounded-2xl p-4 border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 transition-colors">
            <div class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-2 flex items-center justify-between">
                <span>Demo Quick Login Accounts</span>
                <span class="text-[10px] text-teal-600 dark:text-teal-400 font-semibold">Password: password</span>
            </div>
            <div class="grid grid-cols-2 gap-2 text-xs">
                <button type="button" @click="email = 'admin@carepoint.test'; password = 'password'"
                        class="p-2 rounded-lg bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-left transition border border-slate-200 dark:border-slate-700 shadow-xs">
                    <div class="font-semibold text-slate-900 dark:text-white">Clinic Admin</div>
                    <div class="text-[10px] text-slate-500 dark:text-slate-400 truncate">admin@carepoint.test</div>
                </button>
                <button type="button" @click="email = 'dr.tariq@carepoint.test'; password = 'password'"
                        class="p-2 rounded-lg bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-left transition border border-slate-200 dark:border-slate-700 shadow-xs">
                    <div class="font-semibold text-slate-900 dark:text-white">Doctor (Internal)</div>
                    <div class="text-[10px] text-slate-500 dark:text-slate-400 truncate">dr.tariq@carepoint.test</div>
                </button>
                <button type="button" @click="email = 'nurse.poly@carepoint.test'; password = 'password'"
                        class="p-2 rounded-lg bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-left transition border border-slate-200 dark:border-slate-700 shadow-xs">
                    <div class="font-semibold text-slate-900 dark:text-white">Nurse Poly</div>
                    <div class="text-[10px] text-slate-500 dark:text-slate-400 truncate">nurse.poly@carepoint.test</div>
                </button>
                <button type="button" @click="email = 'patient.abdul@healthmate.test'; password = 'password'"
                        class="p-2 rounded-lg bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-left transition border border-slate-200 dark:border-slate-700 shadow-xs">
                    <div class="font-semibold text-slate-900 dark:text-white">Patient Portal</div>
                    <div class="text-[10px] text-slate-500 dark:text-slate-400 truncate">patient.abdul@healthmate.test</div>
                </button>
                <button type="button" @click="email = 'receptionist@carepoint.test'; password = 'password'"
                        class="p-2 rounded-lg bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-left transition border border-slate-200 dark:border-slate-700 shadow-xs">
                    <div class="font-semibold text-slate-900 dark:text-white">Receptionist</div>
                    <div class="text-[10px] text-slate-500 dark:text-slate-400 truncate">receptionist@carepoint.test</div>
                </button>
                <button type="button" @click="email = 'superadmin@healthmate.test'; password = 'password'"
                        class="p-2 rounded-lg bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-left transition border border-slate-200 dark:border-slate-700 shadow-xs">
                    <div class="font-semibold text-amber-600 dark:text-amber-400">Super Admin</div>
                    <div class="text-[10px] text-slate-500 dark:text-slate-400 truncate">superadmin@healthmate.test</div>
                </button>
            </div>
        </div>
    </div>
</body>
</html>
