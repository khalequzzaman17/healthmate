<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-900">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login — HealthMate Multi-Clinic SaaS</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full flex items-center justify-center p-4 font-sans text-slate-800" x-data="{ email: 'admin@carepoint.test', password: 'password' }">
    <div class="w-full max-w-md">
        <!-- Logo -->
        <div class="text-center mb-6">
            <div class="inline-flex w-12 h-12 rounded-2xl bg-gradient-to-tr from-sky-500 to-teal-400 items-center justify-center text-white shadow-xl shadow-sky-500/20 mb-3">
                <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-white tracking-tight">HealthMate</h1>
            <p class="text-xs text-slate-400 mt-1">Multi-Clinic Healthcare Management SaaS</p>
        </div>

        <!-- Login Card -->
        <div class="bg-white rounded-2xl shadow-2xl p-6 sm:p-8 border border-slate-100">
            <h2 class="text-lg font-bold text-slate-900 mb-1">Sign in to your account</h2>
            <p class="text-xs text-slate-500 mb-6">Access your clinic workspace, patient care desks, or portal</p>

            @if($errors->any())
                <div class="mb-4 rounded-xl bg-rose-50 border border-rose-200 p-3 text-xs text-rose-700">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Email Address</label>
                    <input type="email" name="email" x-model="email" required autofocus
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent transition">
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-xs font-semibold text-slate-700">Password</label>
                    </div>
                    <input type="password" name="password" x-model="password" required
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent transition">
                </div>

                <div class="flex items-center justify-between text-xs">
                    <label class="flex items-center gap-2 cursor-pointer text-slate-600">
                        <input type="checkbox" name="remember" class="rounded border-slate-300 text-sky-600 focus:ring-sky-500">
                        <span>Remember this device</span>
                    </label>
                </div>

                <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-sky-600 hover:bg-sky-500 text-white font-semibold text-sm shadow-md shadow-sky-600/25 transition">
                    Sign In
                </button>
            </form>

            <div class="mt-6 pt-6 border-t border-slate-100 text-center text-xs text-slate-500">
                Want to register a new clinic?
                <a href="{{ route('register') }}" class="font-semibold text-sky-600 hover:text-sky-500">Create Workspace</a>
            </div>
        </div>

        <!-- Quick Demo Switcher -->
        <div class="mt-6 bg-slate-800/80 rounded-2xl p-4 border border-slate-700/60 text-slate-300">
            <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2 flex items-center justify-between">
                <span>Demo Quick Login Accounts</span>
                <span class="text-[10px] text-teal-400">Password: password</span>
            </div>
            <div class="grid grid-cols-2 gap-2 text-xs">
                <button type="button" @click="email = 'admin@carepoint.test'; password = 'password'"
                        class="p-2 rounded-lg bg-slate-700/70 hover:bg-slate-700 text-left transition border border-slate-600/50">
                    <div class="font-semibold text-white">Clinic Admin</div>
                    <div class="text-[10px] text-slate-400 truncate">admin@carepoint.test</div>
                </button>
                <button type="button" @click="email = 'dr.tariq@carepoint.test'; password = 'password'"
                        class="p-2 rounded-lg bg-slate-700/70 hover:bg-slate-700 text-left transition border border-slate-600/50">
                    <div class="font-semibold text-white">Doctor (Internal Med)</div>
                    <div class="text-[10px] text-slate-400 truncate">dr.tariq@carepoint.test</div>
                </button>
                <button type="button" @click="email = 'nurse.poly@carepoint.test'; password = 'password'"
                        class="p-2 rounded-lg bg-slate-700/70 hover:bg-slate-700 text-left transition border border-slate-600/50">
                    <div class="font-semibold text-white">Nurse Poly</div>
                    <div class="text-[10px] text-slate-400 truncate">nurse.poly@carepoint.test</div>
                </button>
                <button type="button" @click="email = 'patient.abdul@healthmate.test'; password = 'password'"
                        class="p-2 rounded-lg bg-slate-700/70 hover:bg-slate-700 text-left transition border border-slate-600/50">
                    <div class="font-semibold text-white">Patient Portal</div>
                    <div class="text-[10px] text-slate-400 truncate">patient.abdul@healthmate.test</div>
                </button>
                <button type="button" @click="email = 'receptionist@carepoint.test'; password = 'password'"
                        class="p-2 rounded-lg bg-slate-700/70 hover:bg-slate-700 text-left transition border border-slate-600/50">
                    <div class="font-semibold text-white">Receptionist</div>
                    <div class="text-[10px] text-slate-400 truncate">receptionist@carepoint.test</div>
                </button>
                <button type="button" @click="email = 'superadmin@healthmate.test'; password = 'password'"
                        class="p-2 rounded-lg bg-slate-700/70 hover:bg-slate-700 text-left transition border border-slate-600/50">
                    <div class="font-semibold text-amber-400">Super Admin</div>
                    <div class="text-[10px] text-slate-400 truncate">superadmin@healthmate.test</div>
                </button>
            </div>
        </div>
    </div>
</body>
</html>
