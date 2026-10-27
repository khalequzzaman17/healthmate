# HealthMate Patient Workflow

## 1. Overview
The patient journey in HealthMate encompasses registration, nurse assignment, appointment scheduling, health follow-up calls, clinical consultations, prescription issuance, and payment settlement.

## 2. Patient Registration
- Handled via `PatientController@store` (`POST /patients`).
- Supports Bangladeshi phone numbers (`+8801...`), emergency contact details, blood group, allergies, and chronic conditions.
- Automatically generates a clinic-unique patient number formatted as `HM-YYYY-XXXX` (e.g. `HM-2026-0001`).
- Optional immediate nurse assignment links the patient to a care coordinator.

## 3. Unified Patient Timeline
When viewing a patient's chart (`GET /patients/{patient}`), HealthMate stitches together a chronological, unified timeline:
1. **Registered Demographic Profile**: Basic demographic, blood group, emergency contact.
2. **Recorded Allergies & Chronic Conditions**: Critical clinical safety highlights.
3. **Appointment History**: Booked, checked-in, completed, or cancelled visits.
4. **Nurse Call & Outreach History**: Logged outreach attempts, reached/unreached status, notes.
5. **Vitals & Observations**: Blood pressure (systolic/diastolic), pulse, blood glucose (fasting/random/post-prandial), temperature, weight.
6. **Submitted Health Reports**: Formally submitted reports with doctor reviews and feedback.
7. **Doctor Consultations**: Clinical encounters, physical exams, and diagnoses.
8. **Prescriptions**: Issued medicines with dosage, frequency, route, and duration.
9. **Invoices & Payment Receipts**: Financial statements and payment history.

## 4. Patient Portal Self-Service
Patients can independently log into their portal (`/portal`):
- View their upcoming appointment schedule.
- Book appointments directly with clinic doctors based on doctor availability.
- View authorized medical records and prescriptions without needing staff assistance.
- Review their billing and payment balance.
