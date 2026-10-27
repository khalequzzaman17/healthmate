<?php

namespace Database\Seeders;

use App\Enums\AppointmentStatus;
use App\Enums\CallOutcome;
use App\Enums\ClinicRole;
use App\Enums\CommissionStatus;
use App\Enums\FollowUpStatus;
use App\Enums\HealthReportStatus;
use App\Enums\InvoiceStatus;
use App\Enums\SubscriptionStatus;
use App\Models\Appointment;
use App\Models\AppointmentStatusHistory;
use App\Models\Clinic;
use App\Models\ClinicLocation;
use App\Models\ClinicMembership;
use App\Models\CommissionLedgerEntry;
use App\Models\CommissionRule;
use App\Models\DoctorSchedule;
use App\Models\FollowUpPlan;
use App\Models\HealthObservation;
use App\Models\HealthReport;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Patient;
use App\Models\PatientAllergy;
use App\Models\PatientAssignment;
use App\Models\PatientCall;
use App\Models\PatientCondition;
use App\Models\Payment;
use App\Models\Service;
use App\Models\StaffProfile;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with complete multi-clinic demo data.
     */
    public function run(): void
    {
        // 1. Super Admin
        $superAdmin = User::create([
            'name' => 'Platform Super Admin',
            'email' => 'superadmin@healthmate.test',
            'phone' => '+8801700000000',
            'password' => Hash::make('password'),
            'is_super_admin' => true,
            'status' => 'active',
        ]);

        // 2. Subscription Plans
        $planBasic = SubscriptionPlan::create([
            'name' => 'Essential Clinic',
            'slug' => 'essential-clinic',
            'price' => 2500.00,
            'interval' => 'monthly',
            'max_doctors' => 3,
            'max_patients' => 300,
            'features' => ['appointments', 'patient_records', 'nurse_calls', 'billing'],
            'is_active' => true,
        ]);

        $planPro = SubscriptionPlan::create([
            'name' => 'Professional Health SaaS',
            'slug' => 'professional-health-saas',
            'price' => 5000.00,
            'interval' => 'monthly',
            'max_doctors' => 10,
            'max_patients' => 2000,
            'features' => ['appointments', 'patient_records', 'nurse_calls', 'doctor_portal', 'commissions', 'billing', 'analytics'],
            'is_active' => true,
        ]);

        // 3. Clinic 1: CarePoint Multi-Specialty Clinic (Dhaka)
        $clinic1 = Clinic::create([
            'name' => 'CarePoint Multi-Specialty Clinic',
            'slug' => 'carepoint',
            'email' => 'contact@carepoint.test',
            'phone' => '+8801711111111',
            'address' => 'House 12, Road 4, Dhanmondi',
            'city' => 'Dhaka',
            'timezone' => 'Asia/Dhaka',
            'currency' => 'BDT',
            'status' => 'active',
        ]);

        Subscription::create([
            'clinic_id' => $clinic1->id,
            'subscription_plan_id' => $planPro->id,
            'status' => SubscriptionStatus::Active->value,
            'current_period_starts_at' => now()->startOfMonth(),
            'current_period_ends_at' => now()->addMonth(),
        ]);

        ClinicLocation::create([
            'clinic_id' => $clinic1->id,
            'name' => 'Dhanmondi Main Wing',
            'address' => 'House 12, Road 4, Dhanmondi, Dhaka',
            'phone' => '+8801711111111',
            'is_active' => true,
        ]);

        // Clinic 1 Users
        $admin1 = User::create([
            'name' => 'Dr. Kazi Mahfuz (Clinic Admin)',
            'email' => 'admin@carepoint.test',
            'phone' => '+8801712222222',
            'password' => Hash::make('password'),
            'status' => 'active',
        ]);
        ClinicMembership::create([
            'clinic_id' => $clinic1->id,
            'user_id' => $admin1->id,
            'role' => ClinicRole::Admin->value,
            'status' => 'active',
        ]);

        $doc1 = User::create([
            'name' => 'Dr. Tariqul Islam',
            'email' => 'dr.tariq@carepoint.test',
            'phone' => '+8801713333333',
            'password' => Hash::make('password'),
            'status' => 'active',
        ]);
        ClinicMembership::create([
            'clinic_id' => $clinic1->id,
            'user_id' => $doc1->id,
            'role' => ClinicRole::Doctor->value,
            'status' => 'active',
        ]);
        StaffProfile::create([
            'clinic_id' => $clinic1->id,
            'user_id' => $doc1->id,
            'title' => 'Associate Professor',
            'specialization' => 'Internal Medicine & Diabetology',
            'license_number' => 'BMDC-A-54321',
            'bio' => 'Senior consultant with 15+ years experience in metabolic syndrome and chronic disease management.',
            'commission_rate' => 30.00,
        ]);

        $nurse1 = User::create([
            'name' => 'Nurse Poly Akter',
            'email' => 'nurse.poly@carepoint.test',
            'phone' => '+8801714444444',
            'password' => Hash::make('password'),
            'status' => 'active',
        ]);
        ClinicMembership::create([
            'clinic_id' => $clinic1->id,
            'user_id' => $nurse1->id,
            'role' => ClinicRole::Nurse->value,
            'status' => 'active',
        ]);
        StaffProfile::create([
            'clinic_id' => $clinic1->id,
            'user_id' => $nurse1->id,
            'title' => 'Senior Staff Nurse',
            'specialization' => 'Cardio-Metabolic Care & Patient Follow-up',
            'license_number' => 'BNMC-N-88712',
            'commission_rate' => 50.00,
        ]);

        $receptionist1 = User::create([
            'name' => 'Sadia Jahan (Front Desk)',
            'email' => 'receptionist@carepoint.test',
            'phone' => '+8801715555555',
            'password' => Hash::make('password'),
            'status' => 'active',
        ]);
        ClinicMembership::create([
            'clinic_id' => $clinic1->id,
            'user_id' => $receptionist1->id,
            'role' => ClinicRole::Receptionist->value,
            'status' => 'active',
        ]);

        // Doctor Schedules for Dr. Tariq
        for ($day = 0; $day <= 6; $day++) {
            if ($day === 5) {
                continue;
            } // Friday off
            DoctorSchedule::create([
                'clinic_id' => $clinic1->id,
                'doctor_id' => $doc1->id,
                'day_of_week' => $day,
                'start_time' => '10:00:00',
                'end_time' => '18:00:00',
                'slot_duration_minutes' => 20,
                'is_active' => true,
            ]);
        }

        // Clinic 1 Services
        $srvGp = Service::create([
            'clinic_id' => $clinic1->id,
            'name' => 'General Medicine Consultation',
            'code' => 'GP-01',
            'description' => 'Comprehensive evaluation, vitals review, and diagnostic orders',
            'price' => 800.00,
            'duration_minutes' => 20,
            'is_active' => true,
        ]);

        $srvSpecialist = Service::create([
            'clinic_id' => $clinic1->id,
            'name' => 'Diabetology & Endocrinology Care',
            'code' => 'SPEC-DIAB',
            'description' => 'Specialized diabetes management and titration consultation',
            'price' => 1200.00,
            'duration_minutes' => 30,
            'is_active' => true,
        ]);

        // Clinic 1 Commission Rules
        CommissionRule::create([
            'clinic_id' => $clinic1->id,
            'staff_role' => 'nurse',
            'activity_type' => 'nurse_follow_up',
            'calculation_type' => 'fixed',
            'amount' => 50.00,
            'is_approval_required' => true,
            'is_active' => true,
        ]);

        CommissionRule::create([
            'clinic_id' => $clinic1->id,
            'staff_role' => 'doctor',
            'activity_type' => 'doctor_consultation',
            'calculation_type' => 'fixed',
            'amount' => 250.00,
            'is_approval_required' => true,
            'is_active' => true,
        ]);

        // Clinic 1 Patients
        $patientUser1 = User::create([
            'name' => 'Abdul Karim',
            'email' => 'patient.abdul@healthmate.test',
            'phone' => '+8801716666661',
            'password' => Hash::make('password'),
            'status' => 'active',
        ]);
        ClinicMembership::create([
            'clinic_id' => $clinic1->id,
            'user_id' => $patientUser1->id,
            'role' => 'patient',
            'status' => 'active',
        ]);

        $patient1 = Patient::create([
            'clinic_id' => $clinic1->id,
            'user_id' => $patientUser1->id,
            'patient_number' => 'HM-2026-0001',
            'first_name' => 'Abdul',
            'last_name' => 'Karim',
            'date_of_birth' => '1972-04-15',
            'gender' => 'male',
            'blood_group' => 'B+',
            'phone' => '+8801716666661',
            'email' => 'patient.abdul@healthmate.test',
            'address' => 'Flat 4B, Green Road, Dhanmondi, Dhaka',
            'emergency_contact_name' => 'Karimunnesa Begum (Spouse)',
            'emergency_contact_phone' => '+8801716666662',
            'emergency_contact_relation' => 'Spouse',
            'medical_history_summary' => 'Diagnosed with Type 2 Diabetes (2018) and Primary Hypertension (2020).',
            'notes' => 'Patient prefers afternoon phone calls for nurse check-ins.',
        ]);

        PatientAllergy::create([
            'clinic_id' => $clinic1->id,
            'patient_id' => $patient1->id,
            'allergen' => 'Penicillin',
            'severity' => 'severe',
            'reaction' => 'Skin rash and facial swelling',
        ]);

        PatientCondition::create([
            'clinic_id' => $clinic1->id,
            'patient_id' => $patient1->id,
            'condition_name' => 'Type 2 Diabetes Mellitus',
            'diagnosed_date' => '2018-05-10',
            'status' => 'active',
        ]);

        PatientCondition::create([
            'clinic_id' => $clinic1->id,
            'patient_id' => $patient1->id,
            'condition_name' => 'Essential Hypertension',
            'diagnosed_date' => '2020-02-14',
            'status' => 'active',
        ]);

        // Assign Nurse Poly to Abdul Karim
        PatientAssignment::create([
            'clinic_id' => $clinic1->id,
            'patient_id' => $patient1->id,
            'nurse_id' => $nurse1->id,
            'assigned_by_user_id' => $admin1->id,
            'status' => 'active',
        ]);

        // Follow-up Plan for Nurse Poly
        $followUp1 = FollowUpPlan::create([
            'clinic_id' => $clinic1->id,
            'patient_id' => $patient1->id,
            'assigned_nurse_id' => $nurse1->id,
            'doctor_id' => $doc1->id,
            'due_date' => now()->subDay(),
            'frequency' => 'weekly',
            'priority' => 'high',
            'instructions' => 'Follow up on morning fasting blood glucose and blood pressure compliance.',
            'status' => FollowUpStatus::Completed,
        ]);

        // Completed Call & Health Observation by Nurse Poly
        $call1 = PatientCall::create([
            'clinic_id' => $clinic1->id,
            'patient_id' => $patient1->id,
            'nurse_id' => $nurse1->id,
            'follow_up_plan_id' => $followUp1->id,
            'call_date' => now()->subHours(5),
            'duration_seconds' => 340,
            'reached' => true,
            'outcome' => CallOutcome::Completed,
            'notes' => 'Spoke with patient. Reported mild morning dizziness. Fasting blood sugar was 9.4 mmol/L.',
            'next_follow_up_date' => now()->addDays(7),
        ]);

        HealthObservation::create([
            'clinic_id' => $clinic1->id,
            'patient_id' => $patient1->id,
            'recorded_by_user_id' => $nurse1->id,
            'patient_call_id' => $call1->id,
            'systolic_bp' => 145,
            'diastolic_bp' => 92,
            'blood_glucose' => 9.40,
            'glucose_type' => 'fasting',
            'temperature_f' => 98.4,
            'weight_kg' => 76.5,
            'pulse_rate' => 78,
            'symptoms' => 'Mild morning dizziness and fatigue',
            'medication_adherence' => 'partial',
            'patient_concerns' => 'Patient missed Metformin evening dose twice last week due to gastric discomfort.',
            'escalation_needed' => true,
        ]);

        // Health Report submitted by Nurse Poly -> In Doctor Review Queue
        $report1 = HealthReport::create([
            'clinic_id' => $clinic1->id,
            'patient_id' => $patient1->id,
            'nurse_id' => $nurse1->id,
            'patient_call_id' => $call1->id,
            'title' => 'Weekly Chronic Care Review: Elevated FBG (9.4 mmol/L) & BP (145/92)',
            'summary' => 'Patient reached via phone. Blood pressure is slightly elevated (145/92 mmHg) and fasting glucose is 9.4 mmol/L. Reports gastric upset with current medication. Recommending doctor review for dose adjustment.',
            'status' => HealthReportStatus::Submitted,
            'submitted_at' => now()->subHours(4),
        ]);

        // Commission Ledger Entry for Nurse Poly's completed follow up
        CommissionLedgerEntry::create([
            'clinic_id' => $clinic1->id,
            'staff_user_id' => $nurse1->id,
            'activity_type' => 'nurse_follow_up',
            'source_type' => HealthReport::class,
            'source_id' => $report1->id,
            'calculation_basis' => 0.0,
            'rate_applied' => 50.00,
            'amount' => 50.00,
            'currency' => 'BDT',
            'status' => CommissionStatus::Pending,
            'idempotency_key' => "clinic_{$clinic1->id}_role_nurse_act_nurse_follow_up_src_HealthReport_{$report1->id}",
        ]);

        // Appointment for Abdul Karim with Dr. Tariq
        $appt1 = Appointment::create([
            'clinic_id' => $clinic1->id,
            'patient_id' => $patient1->id,
            'doctor_id' => $doc1->id,
            'service_id' => $srvSpecialist->id,
            'scheduled_at' => now()->addHours(2),
            'duration_minutes' => 30,
            'status' => AppointmentStatus::Scheduled,
            'booking_source' => 'staff',
            'notes' => 'Follow-up consultation after nurse call alert.',
        ]);

        AppointmentStatusHistory::create([
            'appointment_id' => $appt1->id,
            'from_status' => 'none',
            'to_status' => AppointmentStatus::Scheduled->value,
            'changed_by_user_id' => $receptionist1->id,
            'reason' => 'Scheduled by front desk following nurse escalation',
        ]);

        // Invoice for Appointment 1
        $inv1 = Invoice::create([
            'clinic_id' => $clinic1->id,
            'patient_id' => $patient1->id,
            'appointment_id' => $appt1->id,
            'invoice_number' => 'INV-2026-0001',
            'subtotal' => 1200.00,
            'discount_amount' => 0.00,
            'tax_amount' => 0.00,
            'total_amount' => 1200.00,
            'paid_amount' => 1200.00,
            'currency' => 'BDT',
            'status' => InvoiceStatus::Paid,
            'due_date' => now()->toDateString(),
            'notes' => 'Specialist Diabetology consultation fee',
        ]);

        InvoiceItem::create([
            'invoice_id' => $inv1->id,
            'service_id' => $srvSpecialist->id,
            'description' => 'Diabetology & Endocrinology Care',
            'quantity' => 1,
            'unit_price' => 1200.00,
            'total_price' => 1200.00,
        ]);

        Payment::create([
            'clinic_id' => $clinic1->id,
            'invoice_id' => $inv1->id,
            'payment_number' => 'PAY-2026-0001',
            'amount' => 1200.00,
            'currency' => 'BDT',
            'method' => 'bKash',
            'reference' => 'BK994827104',
            'status' => 'completed',
            'recorded_by_user_id' => $receptionist1->id,
            'paid_at' => now()->subHour(),
        ]);

        // Patient 2: Salma Begum
        $patientUser2 = User::create([
            'name' => 'Salma Begum',
            'email' => 'patient.salma@healthmate.test',
            'phone' => '+8801717777771',
            'password' => Hash::make('password'),
            'status' => 'active',
        ]);
        ClinicMembership::create([
            'clinic_id' => $clinic1->id,
            'user_id' => $patientUser2->id,
            'role' => 'patient',
            'status' => 'active',
        ]);

        $patient2 = Patient::create([
            'clinic_id' => $clinic1->id,
            'user_id' => $patientUser2->id,
            'patient_number' => 'HM-2026-0002',
            'first_name' => 'Salma',
            'last_name' => 'Begum',
            'date_of_birth' => '1984-09-20',
            'gender' => 'female',
            'blood_group' => 'O+',
            'phone' => '+8801717777771',
            'email' => 'patient.salma@healthmate.test',
            'address' => 'Mirpur-10, Dhaka',
            'notes' => 'Routine general wellness checkup',
        ]);

        // 4. Clinic 2: Apex Family Care (Chittagong) - for strict cross-tenant verification!
        $clinic2 = Clinic::create([
            'name' => 'Apex Family Health Clinic',
            'slug' => 'apex-health',
            'email' => 'contact@apex.test',
            'phone' => '+8801811111111',
            'address' => 'GEC Circle, Chittagong',
            'city' => 'Chittagong',
            'timezone' => 'Asia/Dhaka',
            'currency' => 'BDT',
            'status' => 'active',
        ]);

        Subscription::create([
            'clinic_id' => $clinic2->id,
            'subscription_plan_id' => $planBasic->id,
            'status' => SubscriptionStatus::Active->value,
            'current_period_starts_at' => now(),
            'current_period_ends_at' => now()->addMonth(),
        ]);

        $admin2 = User::create([
            'name' => 'Dr. Monirul Haque (Apex Admin)',
            'email' => 'admin@apex.test',
            'phone' => '+8801822222222',
            'password' => Hash::make('password'),
            'status' => 'active',
        ]);
        ClinicMembership::create([
            'clinic_id' => $clinic2->id,
            'user_id' => $admin2->id,
            'role' => ClinicRole::Admin->value,
            'status' => 'active',
        ]);

        $patientClinic2 = Patient::create([
            'clinic_id' => $clinic2->id,
            'patient_number' => 'APEX-2026-0001',
            'first_name' => 'Tanvir',
            'last_name' => 'Chowdhury',
            'date_of_birth' => '1990-11-05',
            'gender' => 'male',
            'phone' => '+8801899999999',
            'address' => 'Agrabad, Chittagong',
        ]);
    }
}
