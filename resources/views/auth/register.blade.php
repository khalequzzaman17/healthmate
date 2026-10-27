<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-900">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register Clinic — HealthMate Multi-Clinic SaaS</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full flex items-center justify-center p-4 font-sans text-slate-800">
    <div class="w-full max-w-xl">
        <!-- Logo -->
        <div class="text-center mb-6">
            <div class="inline-flex w-12 h-12 rounded-2xl bg-gradient-to-tr from-sky-500 to-teal-400 items-center justify-center text-white shadow-xl shadow-sky-500/20 mb-3">
                <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-white tracking-tight">Create Clinic Workspace</h1>
            <p class="text-xs text-slate-400 mt-1">Start your 14-day healthcare trial — no credit card required</p>
        </div>

        <div class="bg-white rounded-2xl shadow-2xl p-6 sm:p-8 border border-slate-100">
            @if($errors->any())
                <div class="mb-4 rounded-xl bg-rose-50 border border-rose-200 p-3 text-xs text-rose-700">
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
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Clinic Name</label>
                        <input type="text" name="clinic_name" value="{{ old('clinic_name') }}" required placeholder="e.g. MedCare Family Hospital"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">City</label>
                        <input type="text" name="clinic_city" value="{{ old('clinic_city', 'Dhaka') }}" required
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Clinic Phone (e.g. +8801...)</label>
                        <input type="text" name="clinic_phone" value="{{ old('clinic_phone') }}" required placeholder="+8801700000000"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
                    </div>

                    <div class="sm:col-span-2 pt-2 border-t border-slate-100">
                        <div class="text-xs font-bold text-slate-900 mb-3 uppercase tracking-wider">Owner / Administrator Account</div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Full Name</label>
                        <input type="text" name="name" value="{{ old('name') }}" required placeholder="Dr. John Doe"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Admin Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" required placeholder="admin@myclinic.com"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Password</label>
                        <input type="password" name="password" required
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Confirm Password</label>
                        <input type="password" name="password_confirmation" required
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-sky-600 hover:bg-sky-500 text-white font-semibold text-sm shadow-md shadow-sky-600/25 transition">
                        Create Clinic & Start Free Trial
                    </button>
                </div>
            </form>

            <div class="mt-4 pt-4 border-t border-slate-100 text-center text-xs text-slate-500">
                Already have an account?
                <a href="{{ route('login') }}" class="font-semibold text-sky-600 hover:text-sky-500">Sign In</a>
            </div>
        </div>
    </div>
</body>
</html>
