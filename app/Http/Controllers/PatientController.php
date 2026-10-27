<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\PatientAllergy;
use App\Models\PatientAssignment;
use App\Models\PatientCondition;
use App\Models\User;
use App\Services\AuditService;
use App\Services\TenantContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PatientController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->input('search');

        $patients = Patient::query()
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('patient_number', 'like', "%{$search}%");
                });
            })
            ->with(['activeAssignment.nurse'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('patients.index', compact('patients', 'search'));
    }

    public function create(): View
    {
        $clinic = TenantContext::getClinic();

        // Get nurses in this clinic for assignment option
        $nurses = User::whereHas('clinicMemberships', function ($q) use ($clinic) {
            $q->where('clinic_id', $clinic->id)->where('role', 'nurse')->where('status', 'active');
        })->get();

        return view('patients.create', compact('nurses'));
    }

    public function store(Request $request): RedirectResponse
    {
        $clinic = TenantContext::getClinic();

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['nullable', 'string', 'max:100'],
            'date_of_birth' => ['nullable', 'date'],
            'gender' => ['required', 'in:male,female,other'],
            'blood_group' => ['nullable', 'string', 'max:10'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
            'emergency_contact_name' => ['nullable', 'string', 'max:100'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:20'],
            'emergency_contact_relation' => ['nullable', 'string', 'max:50'],
            'medical_history_summary' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'allergies' => ['nullable', 'string'],
            'chronic_conditions' => ['nullable', 'string'],
            'assign_nurse_id' => ['nullable', 'exists:users,id'],
        ]);

        return DB::transaction(function () use ($clinic, $validated) {
            // Generate clinic patient number
            $patientCount = Patient::where('clinic_id', $clinic->id)->count() + 1;
            $patientNumber = 'HM-'.date('Y').'-'.str_pad((string) $patientCount, 4, '0', STR_PAD_LEFT);

            $patient = Patient::create([
                'clinic_id' => $clinic->id,
                'patient_number' => $patientNumber,
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'] ?? null,
                'date_of_birth' => $validated['date_of_birth'] ?? null,
                'gender' => $validated['gender'],
                'blood_group' => $validated['blood_group'] ?? null,
                'phone' => $validated['phone'],
                'email' => $validated['email'] ?? null,
                'address' => $validated['address'] ?? null,
                'emergency_contact_name' => $validated['emergency_contact_name'] ?? null,
                'emergency_contact_phone' => $validated['emergency_contact_phone'] ?? null,
                'emergency_contact_relation' => $validated['emergency_contact_relation'] ?? null,
                'medical_history_summary' => $validated['medical_history_summary'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]);

            // Save allergies
            if (! empty($validated['allergies'])) {
                foreach (array_filter(array_map('trim', explode(',', $validated['allergies']))) as $allergen) {
                    PatientAllergy::create([
                        'clinic_id' => $clinic->id,
                        'patient_id' => $patient->id,
                        'allergen' => $allergen,
                        'severity' => 'mild',
                    ]);
                }
            }

            // Save conditions
            if (! empty($validated['chronic_conditions'])) {
                foreach (array_filter(array_map('trim', explode(',', $validated['chronic_conditions']))) as $cond) {
                    PatientCondition::create([
                        'clinic_id' => $clinic->id,
                        'patient_id' => $patient->id,
                        'condition_name' => $cond,
                        'status' => 'active',
                    ]);
                }
            }

            // Assign Nurse if chosen
            if (! empty($validated['assign_nurse_id'])) {
                PatientAssignment::create([
                    'clinic_id' => $clinic->id,
                    'patient_id' => $patient->id,
                    'nurse_id' => $validated['assign_nurse_id'],
                    'assigned_by_user_id' => auth()->id(),
                    'status' => 'active',
                ]);
            }

            AuditService::log('patient.registered', $patient, null, [
                'patient_number' => $patientNumber,
                'name' => $patient->fullName,
            ], $clinic->id);

            return redirect()->route('patients.show', $patient)->with('success', "Patient {$patient->fullName} ({$patientNumber}) registered successfully.");
        });
    }

    public function show(Patient $patient): View
    {
        if (TenantContext::hasClinic() && $patient->clinic_id !== TenantContext::getClinicId()) {
            abort(404);
        }

        $patient->load([
            'allergies',
            'conditions',
            'activeAssignment.nurse',
            'appointments.doctor',
            'appointments.service',
            'calls.nurse',
            'healthObservations.recordedBy',
            'healthReports.nurse',
            'healthReports.reviewerDoctor',
            'consultations.doctor',
            'consultations.diagnoses',
            'consultations.prescription.items',
            'invoices.payments',
        ]);

        AuditService::logAccess($patient->id, 'PatientProfile', $patient->id, 'viewed', $patient->clinic_id);

        return view('patients.show', compact('patient'));
    }
}
