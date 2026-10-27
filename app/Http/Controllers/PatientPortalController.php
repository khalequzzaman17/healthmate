<?php

namespace App\Http\Controllers;

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\Service;
use App\Models\User;
use App\Services\AppointmentService;
use App\Services\TenantContext;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PatientPortalController extends Controller
{
    public function __construct(
        protected AppointmentService $appointmentService
    ) {}

    protected function getPatient(): Patient
    {
        $user = Auth::user();
        $patient = $user->patientProfile ?? Patient::where('user_id', $user->id)->first();

        if (! $patient) {
            abort(403, 'No patient profile linked with your account.');
        }

        return $patient;
    }

    public function dashboard(): View
    {
        $patient = $this->getPatient();

        $upcomingAppointments = Appointment::where('patient_id', $patient->id)
            ->where('scheduled_at', '>=', now())
            ->whereIn('status', [AppointmentStatus::Scheduled->value, AppointmentStatus::CheckedIn->value])
            ->with(['doctor', 'service'])
            ->orderBy('scheduled_at')
            ->get();

        $prescriptions = Prescription::where('patient_id', $patient->id)
            ->with(['doctor', 'items'])
            ->latest('issued_at')
            ->take(5)
            ->get();

        $invoices = Invoice::where('patient_id', $patient->id)
            ->latest()
            ->take(5)
            ->get();

        return view('portal.dashboard', compact('patient', 'upcomingAppointments', 'prescriptions', 'invoices'));
    }

    public function book(): View
    {
        $clinic = TenantContext::getClinic();
        $patient = $this->getPatient();

        $doctors = User::whereHas('clinicMemberships', function ($q) use ($clinic) {
            $q->where('clinic_id', $clinic->id)->where('role', 'doctor')->where('status', 'active');
        })->get();

        $services = Service::where('is_active', true)->get();

        return view('portal.book', compact('patient', 'doctors', 'services'));
    }

    public function storeBooking(Request $request): RedirectResponse
    {
        $clinic = TenantContext::getClinic();
        $patient = $this->getPatient();

        $validated = $request->validate([
            'doctor_id' => ['required', 'exists:users,id'],
            'service_id' => ['nullable', 'exists:services,id'],
            'scheduled_date' => ['required', 'date', 'after_or_equal:today'],
            'scheduled_time' => ['required'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $scheduledAt = Carbon::parse("{$validated['scheduled_date']} {$validated['scheduled_time']}");
        $doctor = User::findOrFail($validated['doctor_id']);
        $service = ! empty($validated['service_id']) ? Service::find($validated['service_id']) : null;

        $this->appointmentService->bookAppointment(
            clinic: $clinic,
            patient: $patient,
            doctor: $doctor,
            scheduledAt: $scheduledAt,
            service: $service,
            durationMinutes: 20,
            bookingSource: 'patient_portal',
            notes: $validated['notes'] ?? null
        );

        return redirect()->route('portal.dashboard')
            ->with('success', "Your appointment with Dr. {$doctor->name} on {$scheduledAt->format('M d, Y h:i A')} has been confirmed.");
    }

    public function records(): View
    {
        $patient = $this->getPatient();
        $patient->load([
            'conditions',
            'allergies',
            'consultations.doctor',
            'consultations.diagnoses',
            'consultations.prescription.items',
            'healthObservations',
        ]);

        return view('portal.records', compact('patient'));
    }
}
