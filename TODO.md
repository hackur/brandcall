# BrandCall TODO & Roadmap

> Last Updated: 2026-02-01
> Status: Phase 1 Complete, Awaiting NumHub API Credentials

---

## Current State Summary

### ✅ Completed (Phase 1: Foundation)
- Laravel 12 + Inertia.js + React 19 + TypeScript
- Multi-tenant architecture with TenantScope
- Spatie Permission RBAC (super-admin, owner, admin, member)
- Filament 3 admin panel (Tenants, Brands, Users, Phone Numbers)
- Customer dashboard (React/Inertia pages)
- All models: Tenant, Brand, BrandPhoneNumber, CallLog, UsageRecord, PricingTier
- Database migrations + seeders
- Code quality tools (Pint, Larastan Level 6)
- Smoke tests (20 page tests)
- Comprehensive documentation (11 docs)
- Test users configured

### 🚧 Blocker
- **NumHub API credentials** — Cannot proceed with call integration until obtained
- Alternative: Consider Twilio Branded Calling or Numeracle as backup

---

## Phase 2: NumHub Integration (NEXT)

**Estimated: 1 week after API access**

### Core Integration
- [ ] `app/Services/NumHubService.php` — API client class
  - [ ] Authentication (API key, OAuth, or JWT)
  - [ ] Error handling + retry logic
  - [ ] Rate limiting
  - [ ] Logging all API calls

### Brand Registration
- [ ] `RegisterBrandWithNumHub` job (queued)
- [ ] Upload logo to S3, pass URL to NumHub
- [ ] Store `numhub_enterprise_id` on Brand model
- [ ] Handle vetting status (pending → approved/rejected)

### Phone Number Registration
- [ ] Register numbers for STIR/SHAKEN attestation
- [ ] LOA verification workflow
- [ ] CNAM registration
- [ ] Attestation level assignment (A/B/C)

### Call Initiation API
- [ ] `POST /api/v1/brands/{slug}/calls` endpoint
- [ ] Validate brand ownership + number ownership
- [ ] Calculate tier pricing
- [ ] Log call as "initiated"
- [ ] Send to NumHub with RCD payload
- [ ] Return call_id + status

### Webhooks
- [ ] `POST /webhooks/numhub` endpoint
- [ ] Verify webhook signature
- [ ] Handle events:
  - [ ] `enterprise.approved`
  - [ ] `enterprise.rejected`
  - [ ] `call.initiated`
  - [ ] `call.answered`
  - [ ] `call.completed`
  - [ ] `call.failed`
- [ ] Update CallLog status in real-time
- [ ] Webhook retry handling

---

## Phase 3: Usage Tracking & Billing

**Estimated: 1 week**

### Usage Tracking
- [ ] `UsageTrackingService.php`
- [ ] Increment call counts on successful calls
- [ ] Monthly usage aggregation
- [ ] Tier price calculation (volume-based)

### Stripe Integration
- [ ] Create Stripe customers on tenant signup
- [ ] Metered billing subscription setup
- [ ] `SyncUsageToStripe` job (runs hourly)
- [ ] Invoice generation
- [ ] Handle failed payments
- [ ] Webhook handling for payment events

### Billing UI
- [ ] `/billing` page — current usage, tier, projected cost
- [ ] Payment method management
- [ ] Invoice history + downloads
- [ ] Upgrade/downgrade subscription

---

## Phase 4: Analytics Dashboard

**Estimated: 1-2 weeks**

### Metrics Collection
- [ ] Answer rate calculation
- [ ] Call duration statistics
- [ ] Peak hours analysis
- [ ] Geographic distribution
- [ ] Per-brand/per-number breakdown

### Dashboard UI
- [ ] Call volume chart (Chart.js or Recharts)
- [ ] Answer rate trend
- [ ] Top performing brands
- [ ] Recent calls table
- [ ] Real-time call activity (optional)

### Reports
- [ ] Export to CSV/Excel
- [ ] Scheduled email reports (weekly/monthly)
- [ ] Custom date range filtering

---

## Phase 5: Polish & Enterprise Features

**Estimated: 2 weeks**

### Security & Compliance
- [ ] Two-factor authentication (2FA)
- [ ] SSO/SAML integration
- [ ] Comprehensive audit logs
- [ ] API key rotation
- [ ] Rate limiting per API key

### API Documentation
- [ ] Interactive API docs page
- [ ] Code examples (cURL, Node, Python, PHP)
- [ ] Postman collection
- [ ] SDK generation (OpenAPI spec)

### Enterprise Features
- [ ] Custom SLAs
- [ ] White-label support
- [ ] Dedicated support channel
- [ ] Custom integrations

### Landing Page Enhancements
- [ ] Customer testimonials
- [ ] Case studies
- [ ] ROI calculator
- [ ] Live demo scheduling

---

## Backlog (Future Versions)

### v1.1 — Enhanced Calling
- [ ] Batch call initiation API
- [ ] Call scheduling
- [ ] Dynamic call reason per call
- [ ] Call recording integration
- [ ] IVR support

### v1.2 — Integrations
- [ ] Salesforce integration
- [ ] HubSpot integration
- [ ] Zapier connector
- [ ] Make.com connector
- [ ] Custom webhook templates

### v1.3 — Advanced Analytics
- [ ] Campaign management
- [ ] A/B testing for call reasons
- [ ] AI-powered insights
- [ ] Anomaly detection
- [ ] Competitor benchmarking

### v1.4 — SMS/A2P 10DLC
- [ ] A2P 10DLC registration
- [ ] Branded SMS support
- [ ] SMS + Voice unified platform

### v1.5 — Mobile SDKs
- [ ] iOS SDK
- [ ] Android SDK
- [ ] React Native SDK

---

## Provider Evaluation (If NumHub Doesn't Work Out)

Based on NUMHUB-ALTERNATIVES.md analysis:

| Provider | Best For | API Quality | Pricing |
|----------|----------|-------------|---------|
| **Twilio Branded Calling** | Developer-first, quick start | Excellent | Per-call |
| **Numeracle** | Multi-carrier aggregation | Good | Subscription |
| **Plivo** | Cost-effective alternative | Good | Per-call |
| **Sinch** | Global reach | Good | Per-call |

**Recommendation**: If NumHub delays, prototype with Twilio Branded Calling (GA Jan 2025) for fastest time-to-market.

---

## Commands Reference

```bash
# Development
composer dev          # Start all dev servers

# Quality
composer lint         # Fix code style
composer analyse      # Static analysis
composer quality      # Both lint:check + analyse
composer smoke        # Run smoke tests
composer test         # Run all tests

# Database
php artisan migrate:fresh --seed

# Test Users
# admin@brandcall.com (super-admin) — password
# owner@example.com (owner) — password
```

---

## Files to Create (Phase 2)

```
app/
├── Services/
│   └── NumHubService.php           # NumHub API client
├── Jobs/
│   ├── RegisterBrandWithNumHub.php
│   ├── ProcessCallWebhook.php
│   └── SyncUsageToStripe.php
├── Http/Controllers/
│   ├── Api/BrandedCallController.php
│   └── WebhookController.php
└── Events/
    ├── CallInitiated.php
    ├── CallCompleted.php
    └── CallFailed.php

routes/
└── api.php                         # API routes

config/
└── services.php                    # NumHub config
```

---

## Decision Log

| Date | Decision | Rationale |
|------|----------|-----------|
| 2026-01-31 | Spatie Permission for RBAC | Community standard, well-maintained |
| 2026-01-31 | Filament 3 for admin | Modern, rapid development |
| 2026-01-31 | TenantScope pattern | Simple row-level security |
| 2026-01-31 | Per-brand API keys | Granular security, easier rotation |
| 2026-01-31 | NumHub as primary provider | Aggregated carrier access |
| 2026-02-01 | Twilio as backup | If NumHub credentials delayed |

---

## Next Immediate Actions

1. **Get NumHub API credentials** — Contact sales or sign up
2. **If delayed**: Prototype with Twilio Branded Calling
3. **Create NumHubService.php** — Start with mock responses
4. **Build call initiation endpoint** — `/api/v1/brands/{slug}/calls`
5. **Add webhook handling** — For call status updates

---

*This document is the single source of truth for BrandCall development priorities.*
