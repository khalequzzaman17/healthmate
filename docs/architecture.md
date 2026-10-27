# HealthMate System Architecture

## 1. Overview
HealthMate is a multi-tenant healthcare software-as-a-service (SaaS) platform built on **Laravel 13**, **Blade**, **Tailwind CSS**, and **Alpine.js**. The platform enables independent medical clinics to operate within secure, isolated virtual workspaces while sharing a single unified infrastructure footprint.

## 2. Core Architectural Principles

### 2.1 Modular Monolith
The application is structured as a modular Laravel monolith rather than distributed microservices or an SPA:
- **Thin Controllers**: HTTP controllers act purely as request parsers and response formatters.
- **Domain Services**: Business logic, workflow state transitions, locking, and multi-step mutations reside in testable domain service classes (`App\Services\*`).
- **Enums as State Machines**: Explicit workflow states are governed by Backed PHP Enums (`AppointmentStatus`, `CallOutcome`, `HealthReportStatus`, `CommissionStatus`, etc.).
- **Transactional Consistency**: Multi-table operations (such as appointment booking, consultation completion with prescription issuance, and commission payouts) execute inside `DB::transaction()` blocks.

### 2.2 Shared Database Multi-Tenancy
- Clinics are represented by the `Clinic` model.
- Every clinic-owned entity implements the `BelongsToClinic` trait, which binds the `ClinicScope` global scope.
- Tenant context is managed by `TenantContext` service and enforced by the `IdentifyTenant` middleware.
- Defense in depth is applied: queries are scoped globally, middleware checks route-model bindings, and domain policies verify tenant ownership.

### 2.3 Layered System Diagram

```
+-------------------------------------------------------------------------+
|                              Web Clients                                |
|  (Blade Templates + Tailwind CSS + Alpine.js + Lucide Icons)            |
+-------------------------------------------------------------------------+
                                    |
                                    v
+-------------------------------------------------------------------------+
|                           HTTP Middleware                               |
|   - Authenticate (Session / Auth)                                       |
|   - IdentifyTenant (Resolves active Clinic & sets TenantContext)        |
|   - EnsureClinicRole (Enforces RBAC per tenant: doctor, nurse, admin)   |
|   - EnsureSuperAdmin (Enforces platform administration privileges)      |
+-------------------------------------------------------------------------+
                                    |
                                    v
+-------------------------------------------------------------------------+
|                        Thin HTTP Controllers                            |
|   - PatientController, AppointmentController, DoctorWorkspaceController |
|   - NurseWorkspaceController, CommissionController, BillingController   |
|   - PatientPortalController, SuperAdminController                       |
+-------------------------------------------------------------------------+
                                    |
                                    v
+-------------------------------------------------------------------------+
|                        Domain Service Layer                             |
|   - AppointmentService (Availability, double-booking prevention)        |
|   - NurseWorkflowService (Call outcomes, vitals, report submission)     |
|   - DoctorConsultationService (Report reviews, diagnoses, prescriptions) |
|   - CommissionService (Ledger calculations, rule snapshots, payouts)    |
|   - BillingService (Invoice generation, payment reconciliation)         |
|   - TenantService (Clinic onboarding, memberships, default rules)       |
|   - AuditService (System action logs, sensitive record access logs)     |
+-------------------------------------------------------------------------+
                                    |
                                    v
+-------------------------------------------------------------------------+
|                      Eloquent Models & Database                         |
|   - Global Scope: ClinicScope (`WHERE clinic_id = ?`)                   |
|   - MySQL / MariaDB (InnoDB) / SQLite for fast automated test suites    |
+-------------------------------------------------------------------------+
```

## 3. Key Design Decisions

1. **No External CDN Dependencies**: Alpine.js and Lucide icons are bundled locally via Vite and npm.
2. **Server-Side Rendered (Blade)**: Maximizes accessibility, SEO, initial load speeds, and simplifies session and CSRF security.
3. **Database-Agnostic Concurrency Checks**: Appointment overlapping calculations utilize standard datetime math compatible with both MySQL (`DATE_ADD`) and SQLite (`datetime(...)`).
4. **Append-Only Financial Ledger**: Commission ledger entries are never updated in place; reversals and adjustment history maintain absolute financial integrity.
