# HealthMate — Multi-Clinic Healthcare Management SaaS Platform

[![PHP Unit & Pest](https://img.shields.io/badge/Tests-16%20Passed-brightgreen)](tests)
[![Laravel](https://img.shields.io/badge/Laravel-13.x-red)](https://laravel.com)
[![Tailwind CSS](https://img.shields.io/badge/TailwindCSS-v4-blue)](https://tailwindcss.com)
[![Alpine.js](https://img.shields.io/badge/Alpine.js-v3-purple)](https://alpinejs.dev)
[![License](https://img.shields.io/badge/License-Proprietary-darkblue)](#license)

**HealthMate** is a production-grade, multi-tenant healthcare software-as-a-service (SaaS) platform built with Laravel 13, Blade, Tailwind CSS, and Alpine.js.

Designed for healthcare networks and multi-specialty clinics, HealthMate provides complete clinic workspace isolation, proactive nurse care coordination, structured doctor consultation desks, multi-item prescription authoring, an auditable staff commission engine, patient billing, and a self-service patient portal.

---

## 🌟 Key Functional Modules

1. **Strict Multi-Tenant Workspace Isolation**
   - Independent clinics operate inside their own workspaces (`TenantContext`, `ClinicScope`).
   - Strict tenant-scoped query filtering prevents cross-clinic data leakage.
   - Support for multiple clinic memberships per user with active workspace switching.

2. **Proactive Nurse Call & Follow-up Module**
   - Daily call queues with overdue follow-up tracking.
   - Structured vital signs capture (Blood Pressure, Blood Glucose, Pulse, Temperature, Weight).
   - Direct health report compilation and submission to the attending Doctor's review queue.
   - Automatic, auditable nurse commission earning.

3. **Attending Doctor Workspace**
   - Clinical review queue for nurse-submitted health reports with review feedback.
   - Today's appointment consultations with chief complaint and clinical notes.
   - Structured diagnoses with ICD-10 codings.
   - Multi-item prescription issuance (Dosage, Route, Frequency, Duration).
   - Automatic, auditable doctor commission earning.

4. **Auditable Commission Engine**
   - Configurable rules (fixed amount or percentage of service fees).
   - SHA-256 database-enforced idempotency prevents duplicate earnings on retries.
   - Immutable historical snapshots of applied commission rules.
   - Payout preparation, Clinic Admin approval, and batch disbursement reconciliation.

5. **Patient Management & Unified Timeline**
   - Human-readable clinic-specific patient numbers (`HM-YYYY-XXXX`).
   - Unified medical timeline: Appointments, outreach calls, vital trends, doctor consultations, prescriptions, and invoices.

6. **Clinic Billing & Invoicing**
   - Itemized invoices with discounts, tax calculations, and partial payment reconciliation.
   - Multiple payment methods supported: Cash, Card, bKash, Nagad, Rocket, Bank Transfer.

7. **Patient Portal**
   - Self-service portal for patients to view appointments, medical history, prescriptions, and bills.
   - Online appointment self-booking.

8. **Platform Super Administration**
   - Platform subscription tiers, active tenant monitoring, clinic suspension/reactivation.

---

## 🚀 Quick Start Guide

### 1. Requirements
- PHP 8.3 or PHP 8.4+
- Composer 2+
- Node.js 20+ & npm
- SQLite (for testing) or MySQL 8.0+ / MariaDB 10.11+ (production)

### 2. Installation
```bash
# Clone the repository
git clone https://github.com/your-org/healthmate.git
cd healthmate

# Install PHP dependencies
composer install

# Install JS dependencies & compile frontend assets
npm install
npm run build

# Setup environment configuration
cp .env.example .env
php artisan key:generate

# Run database migrations and seed sample demo data
php artisan migrate:fresh --seed
```

### 3. Start Local Development Server
```bash
php artisan serve
```
Visit `http://127.0.0.1:8000` in your web browser.

---

## 🔑 Demo Credentials

The database seeder provisions two independent clinics (`CarePoint Multi-Specialty Clinic` and `Apex Family Health Clinic`) to demonstrate strict tenant isolation and role workflows.

All demo accounts use password: `password`

| Role | Name | Email | Clinic Workspace |
|---|---|---|---|
| **Super Admin** | Platform Administrator | `superadmin@healthmate.test` | Platform Management |
| **Clinic Admin** | Dr. Kazi Mahfuz | `admin@carepoint.test` | CarePoint Clinic |
| **Doctor** | Dr. Tariqul Islam | `dr.tariq@carepoint.test` | CarePoint Clinic |
| **Nurse** | Poly Begum | `nurse.poly@carepoint.test` | CarePoint Clinic |
| **Receptionist** | Sumi Akhter | `receptionist@carepoint.test` | CarePoint Clinic |
| **Accountant** | Farhan Ahmed | `accountant@carepoint.test` | CarePoint Clinic |
| **Patient** | Abdul Karim | `patient.abdul@healthmate.test` | Patient Portal |
| **Apex Clinic Admin** | Dr. Monirul Haque | `admin@apex.test` | Apex Family Care (Clinic 2) |

> **Note:** The login screen includes a 1-click **Quick Demo Login** selector that auto-fills credentials for any role.

---

## 🧪 Automated Testing

HealthMate features a comprehensive automated test suite written in **Pest PHP** verifying tenant isolation, nurse and doctor workflows, commission calculations, idempotency, role permissions, and billing:

```bash
# Run the complete test suite
php artisan test --compact
```

### Test Coverage Highlights:
- **Tenant Isolation**: Confirms Clinic B users cannot read or access Clinic A patients, appointments, or medical records (HTTP 404).
- **Double Booking Prevention**: Enforces doctor availability and rejects overlapping appointments.
- **Nurse Follow-up Lifecycle**: Tests call outcomes, vitals recording, report submission, and commission generation.
- **Doctor Consultation**: Tests report review, consultation completion, diagnoses, and multi-item prescriptions.
- **Financial Ledger & Idempotency**: Tests fixed & percentage rules, rule snapshotting, duplicate prevention, and payout batches.
- **Role Permissions**: Tests that nurses/receptionists cannot prescribe drugs (HTTP 403) and doctors cannot approve payouts without admin privileges.

---

## 📚 Technical Documentation

Detailed architectural and workflow documentation is located in the [`docs/`](docs) directory:

- [System Architecture](docs/architecture.md)
- [Multi-Tenancy Isolation](docs/tenancy.md)
- [Roles & Permissions Matrix](docs/permissions.md)
- [Database Schema Specification](docs/database-schema.md)
- [Patient Workflow](docs/patient-workflow.md)
- [Nurse Workflow & Call Management](docs/nurse-workflow.md)
- [Doctor Workspace & Consultation](docs/doctor-workflow.md)
- [Auditable Commission Engine](docs/commission-engine.md)
- [Billing & Payments](docs/billing.md)
- [SaaS Subscriptions](docs/subscriptions.md)
- [Application Security Specification](docs/security.md)
- [Privacy & Medical Record Integrity](docs/privacy-and-retention.md)
- [Automated Testing Strategy](docs/testing.md)
- [Production Deployment Guide](docs/deployment.md)
- [Backup & Disaster Recovery](docs/backup-and-recovery.md)
- [Integrations & Payment Gateways](docs/api-and-integrations.md)

---

## 🛡️ License
Proprietary — HealthMate Multi-Clinic SaaS Platform. All rights reserved.
