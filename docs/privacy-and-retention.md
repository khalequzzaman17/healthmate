# HealthMate Privacy, Data Retention, & Medical Record Integrity

## 1. Regulatory Context
HealthMate is designed in alignment with healthcare data protection principles:
- Minimization of data collection.
- Purpose limitation (clinical care, care coordination, financial settlement).
- Immutable audit trails.

## 2. Clinical Record Amendments
- Submitted clinical health reports and completed consultations are preserved for clinical safety.
- Rather than destructively overwriting previous clinical notes, any update produces an amendment record with author ID, timestamp, and amendment reason.

## 3. Data Retention Policy
- Medical records must be retained for the legally mandated period (minimum 7 years from last encounter).
- Soft deletion is disabled for core patient records and consultations to prevent accidental data loss.
- In the event of clinic subscription termination, clinical records are archived in a secure, read-only state.
