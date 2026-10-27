<?php

namespace App\Http\Controllers;

use App\Models\Clinic;
use App\Models\SubscriptionPlan;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SuperAdminController extends Controller
{
    public function clinics(): View
    {
        $clinics = Clinic::with(['activeSubscription.plan', 'memberships'])
            ->withCount(['patients', 'appointments'])
            ->latest()
            ->paginate(15);

        return view('superadmin.clinics', compact('clinics'));
    }

    public function toggleClinicStatus(Clinic $clinic): RedirectResponse
    {
        $newStatus = $clinic->status === 'active' ? 'suspended' : 'active';
        $clinic->update(['status' => $newStatus]);

        return back()->with('success', "Clinic {$clinic->name} status updated to {$newStatus}.");
    }

    public function plans(): View
    {
        $plans = SubscriptionPlan::withCount('subscriptions')->get();

        return view('superadmin.plans', compact('plans'));
    }
}
