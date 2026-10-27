<?php

namespace App\Http\Controllers;

use App\Models\Clinic;
use App\Models\ClinicMembership;
use App\Services\TenantContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClinicContextController extends Controller
{
    /**
     * Switch active clinic context for the authenticated user.
     */
    public function switch(Request $request, Clinic $clinic): RedirectResponse
    {
        $user = Auth::user();

        if (! $user) {
            return redirect()->route('login');
        }

        $isMember = ClinicMembership::where('clinic_id', $clinic->id)
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->exists();

        if (! $isMember && ! $user->is_super_admin) {
            abort(403, 'You are not an authorized member of this clinic.');
        }

        $request->session()->put('active_clinic_id', $clinic->id);
        TenantContext::setClinic($clinic);

        return redirect()->back()->with('success', "Switched active workspace to {$clinic->name}");
    }
}
