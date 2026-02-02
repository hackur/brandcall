# BrandCall - Branded Caller ID SaaS Platform

## Project Overview

A multi-tenant SaaS platform that enables businesses to set up branded caller ID for their outbound calls. Customers log in, create "brands" (company name + logo), receive unique API endpoints per brand, and get charged per successful branded call with tiered volume pricing.

**Business Model:**
- $0.075/call (0-9,999 calls/month)
- $0.065/call (10,000-99,999)
- $0.050/call (100,000-999,999)
- $0.035/call (1,000,000-9,999,999)
- $0.025/call (10,000,000+)

---

## Tech Stack

| Layer | Technology | Version |
|-------|------------|---------|
| **Framework** | Laravel | 11.x |
| **PHP** | PHP | 8.3+ |
| **Frontend** | React + Inertia.js | React 19 |
| **Database** | PostgreSQL | 16+ |
| **Queue** | Redis + Laravel Horizon | - |
| **Billing** | Stripe (metered) | - |
| **Caller ID API** | NumHub BrandControl | - |
| **Storage** | S3 (logos) | - |
| **Deployment** | Laravel Forge | - |

---

## Core Features (MVP)

### 1. Authentication & Multi-Tenancy
- [x] User registration/login (Laravel Breeze + Inertia)
- [x] Tenant (company) creation on signup
- [x] Row-level security via global scopes
- [ ] Team invites (Phase 2)

### 2. Brand Management
- [ ] Create brand (name, display name, logo upload)
- [ ] Auto-generate unique API key per brand
- [ ] Brand slug for API endpoint: `/api/v1/brands/{slug}/calls`
- [ ] Brand status: draft → pending_vetting → active
- [ ] Rich Call Data config (colors, call reason)

### 3. Phone Number Management
- [ ] Add phone numbers to brands (E.164 format)
- [ ] LOA (Letter of Authorization) upload for A-level attestation
- [ ] CNAM registration
- [ ] Number health monitoring

### 4. API for Branded Calls
- [ ] `POST /api/v1/brands/{slug}/calls` - Initiate branded call
- [ ] `GET /api/v1/brands/{slug}/calls/{id}` - Get call status
- [ ] `GET /api/v1/brands/{slug}/numbers` - List phone numbers
- [ ] API key authentication (Bearer token)
- [ ] Rate limiting (1000 req/min)

### 5. NumHub Integration
- [ ] Register enterprise with NumHub BrandControl
- [ ] Submit phone numbers for STIR/SHAKEN attestation
- [ ] Make branded calls via NumHub API
- [ ] Handle webhooks for call status updates
- [ ] Rich Call Data (logo, brand colors, call reason)

### 6. Usage Tracking & Billing
- [ ] Track calls per tenant per month
- [ ] Calculate tiered pricing in real-time
- [ ] Stripe metered billing subscription
- [ ] Sync usage to Stripe (queued job)
- [ ] Monthly invoice generation

### 7. Dashboard
- [ ] Overview: total calls, revenue, active brands
- [ ] Call volume charts (30 days)
- [ ] Per-brand analytics
- [ ] API documentation with code examples
- [ ] API key management (view, rotate)

---

## Database Schema

### Tables

```
tenants
├── id
├── name (company name)
├── email
├── slug (for subdomain/routing)
├── stripe_customer_id
├── subscription_tier (starter/growth/enterprise)
├── monthly_call_limit (null = unlimited)
├── settings (JSON)
├── created_at, updated_at, deleted_at

users
├── id
├── tenant_id (FK)
├── name
├── email
├── password
├── role (owner/admin/member)
├── created_at, updated_at

brands
├── id
├── tenant_id (FK)
├── name ("Health Insurance Florida")
├── slug ("health-insurance-florida")
├── display_name (32-char CNAM)
├── logo_path (S3)
├── call_reason
├── rich_call_data (JSON: colors, secondary logo)
├── numhub_enterprise_id
├── numhub_vetting_status (pending/approved/rejected)
├── api_key (unique)
├── api_key_last_rotated_at
├── default_attestation_level (A/B/C)
├── status (draft/pending_vetting/active/suspended)
├── metadata (JSON)
├── created_at, updated_at, deleted_at

brand_phone_numbers
├── id
├── brand_id (FK)
├── phone_number (E.164)
├── country_code
├── loa_document_path
├── loa_verified_at
├── ownership_status (unverified/verified/pending)
├── cnam_display_name
├── cnam_registered
├── status (active/suspended/quarantined)
├── created_at, updated_at

call_logs
├── id
├── tenant_id (FK)
├── brand_id (FK)
├── brand_phone_number_id (FK)
├── call_id (internal UUID)
├── external_call_sid (NumHub)
├── from_number
├── to_number
├── attestation_level (A/B/C)
├── status (initiated/ringing/in-progress/completed/failed)
├── failure_reason
├── branded_call (bool)
├── rcd_payload (JSON)
├── call_initiated_at
├── call_answered_at
├── call_ended_at
├── ring_duration_seconds
├── talk_duration_seconds
├── cost
├── tier_price (at time of call)
├── billable (bool)
├── numhub_response (JSON)
├── created_at, updated_at

usage_records
├── id
├── tenant_id (FK)
├── year
├── month
├── call_count
├── successful_calls
├── failed_calls
├── total_cost
├── tier_price
├── stripe_usage_record_id
├── synced_to_stripe
├── created_at, updated_at
├── UNIQUE(tenant_id, year, month)

pricing_tiers
├── id
├── min_calls
├── max_calls (null = unlimited)
├── price_per_call
├── order
├── active
├── created_at, updated_at

webhook_logs
├── id
├── tenant_id (FK, nullable)
├── source (numhub/stripe)
├── event_type
├── payload (JSON)
├── signature
├── verified
├── processed
├── processed_at
├── processing_error
├── created_at, updated_at
```

---

## API Design

### Authentication
All API requests require Bearer token:
```
Authorization: Bearer {brand_api_key}
```

### Endpoints

#### Initiate Branded Call
```
POST /api/v1/brands/{slug}/calls

Request:
{
  "from": "+15551234567",
  "to": "+15559876543",
  "call_reason": "Appointment Reminder",
  "attestation_level": "A",
  "metadata": {"customer_id": "12345"}
}

Response (201):
{
  "success": true,
  "call_id": "call_abc123",
  "external_call_sid": "NH123456",
  "status": "initiated",
  "cost": 0.0750,
  "attestation_level": "A",
  "brand": {
    "name": "Health Insurance Florida",
    "display_name": "FL Health Ins",
    "logo_url": "https://s3.../logo.png",
    "call_reason": "Appointment Reminder"
  },
  "stir_shaken": {
    "enabled": true,
    "verified": true
  }
}
```

#### Get Call Status
```
GET /api/v1/brands/{slug}/calls/{call_id}

Response:
{
  "call_id": "call_abc123",
  "status": "completed",
  "from": "+15551234567",
  "to": "+15559876543",
  "duration": {
    "ring_seconds": 12,
    "talk_seconds": 180,
    "total_seconds": 192
  },
  "cost": 0.0750,
  "timestamps": {
    "initiated_at": "2026-01-31T19:00:00Z",
    "answered_at": "2026-01-31T19:00:12Z",
    "ended_at": "2026-01-31T19:03:12Z"
  }
}
```

#### List Phone Numbers
```
GET /api/v1/brands/{slug}/numbers

Response:
{
  "numbers": [
    {
      "phone_number": "+15551234567",
      "status": "active",
      "attestation": {
        "ownership_verified": true,
        "can_attest_a_level": true
      },
      "registrations": {
        "cnam": true,
        "free_caller_registry": true
      }
    }
  ]
}
```

---

## NumHub Integration

### Registration Flow
1. User creates brand → Queue job to register with NumHub
2. Upload logo to S3 → Pass URL to NumHub
3. NumHub returns enterprise_id → Store on brand
4. User adds phone numbers → Register with NumHub for attestation
5. NumHub vetting (1-3 days) → Webhook updates status

### Making Calls
1. API request comes in → Validate brand/number ownership
2. Calculate tier price → Log call as "initiated"
3. Send to NumHub with RCD payload → Get call_sid
4. NumHub handles STIR/SHAKEN signing
5. Webhooks update call status → Completed/Failed

### Webhook Events
- `enterprise.approved` - Brand vetting completed
- `enterprise.rejected` - Brand vetting failed
- `call.initiated` - Call started
- `call.ringing` - Recipient phone ringing
- `call.answered` - Call connected
- `call.completed` - Call ended successfully
- `call.failed` - Call failed

---

## Directory Structure

```
brandcall/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/
│   │   │   │   ├── BrandedCallController.php
│   │   │   │   └── PhoneNumberController.php
│   │   │   ├── BrandController.php
│   │   │   ├── DashboardController.php
│   │   │   └── WebhookController.php
│   │   └── Middleware/
│   │       ├── ResolveTenant.php
│   │       └── VerifyApiKey.php
│   ├── Jobs/
│   │   ├── RegisterWithNumHub.php
│   │   ├── SyncUsageToStripe.php
│   │   └── ProcessCallWebhook.php
│   ├── Models/
│   │   ├── Tenant.php
│   │   ├── User.php
│   │   ├── Brand.php
│   │   ├── BrandPhoneNumber.php
│   │   ├── CallLog.php
│   │   ├── UsageRecord.php
│   │   └── PricingTier.php
│   ├── Scopes/
│   │   └── TenantScope.php
│   └── Services/
│       ├── NumHubService.php
│       ├── UsageTrackingService.php
│       └── StripeService.php
├── database/
│   ├── migrations/
│   └── seeders/
│       └── PricingTierSeeder.php
├── resources/
│   └── js/
│       └── Pages/
│           ├── Dashboard.tsx
│           ├── Brands/
│           │   ├── Index.tsx
│           │   ├── Create.tsx
│           │   └── Show.tsx
│           └── Settings/
├── routes/
│   ├── api.php
│   └── web.php
└── config/
    └── services.php (numhub, stripe)
```

---

## Implementation Phases

### Phase 1: Foundation (Week 1)
- [x] Laravel project setup with Inertia + React + TypeScript
- [x] SQLite database + migrations (switch to PostgreSQL for prod)
- [x] Authentication (Breeze)
- [x] Tenant model + TenantScope
- [x] All models created (Tenant, Brand, BrandPhoneNumber, CallLog, UsageRecord, PricingTier)
- [x] Pricing tiers seeded
- [ ] Brand CRUD UI
- [ ] Logo upload to S3
- [ ] Basic dashboard

### Phase 2: Core API (Week 2)
- [ ] Phone number management
- [ ] API key generation
- [ ] API endpoints (calls, status, numbers)
- [ ] Call logging
- [ ] Rate limiting

### Phase 3: NumHub Integration (Week 3)
- [ ] NumHub service class
- [ ] Enterprise registration job
- [ ] Phone number registration
- [ ] Make branded calls
- [ ] Webhook handling

### Phase 4: Billing (Week 4)
- [ ] Stripe customer creation
- [ ] Metered subscription setup
- [ ] Usage tracking
- [ ] Tier price calculation
- [ ] Usage sync to Stripe

### Phase 5: Dashboard & Polish (Week 5)
- [ ] Call volume charts
- [ ] Per-brand analytics
- [ ] API documentation page
- [ ] API key rotation
- [ ] Error handling + logging

---

## Environment Variables

```env
# App
APP_NAME=BrandCall
APP_URL=https://brandcall.io

# Database
DB_CONNECTION=pgsql
DB_DATABASE=brandcall
DB_USERNAME=brandcall
DB_PASSWORD=secret

# NumHub BrandControl
NUMHUB_API_URL=https://api.numhub.com
NUMHUB_API_KEY=
NUMHUB_API_SECRET=
NUMHUB_WEBHOOK_SECRET=

# Stripe
STRIPE_KEY=pk_live_xxx
STRIPE_SECRET=sk_live_xxx
STRIPE_METERED_PRICE_ID=price_xxx

# AWS S3
AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=brandcall-logos

# Queue
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
```

---

## Questions to Answer

1. **NumHub API access** - Do you have credentials, or need to sign up?
2. **Domain** - What domain will this run on?
3. **Deployment** - Laravel Forge, or different?
4. **Phone provisioning** - Will customers bring their own numbers, or do you want to offer number purchasing?
5. **SMS support** - Voice-only for MVP, or include A2P 10DLC?

---

## Next Steps

1. Create Laravel project with Breeze + Inertia + React
2. Set up PostgreSQL database
3. Create initial migrations
4. Build Brand CRUD
5. Get NumHub API credentials
6. Continue from there

---

*Created: 2026-01-31*
*Status: Planning*
