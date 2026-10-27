# HealthMate Database Schema Specification

## 1. Overview
The database schema is organized into 5 logical clusters across 9 migrations in `database/migrations/`. All tables use InnoDB storage engine with strict foreign key constraints and UTF8mb4 encoding.

## 2. Table Catalog

### 2.1 Tenancy & Subscriptions
- `clinics`: Clinic records (`name`, `slug`, `email`, `phone`, `address`, `city`, `timezone`, `currency`, `status`).
- `clinic_settings`: Key-value JSON and configuration parameters per clinic.
- `clinic_locations`: Physical branches and department wings per clinic.
- `subscription_plans`: Platform SaaS tiers (`name`, `slug`, `price`, `interval`, `max_doctors`, `max_patients`, `features`).
- `subscriptions`: Active clinic subscriptions (`clinic_id`, `subscription_plan_id`, `status`, `period_starts_at`, `period_ends_at`).
- `subscription_invoices`: SaaS subscription invoices billed to clinic owners.
- `clinic_memberships`: Staff and user associations to clinics (`clinic_id`, `user_id`, `role`, `status`).
- `staff_profiles`: Professional metadata (`specialization`, `qualifications`, `license_number`, `bio`).
- `doctor_schedules`: Working hours per day (`doctor_id`, `day_of_week`, `start_time`, `end_time`, `slot_duration_minutes`).

### 2.2 Patients & Medical Profiles
- `patients`: Patient demographic master (`clinic_id`, `patient_number`, `first_name`, `last_name`, `dob`, `gender`, `blood_group`, `phone`, `email`, `address`).
- `patient_allergies`: Recorded allergies (`allergen`, `severity`, `reaction`).
- `patient_conditions`: Chronic conditions (`condition_name`, `diagnosed_date`, `status`).
- `patient_assignments`: Nurse assignments to patients (`patient_id`, `nurse_id`, `assigned_by_user_id`, `status`).

### 2.3 Services & Appointments
- `services`: Clinic clinical service catalog (`name`, `code`, `price`, `duration_minutes`).
- `appointments`: Booking records (`clinic_id`, `patient_id`, `doctor_id`, `service_id`, `scheduled_at`, `duration_minutes`, `status`, `booking_source`).
- `appointment_status_history`: Audited state transition log (`appointment_id`, `from_status`, `to_status`, `changed_by_user_id`, `reason`).

### 2.4 Nurse Workflows
- `follow_up_plans`: Scheduled follow-up milestones (`patient_id`, `nurse_id`, `target_date`, `frequency`, `status`).
- `patient_calls`: Nurse call logs (`patient_id`, `nurse_id`, `outcome`, `reached`, `duration_seconds`, `notes`).
- `health_observations`: Structured clinical measurements (`systolic_bp`, `diastolic_bp`, `pulse_rate`, `blood_glucose`, `temperature`, `weight`, `symptoms`).
- `health_reports`: Nurse-compiled clinical reports awaiting doctor review (`title`, `summary`, `status`, `submitted_at`, `reviewed_at`, `reviewer_doctor_id`, `doctor_feedback`).

### 2.5 Consultations, Prescriptions & Records
- `consultations`: Doctor clinical encounters (`appointment_id`, `patient_id`, `doctor_id`, `health_report_id`, `chief_complaint`, `clinical_notes`, `completed_at`).
- `diagnoses`: Formal clinical diagnoses (`consultation_id`, `patient_id`, `icd10_code`, `condition_name`, `type`, `notes`).
- `prescriptions`: Prescriptions authored by doctors (`consultation_id`, `patient_id`, `doctor_id`, `instructions`, `issued_at`).
- `prescription_items`: Medicine lines (`medicine_name`, `strength`, `dosage`, `route`, `frequency`, `duration_days`, `instructions`).
- `medical_attachments`: Authorized uploads (`patient_id`, `file_name`, `file_path`, `mime_type`, `file_size_bytes`).

### 2.6 Commission Engine
- `commission_rules`: Clinic commission rules (`staff_role`, `activity_type`, `calculation_type`, `amount`, `percentage_rate`, `is_approval_required`).
- `commission_ledger_entries`: Append-only earnings ledger (`staff_id`, `activity_type`, `source_type`, `source_id`, `amount`, `currency`, `status`, `idempotency_key`, `rule_snapshot`).
- `commission_payouts`: Batch payouts (`staff_id`, `payout_reference`, `total_amount`, `currency`, `status`, `prepared_by_user_id`, `approved_by_user_id`).
- `commission_payout_items`: Pivot linking ledger entries to batch payouts (`commission_payout_id`, `commission_ledger_entry_id`).

### 2.7 Billing & Payments
- `invoices`: Clinic patient bills (`invoice_number`, `patient_id`, `subtotal`, `discount_amount`, `tax_amount`, `total_amount`, `paid_amount`, `status`).
- `invoice_items`: Line items (`invoice_id`, `description`, `quantity`, `unit_price`, `total_price`).
- `payments`: Collected payments (`invoice_id`, `amount`, `payment_method`, `transaction_reference`, `paid_at`).

### 2.8 Auditing
- `audit_logs`: System mutations (`action`, `auditable_type`, `auditable_id`, `old_values`, `new_values`, `ip_address`, `user_agent`).
- `record_access_logs`: Sensitive medical record read tracking (`patient_id`, `resource_type`, `resource_id`, `action`).
