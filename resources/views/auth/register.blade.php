<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register Clinic — HealthMate Multi-Clinic SaaS</title>

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
      x-data="{ themeOpen: false }">

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

    <div class="w-full max-w-xl my-8">
        <!-- Logo -->
        <div class="text-center mb-6">
            <div class="inline-flex w-12 h-12 rounded-2xl bg-gradient-to-tr from-sky-500 to-teal-400 items-center justify-center text-white shadow-xl shadow-sky-500/20 mb-3">
                <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">Create Clinic Workspace</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Start your 14-day healthcare trial — no credit card required</p>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-xl p-6 sm:p-8 border border-slate-200 dark:border-slate-800 transition-colors">
            @if($errors->any())
                <div class="mb-4 rounded-xl bg-rose-50 dark:bg-rose-950/60 border border-rose-200 dark:border-rose-800 p-3 text-xs text-rose-700 dark:text-rose-300">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('register') }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Clinic Name</label>
                        <input type="text" name="clinic_name" value="{{ old('clinic_name') }}" required placeholder="e.g. MedCare Family Hospital"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">City</label>
                        <input type="text" name="clinic_city" value="{{ old('clinic_city', 'Dhaka') }}" required
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Clinic Phone (e.g. +8801...)</label>
                        <input type="text" name="clinic_phone" value="{{ old('clinic_phone') }}" required placeholder="+8801700000000"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
                    </div>

                    <div class="sm:col-span-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                        <div class="text-xs font-bold text-slate-900 dark:text-white mb-3 uppercase tracking-wider">Owner / Administrator Account</div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Full Name</label>
                        <input type="text" name="name" value="{{ old('name') }}" required placeholder="Dr. John Doe"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Admin Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" required placeholder="admin@myclinic.com"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Password</label>
                        <div class="relative" x-data="{ showPassword: false }">
                            <input :type="showPassword ? 'text' : 'password'" name="password" required
                                   class="w-full pl-3.5 pr-10 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
                            <button type="button" @click="showPassword = !showPassword"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 focus:outline-none transition-colors"
                                    :title="showPassword ? 'Hide password' : 'Show password'"
                                    :aria-label="showPassword ? 'Hide password' : 'Show password'">
                                <svg x-show="!showPassword" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg x-show="showPassword" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Confirm Password</label>
                        <div class="relative" x-data="{ showConfirmPassword: false }">
                            <input :type="showConfirmPassword ? 'text' : 'password'" name="password_confirmation" required
                                   class="w-full pl-3.5 pr-10 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
                            <button type="button" @click="showConfirmPassword = !showConfirmPassword"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 focus:outline-none transition-colors"
                                    :title="showConfirmPassword ? 'Hide password' : 'Show password'"
                                    :aria-label="showConfirmPassword ? 'Hide password' : 'Show password'">
                                <svg x-show="!showConfirmPassword" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg x-show="showConfirmPassword" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-sky-600 hover:bg-sky-500 text-white font-semibold text-sm shadow-md shadow-sky-600/25 transition">
                        Create Clinic & Start Free Trial
                    </button>
                </div>
            </form>

            <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-800 text-center text-xs text-slate-500 dark:text-slate-400">
                Already have an account?
                <a href="{{ route('login') }}" class="font-semibold text-sky-600 dark:text-sky-400 hover:underline">Sign In</a>
            </div>
        </div>
    </div>
</body>
</html>
