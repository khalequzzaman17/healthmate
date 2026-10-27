<?php

namespace App\Http\Controllers;

use App\Enums\AppointmentStatus;
use App\Enums\CommissionStatus;
use App\Enums\FollowUpStatus;
use App\Enums\HealthReportStatus;
use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\CommissionLedgerEntry;
use App\Models\Consultation;
use App\Models\FollowUpPlan;
use App\Models\HealthReport;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\PatientAssignment;
use App\Models\Prescription;
use App\Models\Subscription;
use App\Services\TenantContext;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();
        $clinic = TenantContext::getClinic();

        if ($user->is_super_admin && ! $clinic) {
            $totalClinics = Clinic::count();
            $activeSubscriptions = Subscription::where('status', 'active')->count();
            $recentClinics = Clinic::with('activeSubscription.plan')->latest()->take(5)->get();

            return view('dashboard.super_admin', compact('totalClinics', 'activeSubscriptions', 'recentClinics'));
        }

        if (! $clinic) {
            return view('dashboard.no_clinic');
        }

        $membership = $user->getMembershipForClinic($clinic);
        $role = $membership?->role ?? ($user->is_super_admin ? 'admin' : 'patient');

        $today = Carbon::today();

        switch ($role) {
            case 'doctor':
                $todayAppointments = Appointment::where('doctor_id', $user->id)
                    ->whereDate('scheduled_at', $today)
                    ->with(['patient', 'service'])
                    ->orderBy('scheduled_at')
                    ->get();

                $reportsAwaitingReview = HealthReport::where('status', HealthReportStatus::Submitted->value)
                    ->with(['patient', 'nurse'])
                    ->latest()
                    ->take(5)
                    ->get();

                $completedConsultationsCount = Consultation::where('doctor_id', $user->id)->count();

                $upcomingConsultationsCount = Appointment::where('doctor_id', $user->id)
                    ->where('scheduled_at', '>=', now())
                    ->where('scheduled_at', '<=', now()->addDays(7))
                    ->count();

                $commissionTotal = CommissionLedgerEntry::where('staff_user_id', $user->id)
                    ->whereIn('status', [CommissionStatus::Approved->value, CommissionStatus::Payable->value, CommissionStatus::Paid->value])
                    ->sum('amount');

                $recentPatients = Patient::latest()->take(5)->get();
                $featuredPatient = $todayAppointments->first()?->patient ?? Patient::first();

                return view('dashboard.doctor', compact(
                    'todayAppointments',
                    'reportsAwaitingReview',
                    'completedConsultationsCount',
                    'upcomingConsultationsCount',
                    'commissionTotal',
                    'recentPatients',
                    'featuredPatient'
                ));

            case 'nurse':
                $assignedPatientsCount = PatientAssignment::where('nurse_id', $user->id)
                    ->where('status', 'active')
                    ->count();

                $callsDueToday = FollowUpPlan::where('assigned_nurse_id', $user->id)
                    ->whereDate('due_date', '<=', $today)
                    ->where('status', FollowUpStatus::Scheduled->value)
                    ->with('patient')
                    ->orderBy('priority', 'desc')
                    ->get();

                $overdueFollowUpsCount = FollowUpPlan::where('assigned_nurse_id', $user->id)
                    ->whereDate('due_date', '<', $today)
                    ->where('status', FollowUpStatus::Scheduled->value)
                    ->count();

                $upcomingFollowUps = FollowUpPlan::where('assigned_nurse_id', $user->id)
                    ->whereDate('due_date', '>', $today)
                    ->where('status', FollowUpStatus::Scheduled->value)
                    ->with('patient')
                    ->take(10)
                    ->get();

                $submittedReports = HealthReport::where('nurse_id', $user->id)
                    ->with(['patient', 'reviewerDoctor'])
                    ->latest()
                    ->take(5)
                    ->get();

                $pendingReportsCount = HealthReport::where('nurse_id', $user->id)
                    ->where('status', HealthReportStatus::Submitted->value)
                    ->count();

                $nurseCommissionTotal = CommissionLedgerEntry::where('staff_user_id', $user->id)
                    ->whereIn('status', [CommissionStatus::Approved->value, CommissionStatus::Payable->value, CommissionStatus::Paid->value])
                    ->sum('amount');

                return view('dashboard.nurse', compact(
                    'assignedPatientsCount',
                    'callsDueToday',
                    'overdueFollowUpsCount',
                    'upcomingFollowUps',
                    'submittedReports',
                    'pendingReportsCount',
                    'nurseCommissionTotal'
                ));

            case 'receptionist':
                $todayAppointments = Appointment::whereDate('scheduled_at', $today)
                    ->with(['patient', 'doctor', 'service'])
                    ->orderBy('scheduled_at')
                    ->get();

                $recentInvoices = Invoice::with('patient')->latest()->take(5)->get();

                return view('dashboard.receptionist', compact('todayAppointments', 'recentInvoices'));

            case 'patient':
                $patient = $user->patientProfile ?? Patient::where('user_id', $user->id)->first();

                $upcomingAppointments = $patient ? Appointment::where('patient_id', $patient->id)
                    ->where('scheduled_at', '>=', now())
                    ->whereIn('status', [AppointmentStatus::Scheduled->value, AppointmentStatus::CheckedIn->value])
                    ->with('doctor', 'service')
                    ->orderBy('scheduled_at')
                    ->get() : collect();

                $prescriptions = $patient ? Prescription::where('patient_id', $patient->id)
                    ->with(['doctor', 'items'])
                    ->latest()
                    ->take(5)
                    ->get() : collect();

                $invoices = $patient ? Invoice::where('patient_id', $patient->id)
                    ->latest()
                    ->take(5)
                    ->get() : collect();

                return view('dashboard.patient', compact('patient', 'upcomingAppointments', 'prescriptions', 'invoices'));

            case 'admin':
            default:
                $totalPatients = Patient::count();
                $todayAppointmentsCount = Appointment::whereDate('scheduled_at', $today)->count();
                $completedConsultationsCount = Consultation::count();
                if ($completedConsultationsCount === 0) {
                    $completedConsultationsCount = Appointment::where('status', AppointmentStatus::Completed->value)->count();
                }
                $pendingNurseFollowUpsCount = FollowUpPlan::where('status', FollowUpStatus::Scheduled->value)->count();
                $overdueFollowUpsCount = FollowUpPlan::whereDate('due_date', '<', $today)
                    ->where('status', FollowUpStatus::Scheduled->value)
                    ->count();

                $pendingReportsCount = HealthReport::where('status', HealthReportStatus::Submitted->value)->count();
                $pendingCommissionsCount = CommissionLedgerEntry::where('status', CommissionStatus::Pending->value)->count();
                $totalRevenue = Invoice::sum('paid_amount');
                $dueRevenue = Invoice::where('status', '!=', 'paid')->sum('total_amount') - Invoice::where('status', '!=', 'paid')->sum('paid_amount');

                $recentAppointments = Appointment::with(['patient', 'doctor', 'service'])
                    ->latest('scheduled_at')
                    ->take(5)
                    ->get();

                $recentReports = HealthReport::with(['patient', 'nurse'])
                    ->latest()
                    ->take(5)
                    ->get();

                $patientFlow = [
                    'completed' => Appointment::whereDate('scheduled_at', $today)->where('status', AppointmentStatus::Completed->value)->count(),
                    'in_progress' => Appointment::whereDate('scheduled_at', $today)->where('status', AppointmentStatus::CheckedIn->value)->count(),
                    'waiting' => Appointment::whereDate('scheduled_at', $today)->where('status', AppointmentStatus::Scheduled->value)->count(),
                    'cancelled' => Appointment::whereDate('scheduled_at', $today)->where('status', AppointmentStatus::Cancelled->value)->count(),
                ];

                return view('dashboard.admin', compact(
                    'totalPatients',
                    'todayAppointmentsCount',
                    'completedConsultationsCount',
                    'pendingNurseFollowUpsCount',
                    'overdueFollowUpsCount',
                    'pendingReportsCount',
                    'pendingCommissionsCount',
                    'totalRevenue',
                    'dueRevenue',
                    'recentAppointments',
                    'recentReports',
                    'patientFlow'
                ));
        }
    }
}
