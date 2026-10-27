# HealthMate Nurse Workflow & Call Management

## 1. Differentiating Value Proposition
The proactive Nurse Call & Follow-up module is a key differentiator of HealthMate. Rather than waiting for patients to fall ill and visit the clinic, clinics actively coordinate chronic care, post-op recovery, and routine monitoring.

## 2. Dedicated Nurse Workspace (`/nurse`)
The Nurse Workspace presents:
- **Calls Due Today**: Scheduled follow-ups requiring proactive outreach.
- **Overdue Follow-ups**: Missed or delayed outreach tasks requiring priority attention.
- **Assigned Patients**: The nurse's active panel of coordinated patients.
- **Pending Health Reports**: Reports submitted by the nurse awaiting doctor review.
- **Doctor Review Status**: Feedback and review notes provided by attending doctors.
- **Personal Commission Statement**: Earnings accumulated from verified follow-up activities.

## 3. Call Recording & Structured Vitals Collection
When conducting an outreach call (`POST /nurse/patients/{patient}/call`), the nurse records:
- **Call Outcome**: `completed`, `no_answer`, `busy`, `callback_requested`, `wrong_number`, `patient_declined`, or `escalated`.
- **Reached Flag**: Boolean indicating successful connection.
- **Duration**: Call duration in seconds.
- **Structured Observations**:
  - Blood Pressure (Systolic & Diastolic mmHg)
  - Blood Glucose (mmol/L or mg/dL) with context (`fasting`, `random`, `post_prandial`)
  - Pulse Rate (bpm), Body Temperature (°F/°C), Weight (kg)
  - Patient-reported symptoms and medication adherence notes.

## 4. Report Submission to Doctor Review Queue
If the nurse selects **Submit Health Report**, the system:
1. Creates a `HealthReport` record with status `submitted`.
2. Packages the gathered vitals and observations into the report.
3. Automatically places the report in the attending Doctor's review queue.
4. Triggers an auditable Nurse Commission event via `CommissionService::recordActivity()`.
5. Schedules the next follow-up milestone if requested.
