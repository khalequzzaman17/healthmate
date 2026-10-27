# HealthMate Integrations & Payment Gateways

## 1. Localized Market Integration (Bangladesh)
HealthMate is built with configurable localization suitable for Bangladesh and South Asian healthcare markets while keeping core logic decoupled from country-specific assumptions.

### 1.1 Phone Number Normalization
- Supports Bangladeshi mobile numbers (`+8801XXXXXXXXX` and `01XXXXXXXXX`).
- Validated via standard regex patterns at form request boundary.

### 1.2 Currency & Timezone
- Default clinic currency: `BDT` (Bangladeshi Taka, symbol: ৳).
- Default clinic timezone: `Asia/Dhaka` (UTC+6).
- Formats dates and currencies using localized formatters.

## 2. Payment Gateway Architecture
The billing module supports pluggable payment gateway adapters:
- **bKash PGW (Direct API / Tokenized)**
- **Nagad Online Payment Gateway**
- **SSLCommerz (Multi-Gateway Aggregator)**
- **Stripe / Credit Card**

Payment webhook handlers verify HMAC signatures, enforce idempotency via `transaction_reference`, and atomically credit `invoices.paid_amount`.

## 3. Communication & Messaging Adapters
Planned adapter interfaces for notifications:
- **SMS Gateway**: Local BD SMS providers (e.g. Greenweb, SSL Wireless).
- **WhatsApp Business Cloud API**: Patient appointment reminders and prescription delivery.
