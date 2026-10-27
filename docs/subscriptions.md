# HealthMate SaaS Subscription Architecture

## 1. Overview
HealthMate operates as a B2B SaaS platform. Independent clinics purchase subscriptions to access the platform.

## 2. Subscription Tiers (`App\Models\SubscriptionPlan`)
Plans define entitlements and operational boundaries:
- **Starter Health Clinic**: 1-2 doctors, 300 active patients, basic scheduling & nurse calls.
- **Professional Health SaaS**: Up to 10 doctors, 2,000 active patients, doctor review portal, auditable commission engine, billing & analytics.
- **Enterprise Multi-Branch**: Unlimited doctors, unlimited patients, multi-location management, custom API access.

## 3. Subscription States (`App\Enums\SubscriptionStatus`)
- `active`: Fully operational.
- `trialing`: Within trial window.
- `past_due`: Grace period for payment resolution.
- `cancelled`: Subscription terminated at end of period.
- `expired`: Subscription elapsed; workspace restricted to read-only archival view.

## 4. Preservation of Patient Data
In compliance with healthcare compliance standards:
- **No Indiscriminate Deletion**: An expired clinic subscription NEVER triggers deletion of patient health records.
- Records remain accessible in read-only mode for verified medical continuity and legal compliance.
