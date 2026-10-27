# HealthMate Automated Testing Strategy

## 1. Overview
Automated testing is implemented using **Pest PHP**. Tests cover feature workflows, cross-tenant isolation, role permissions, and financial calculation accuracy.

## 2. Test Suite Structure (`tests/Feature/`)

- `TenantIsolationTest.php`:
  - Validates that patients, appointments, and consultations are filtered to the active clinic.
  - Confirms global query scoping prevents cross-tenant data leakage.
- `NurseWorkflowTest.php`:
  - Tests patient call logging with structured vitals (BP, glucose, pulse, temperature).
  - Verifies report submission to the doctor review queue.
  - Confirms immediate creation of nurse commission ledger entries.
- `DoctorConsultationTest.php`:
  - Tests doctor review of nurse health reports with feedback.
  - Verifies encounter documentation: clinical notes, ICD-10 diagnoses, multi-item prescriptions.
  - Confirms doctor commission ledger entries.
- `CommissionEngineTest.php`:
  - Tests fixed and percentage calculation rules.
  - Verifies database-enforced idempotency against duplicate payouts.
  - Tests payout batch preparation, admin approval, and payment processing.
- `RolePermissionSecurityTest.php`:
  - Tests that nurses cannot create prescriptions or consultations (HTTP 403).
  - Tests that doctors cannot approve commission payouts without admin role (HTTP 403).
  - Tests that patients cannot access internal clinic workspaces (HTTP 403).
  - Tests that receptionists cannot access consultation desks (HTTP 403).
- `ComprehensiveWorkflowTest.php`:
  - Full end-to-end integration test: Nurse Follow-up -> Doctor Consultation -> Prescription -> Commission Payout.
  - Appointment booking with conflict rejection for overlapping doctor time slots.
  - Billing invoice generation and partial/full payment settlement.
  - Cross-tenant authorization denial (HTTP 404).

## 3. Running Tests
To run the automated test suite:
```bash
php artisan test --compact
```
Or directly with Pest:
```bash
vendor/bin/pest
```
