<?php

namespace App\Http\Middleware;

use App\Models\Clinic;
use App\Models\ClinicMembership;
use App\Services\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class IdentifyTenant
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (! $user) {
            TenantContext::clear();

            return $next($request);
        }

        $activeClinicId = $request->session()->get('active_clinic_id');
        $clinic = null;

        if ($activeClinicId) {
            $clinic = Clinic::find($activeClinicId);
        }

        // If no clinic in session or not found, pick user's first active membership
        if (! $clinic) {
            $membership = ClinicMembership::where('user_id', $user->id)
                ->where('status', 'active')
                ->first();

            if ($membership) {
                $clinic = $membership->clinic;
            } elseif ($user->is_super_admin) {
                $clinic = Clinic::first();
            }
        }

        // Validate membership if clinic found
        if ($clinic) {
            $isMember = ClinicMembership::where('clinic_id', $clinic->id)
                ->where('user_id', $user->id)
                ->where('status', 'active')
                ->exists();

            if (! $isMember && ! $user->is_super_admin) {
                // Not authorized for this clinic, find one they ARE authorized for
                $membership = ClinicMembership::where('user_id', $user->id)
                    ->where('status', 'active')
                    ->first();

                if ($membership) {
                    $clinic = $membership->clinic;
                } else {
                    $clinic = null;
                }
            }
        }

        if ($clinic) {
            $request->session()->put('active_clinic_id', $clinic->id);
            TenantContext::setClinic($clinic);

            $currentMembership = ClinicMembership::where('clinic_id', $clinic->id)
                ->where('user_id', $user->id)
                ->first();

            View::share('activeClinic', $clinic);
            View::share('currentMembership', $currentMembership);
            View::share('userRole', $currentMembership?->role ?? ($user->is_super_admin ? 'super_admin' : 'guest'));
            View::share('userClinics', $user->clinics()->wherePivot('status', 'active')->get());
        } else {
            TenantContext::clear();
            View::share('activeClinic', null);
            View::share('currentMembership', null);
            View::share('userRole', $user->is_super_admin ? 'super_admin' : 'guest');
            View::share('userClinics', collect());
        }

        return $next($request);
    }
}
