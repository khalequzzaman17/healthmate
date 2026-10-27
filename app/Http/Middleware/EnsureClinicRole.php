<?php

namespace App\Http\Middleware;

use App\Models\ClinicMembership;
use App\Services\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureClinicRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = Auth::user();

        if (! $user) {
            return redirect()->route('login');
        }

        if ($user->is_super_admin) {
            return $next($request);
        }

        $clinic = TenantContext::getClinic();

        if (! $clinic) {
            abort(403, 'No active clinic workspace selected.');
        }

        $membership = ClinicMembership::where('clinic_id', $clinic->id)
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->first();

        if (! $membership || ! in_array($membership->role, $roles)) {
            abort(403, 'Unauthorized. Your clinic role cannot access this area.');
        }

        return $next($request);
    }
}
