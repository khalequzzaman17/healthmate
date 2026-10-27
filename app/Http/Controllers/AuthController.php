<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\TenantService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            // Set initial active clinic
            $user = Auth::user();
            $membership = $user->clinicMemberships()->where('status', 'active')->first();
            if ($membership) {
                $request->session()->put('active_clinic_id', $membership->clinic_id);
            }

            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function register(Request $request, TenantService $tenantService): RedirectResponse
    {
        $validated = $request->validate([
            'clinic_name' => ['required', 'string', 'max:255'],
            'clinic_city' => ['required', 'string', 'max:100'],
            'clinic_phone' => ['required', 'string', 'max:20'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['clinic_phone'],
            'password' => Hash::make($validated['password']),
            'status' => 'active',
        ]);

        $clinic = $tenantService->createClinic([
            'name' => $validated['clinic_name'],
            'city' => $validated['clinic_city'],
            'phone' => $validated['clinic_phone'],
            'email' => $validated['email'],
        ], $user);

        Auth::login($user);
        $request->session()->put('active_clinic_id', $clinic->id);

        return redirect()->route('dashboard')->with('success', "Welcome to HealthMate! Your clinic workspace {$clinic->name} is active.");
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
