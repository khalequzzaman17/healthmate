# HealthMate Roles and Permissions Matrix

## 1. Overview
HealthMate implements explicit role-based access controls (RBAC) scoped per clinic tenant. A user may have different roles in different clinics (e.g. Doctor in Clinic A, Clinic Admin in Clinic B).

## 2. Roles Definition (`App\Enums\ClinicRole`)

| Role | Primary Scope | Responsibilities |
|---|---|---|
| **Super Admin** | Platform-wide | Manages clinic tenants, subscription plans, platform metrics, and system maintenance. |
| **Clinic Admin** | Clinic Workspace | Manages clinic settings, staff invitations, commission rules, service catalogs, financial reports, and commission payouts. |
| **Doctor** | Clinic Workspace | Reviews nurse reports, conducts consultations, writes clinical notes, diagnoses conditions, issues prescriptions, sets follow-ups, and views own commissions. |
| **Nurse** | Clinic Workspace | Manages patient call queue, records call attempts and structured vitals (BP, glucose, temperature), submits health reports to doctor review queue, and views own commissions. |
| **Receptionist** | Clinic Workspace | Registers patients, schedules and reschedules appointments, manages patient check-ins, creates invoices, and records payments. |
| **Accountant** | Clinic Workspace | Audits invoices, payment transactions, reconciles accounts, prepares commission payouts, and reviews financial summaries. |
| **Patient** | Patient Portal | Views own medical records, prescriptions, consultation summaries, invoices, and books self-service appointments. |

## 3. Detailed Ability Matrix

| Feature / Action | Super Admin | Clinic Admin | Doctor | Nurse | Receptionist | Accountant | Patient |
|---|:---:|:---:|:---:|:---:|:---:|:---:|:---:|
| Switch Clinic Workspace | Yes | Yes | Yes | Yes | Yes | Yes | No |
| Register / Edit Clinic Profile | Yes | Yes | No | No | No | No | No |
| Invite Staff Members | Yes | Yes | No | No | No | No | No |
| Register New Patient | No | Yes | Yes | Yes | Yes | No | Self |
| View Patient Demographic Record | No | Yes | Yes | Yes | Yes | Yes | Own |
| View Patient Medical Timeline | No | Yes | Yes | Yes | No | No | Own |
| Book Appointment | No | Yes | Yes | Yes | Yes | No | Self |
| Check-in Appointment | No | Yes | Yes | Yes | Yes | No | No |
| Record Patient Call & Vitals | No | Yes | No | **Yes** | No | No | No |
| Submit Health Report for Review | No | Yes | No | **Yes** | No | No | No |
| Review Nurse Health Report | No | Yes | **Yes** | No | No | No | No |
| Record Consultation & Diagnosis | No | No | **Yes** | **No (403)**| **No (403)**| No | No |
| Issue / Amend Prescription | No | No | **Yes** | **No (403)**| **No (403)**| No | No |
| View All Clinic Commissions | Yes | Yes | No | No | No | Yes | No |
| View Personal Commissions | No | Yes | **Yes** | **Yes** | No | No | No |
| Approve / Process Payouts | No | **Yes** | **No (403)**| **No (403)**| No | **Yes** | No |
| Create Invoice & Record Payment | No | Yes | No | No | **Yes** | **Yes** | No |
| View Invoice & Pay Online | No | Yes | No | No | Yes | Yes | **Own** |
| Access Audit Logs | Yes | Yes | No | No | No | No | No |

## 4. Enforcement Layer
Role permissions are enforced at the HTTP boundary via:
- `App\Http\Middleware\EnsureClinicRole` (`Route::middleware('role:doctor,admin')`)
- `App\Http\Middleware\EnsureSuperAdmin` (`Route::middleware('super_admin')`)
- Direct programmatic policy checks in Domain Services (`NurseWorkflowService`, `DoctorConsultationService`, `CommissionService`).
