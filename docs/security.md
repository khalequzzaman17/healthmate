# HealthMate Application Security Specification

## 1. Threat Modeling & Defense in Depth
Healthcare platforms handle protected health information (PHI) and financial transactions. HealthMate applies strict multi-layered defenses.

## 2. Implemented Security Controls

### 2.1 Multi-Tenant Isolation
- Database-enforced foreign keys.
- Global Eloquent scope `ClinicScope` injected on all queries.
- Tenant ID resolved strictly from authenticated session memberships; arbitrary `clinic_id` in request payloads is discarded.

### 2.2 Access Control & Role Segregation
- Role checks via `EnsureClinicRole` middleware on all sensitive route groups.
- Doctors cannot approve financial payouts.
- Nurses and receptionists cannot prescribe drugs or author clinical diagnoses (HTTP 403 Forbidden).
- Patients cannot access internal clinic workspaces or other patients' charts.

### 2.3 Injection & Parameter Tampering Prevention
- Parameterized queries and Eloquent ORM eliminate SQL injection vulnerabilities.
- Blade's automatic HTML entity escaping (`{{ $variable }}`) prevents Cross-Site Scripting (XSS).
- CSRF token validation on all POST/PUT/DELETE requests via `ValidateCsrfToken`.
- Strict Mass-Assignment protection with explicit `$fillable` arrays on all Eloquent models.

### 2.4 Cryptographic Standards
- User passwords hashed using Bcrypt / Argon2ID (`Hash::make()`).
- Session cookies marked with `HttpOnly`, `SameSite=Lax`, and `Secure` over HTTPS.

### 2.5 Audit & Forensic Logging
- Every sensitive mutation logs to `audit_logs` (actor, action, auditable type, diff of old/new values, IP address, user agent).
- Every clinical record access logs to `record_access_logs` (actor, patient ID, resource type, resource ID, action).
