# HealthMate Doctor Workspace & Consultation Workflow

## 1. Overview
The Doctor Workspace (`/doctor`) provides attending clinicians with a high-throughput, structured clinical dashboard designed for rapid review of incoming nurse health reports, management of daily appointments, and complete clinical encounter documentation.

## 2. Doctor Dashboard Components
- **Today's Appointments**: Checked-in patients ready for consultation.
- **Nurse Reports Awaiting Review**: Chronic disease and follow-up submissions from nursing staff.
- **Recent Patient Updates**: Clinical alerts and newly uploaded lab diagnostics.
- **Personal Commission Ledger**: Accumulated consultation fees and payment status.

## 3. Reviewing Nurse Reports
When an attending doctor opens a submitted report (`/doctor/reports/{report}`):
- The doctor reviews all vitals collected by the nurse (BP, Glucose, Symptoms).
- The doctor can submit feedback and sign off (`POST /doctor/reports/{report}/review`) with status `reviewed` or request revisions.
- Reviewed reports link directly into future consultation encounters.

## 4. Conducting Consultations & Issuing Prescriptions
From an appointment (`/doctor/consultations/{appointment}`), the doctor completes a structured encounter:
1. **Chief Complaint & Clinical Notes**: Subjective symptoms and objective examination notes.
2. **Diagnoses**: Primary and secondary diagnoses, ICD-10 codings, and condition severity.
3. **Multi-Item Prescription Issuance**:
   - Medicine name (e.g. Tab. Metformin, Tab. Losartan Potassium)
   - Strength (e.g. 500mg, 50mg)
   - Dosage & Frequency (e.g. 1+0+1, 1+0+0)
   - Route (Oral, IV, Topical)
   - Duration in days
   - Patient administration instructions (e.g. after meals).
4. **Follow-up Instructions**: Return date and advice.

## 5. Transactional Integrity & Commission Trigger
When the doctor completes the consultation (`POST /doctor/consultations/{appointment}`):
- An atomic database transaction creates `Consultation`, `Diagnosis`, `Prescription`, and `PrescriptionItem` records.
- The appointment state updates to `completed`.
- `CommissionService` records an eligible Doctor consultation earning event.
- An action audit record (`consultation.completed`) is saved.
