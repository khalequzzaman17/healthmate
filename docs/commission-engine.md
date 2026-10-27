# HealthMate Auditable Commission Engine

## 1. Overview
The HealthMate Commission Engine provides a tamper-evident, idempotent financial accounting ledger that calculates, tracks, approves, and disburses staff commissions for Nurses and Doctors.

## 2. Commission Rules (`App\Models\CommissionRule`)
Each clinic configures its own commission rules:
- **Staff Role**: `nurse` or `doctor`.
- **Activity Type**: `nurse_follow_up`, `doctor_consultation`, or procedure-specific.
- **Calculation Type**:
  - `fixed`: Fixed BDT amount (e.g. 50.00 BDT per completed nurse follow-up, 250.00 BDT per consultation).
  - `percentage`: Percentage of the service fee (e.g. 20% of consultation price).
- **Approval Requirement**: Configurable flag requiring Clinic Admin approval prior to payout inclusion.

## 3. Idempotent Ledger Entries (`App\Models\CommissionLedgerEntry`)
To eliminate duplicate payouts caused by retried queue jobs or double form submissions, every earning event has an SHA-256 idempotency key:
`idempotency_key = hash('sha256', "{$clinicId}_{$staffId}_{$activityType}_{$sourceType}_{$sourceId}")`

A unique database index on `idempotency_key` guarantees that no business activity can ever yield more than one earning entry.

### Rule Snapshots
Whenever an earning is posted, the active commission rule is snapshotted into `rule_snapshot` (JSON):
```json
{
  "rule_id": 1,
  "calculation_type": "fixed",
  "amount": 50.00,
  "percentage_rate": null
}
```
If a clinic later updates its commission rates, historical earnings remain completely unaffected.

## 4. Commission Lifecycle States (`App\Enums\CommissionStatus`)

```
[ Activity Completed ]
          |
          v
   +--------------+
   |   pending    | <--- Awaiting Clinic Admin review
   +--------------+
          |
          | Admin Approves
          v
   +--------------+
   |   approved   | <--- Eligible for inclusion in payout batch
   +--------------+
          |
          | Payout Batch Prepared
          v
   +--------------+
   |   payable    | <--- Locked in CommissionPayout
   +--------------+
          |
          | Payment Reference Recorded
          v
   +--------------+
   |     paid     | <--- Disbursed
   +--------------+
```

## 5. Payout Batch Processing (`App\Models\CommissionPayout`)
- **Step 1 - Preparation**: Clinic Admin selects eligible staff member and approved ledger items (`POST /commissions/payouts/prepare`). The selected items transition to status `payable` and are locked to prevent concurrent double-payouts.
- **Step 2 - Approval**: Authorized Clinic Admin or Accountant reviews and approves the payout batch (`POST /commissions/payouts/{payout}/approve`).
- **Step 3 - Processing**: Payment reference (e.g. Bank Transfer ID, bKash Merchant TrxID) is recorded and the payout transitions to `paid`. All linked items update to `paid`.
