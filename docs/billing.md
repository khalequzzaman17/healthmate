# HealthMate Billing & Payment System

## 1. Overview
The billing module enables clinics to bill patients for medical services, diagnostic tests, procedures, and medication while supporting partial payments, discounts, and reconciliation.

## 2. Invoicing Workflow
- Invoices are created via `BillingController@store` (`POST /billing`).
- Each invoice receives a clinic-scoped unique invoice number (e.g. `INV-2026-0001`).
- Supports arbitrary line items (`description`, `quantity`, `unit_price`, `total_price`).
- Supports discounts and taxes.

## 3. Financial Invariants
1. `subtotal` = Sum of all line item totals.
2. `total_amount` = `subtotal` - `discount_amount` + `tax_amount`.
3. `paid_amount` = Sum of all recorded payments.
4. `due_amount` = `total_amount` - `paid_amount`.

### Status Progression (`App\Enums\InvoiceStatus`):
- `unpaid`: `paid_amount == 0`
- `partially_paid`: `0 < paid_amount < total_amount`
- `paid`: `paid_amount >= total_amount`
- `cancelled`: Revoked by clinic admin

## 4. Payment Collection & Multi-Method Support
Payments are recorded via `BillingController@recordPayment` (`POST /billing/{invoice}/payments`):
- Payment methods supported: `Cash`, `Card (POS)`, `bKash`, `Nagad`, `Rocket`, `Bank Transfer`.
- Collects transaction reference and timestamp.
- Atomic updates ensure `invoices.paid_amount` matches actual payment receipts.
