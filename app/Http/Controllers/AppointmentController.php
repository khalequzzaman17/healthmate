<?php

namespace App\Http\Controllers;

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Service;
use App\Models\User;
use App\Services\AppointmentService;
use App\Services\TenantContext;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AppointmentController extends Controller
{
    public function __construct(
        protected AppointmentService $appointmentService
    ) {}

    public function index(Request $request): View
    {
        $status = $request->input('status');
        $date = $request->input('date', Carbon::today()->toDateString());
        $doctorId = $request->input('doctor_id');

        $appointments = Appointment::query()
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($date, fn ($q) => $q->whereDate('scheduled_at', $date))
            ->when($doctorId, fn ($q) => $q->where('doctor_id', $doctorId))
            ->with(['patient', 'doctor', 'service'])
            ->orderBy('scheduled_at')
            ->paginate(20)
            ->withQueryString();

        $clinic = TenantContext::getClinic();
        $doctors = User::whereHas('clinicMemberships', function ($q) use ($clinic) {
            $q->where('clinic_id', $clinic->id)->where('role', 'doctor')->where('status', 'active');
        })->get();

        return view('appointments.index', compact('appointments', 'status', 'date', 'doctorId', 'doctors'));
    }

    public function create(Request $request): View
    {
        $clinic = TenantContext::getClinic();

        $patients = Patient::orderBy('first_name')->get();
        $doctors = User::whereHas('clinicMemberships', function ($q) use ($clinic) {
            $q->where('clinic_id', $clinic->id)->where('role', 'doctor')->where('status', 'active');
        })->get();
        $services = Service::where('is_active', true)->get();

        $preselectedPatientId = $request->input('patient_id');

        return view('appointments.create', compact('patients', 'doctors', 'services', 'preselectedPatientId'));
    }

    public function store(Request $request): RedirectResponse
    {
        $clinic = TenantContext::getClinic();

        $validated = $request->validate([
            'patient_id' => ['required', 'exists:patients,id'],
            'doctor_id' => ['required', 'exists:users,id'],
            'service_id' => ['nullable', 'exists:services,id'],
            'scheduled_date' => ['required', 'date', 'after_or_equal:today'],
            'scheduled_time' => ['required'],
            'duration_minutes' => ['required', 'integer', 'min:10', 'max:120'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $scheduledAt = Carbon::parse("{$validated['scheduled_date']} {$validated['scheduled_time']}");
        $patient = Patient::findOrFail($validated['patient_id']);
        $doctor = User::findOrFail($validated['doctor_id']);
        $service = ! empty($validated['service_id']) ? Service::find($validated['service_id']) : null;

        $appointment = $this->appointmentService->bookAppointment(
            clinic: $clinic,
            patient: $patient,
            doctor: $doctor,
            scheduledAt: $scheduledAt,
            service: $service,
            durationMinutes: (int) $validated['duration_minutes'],
            bookingSource: 'staff',
            notes: $validated['notes'] ?? null
        );

        return redirect()->route('appointments.index', ['date' => $validated['scheduled_date']])
            ->with('success', "Appointment scheduled successfully for {$patient->fullName} with Dr. {$doctor->name}.");
    }

    public function checkIn(Appointment $appointment): RedirectResponse
    {
        $this->appointmentService->transitionStatus(
            appointment: $appointment,
            targetStatus: AppointmentStatus::CheckedIn,
            user: auth()->user(),
            reason: 'Patient checked in at reception'
        );

        return back()->with('success', "Patient {$appointment->patient->fullName} marked as Checked In.");
    }

    public function cancel(Request $request, Appointment $appointment): RedirectResponse
    {
        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:255'],
        ]);

        $this->appointmentService->transitionStatus(
            appointment: $appointment,
            targetStatus: AppointmentStatus::Canceled,
            user: auth()->user(),
            reason: $validated['reason']
        );

        return back()->with('success', 'Appointment has been canceled.');
    }
}
