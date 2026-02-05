# BrandCall.io - Master Plan

> **Last Updated:** 2026-02-05
> **Status:** MVP Phase - Pre-Launch
> **Production URL:** https://brandcall.io

---

## Executive Summary

BrandCall is a Branded Caller ID SaaS platform. We're building MVP to validate the market before significant NumHub API investment.

**Current Reality:**
- ✅ Production infrastructure solid (Hetzner, SSL, Deployer)
- ✅ Core onboarding flow built
- ⏸️ 3 critical dependencies blocking full functionality
- 🎯 Focus: Get to "demo-able MVP" state

---

## Blocking Dependencies

| Dependency | Blocks | Priority | Action Required |
|------------|--------|----------|-----------------|
| **Resend API** | Email verification, notifications | 🔴 HIGH | Jeremy: Add API key to .env |
| **NumHub Credentials** | Voice API, branded calling | 🟡 MEDIUM | Meeting scheduled? Get sandbox access |
| **Stripe Keys** | Payments, subscriptions | 🟢 LOW | Not needed for MVP demo |

**Without Resend:** Users can register but can't verify email → can't complete onboarding
**Without NumHub:** Can demo the platform, but no actual branded calls

---

## Current State Assessment

### ✅ What's Working

| Component | Status | Notes |
|-----------|--------|-------|
| Production Server | ✅ Live | Hetzner CPX21, 178.156.223.166 |
| SSL/HTTPS | ✅ Configured | Let's Encrypt, expires May 2026 |
| Domain | ✅ brandcall.io | DNS pointing correctly |
| Deploys | ✅ Zero-downtime | Deployer, 18 releases |
| Sessions | ✅ Redis | Stable across deploys |
| Database | ✅ MariaDB | Running, seeded |
| Admin Panel | ✅ Filament | /admin accessible |
| Landing Page | ✅ Complete | CMS, 8 layouts, 7 color schemes |
| Registration | ✅ 4-step flow | Name, email, company, password |
| Onboarding Pages | ✅ Built | Dashboard, Profile, Documents, Tickets, Settings, Docs |
| Document Upload | ✅ Functional | Spatie Media Library |
| Support Tickets | ✅ Working | Create, view, reply |
| Dev Dashboards | ✅ Installed | Horizon, Telescope, Pulse, Health (super-admin gated) |
| Tests | ✅ 51 passing | 90 skipped (NumHub) |

### 🔶 Needs Testing/Polish

| Component | Status | Work Needed |
|-----------|--------|-------------|
| Full onboarding flow | 🔶 Untested E2E | Test registration → KYC → approval |
| Admin KYC review | 🔶 Basic | Need approval workflow UI |
| Email templates | 🔶 Created but unsent | Blocked by Resend |
| Error handling | 🔶 Basic | Better user feedback, toasts |
| Loading states | 🔶 Partial | Some forms lack spinners |

### ❌ Not Built Yet

| Component | Priority | Blocked By |
|-----------|----------|------------|
| Email verification flow | HIGH | Resend API |
| Password reset | HIGH | Resend API |
| Admin notification emails | MEDIUM | Resend API |
| Voice API integration | HIGH | NumHub credentials |
| Brand management UI | MEDIUM | NumHub credentials |
| Call analytics | LOW | NumHub credentials |
| Stripe billing | LOW | Not needed for MVP |
| Phone number provisioning | LOW | NumHub credentials |

---

## Phase Plan

### Phase 0: Unblock Email (1 day)
**Goal:** Enable user verification

**Jeremy TODO:**
1. Create Resend account at resend.com
2. Add domain verification (brandcall.io)
3. Get API key
4. Add to production .env:
   ```
   RESEND_KEY=re_xxxxx
   MAIL_FROM_ADDRESS=hello@brandcall.io
   MAIL_FROM_NAME="BrandCall"
   ```
5. Run `composer deploy`

**Donna TODO (after key is added):**
- [ ] Test email verification flow
- [ ] Test password reset
- [ ] Style email templates if needed
- [ ] Add "Resend verification email" button

---

### Phase 1: Demo-Ready MVP (1 week)
**Goal:** Platform that can be demoed to potential customers/investors

#### 1.1 Admin KYC Workflow (2 days)
- [ ] Filament: Document review page with image preview
- [ ] Approve/Reject buttons with notes field
- [ ] User status update on approval
- [ ] Email notification on approval/rejection
- [ ] Dashboard widget: Pending KYC count

#### 1.2 User Experience Polish (1 day)
- [ ] Toast notifications (sonner or similar)
- [ ] Loading spinners on all forms
- [ ] Better error messages
- [ ] Success confirmations
- [ ] Mobile responsive check

#### 1.3 Post-Approval Dashboard (2 days)
- [ ] Mock brand management UI (shows what will be)
- [ ] "Coming soon" sections for:
  - Phone number management
  - Call analytics
  - Usage & billing
- [ ] Profile photo upload

#### 1.4 Content & Polish (1 day)
- [ ] Terms of Service page
- [ ] Privacy Policy page
- [ ] FAQ page
- [ ] Contact/Support page
- [ ] 404/500 error pages

---

### Phase 2: NumHub Integration (2-4 weeks)
**Goal:** Real branded calling functionality

**Prerequisites:**
- NumHub sandbox credentials
- Understanding of their API (meeting done?)
- Test phone numbers

#### 2.1 Foundation (Week 1)
Per `docs/TODO-NUMHUB-INTEGRATION.md`:
- [ ] NumHubClient service class
- [ ] Token management (24h expiry, auto-refresh)
- [ ] Configuration in .env
- [ ] Database schema for entity mapping
- [ ] Sync log for audit trail

#### 2.2 Application Flow (Week 2)
- [ ] Submit BCID application
- [ ] Document upload to NumHub
- [ ] OTP verification
- [ ] Status polling/webhooks

#### 2.3 Display Identity (Week 3)
- [ ] Caller ID management
- [ ] Phone number upload
- [ ] Logo/branding submission
- [ ] Call reason templates

#### 2.4 Live Calling (Week 4)
- [ ] Initiate branded call
- [ ] Delivery confirmation
- [ ] Analytics/reporting
- [ ] Settlement reports

---

### Phase 3: Billing & Scale (2 weeks)
**Goal:** Paying customers

#### 3.1 Stripe Integration
- [ ] Laravel Cashier setup
- [ ] Subscription plans (per LEADS-STRATEGY.md pricing)
- [ ] Billing portal
- [ ] Usage-based add-ons
- [ ] Invoice history

#### 3.2 Multi-Tenant Polish
- [ ] Team member invitations
- [ ] Role management UI
- [ ] Tenant settings
- [ ] API key generation

---

### Phase 4: Growth Features (Ongoing)
- [ ] Blog/content system
- [ ] Referral program
- [ ] White-label for BPOs
- [ ] Advanced analytics
- [ ] API documentation portal
- [ ] Compliance reporting (TCPA)

---

## Technical Debt / Nice-to-Have

| Item | Priority | Notes |
|------|----------|-------|
| Form Request classes | LOW | Currently inline validation |
| Feature tests for onboarding | MEDIUM | Only smoke tests exist |
| TypeScript strict mode | LOW | Some any types |
| API documentation | MEDIUM | Needed for integrations |
| Error tracking (Sentry) | MEDIUM | Currently just logs |
| Automated backups | MEDIUM | Manual only |
| Rate limiting | LOW | Not yet configured |

---

## Metrics to Track

### Pre-Launch
- [ ] E2E test pass rate
- [ ] Page load times
- [ ] Mobile responsiveness score
- [ ] Lighthouse scores

### Post-Launch
- Signups / week
- KYC submission rate
- Approval → Active rate
- Call volume
- Answer rate improvement (the core metric!)

---

## Key Decisions Made

| Date | Decision | Rationale |
|------|----------|-----------|
| 2026-02-05 | Email first, then NumHub | Can't onboard users without email verification |
| 2026-02-02 | Deployer over Envoyer | Free, zero-downtime, works great |
| 2026-02-02 | Redis for sessions | Stable across deploys |
| 2026-01-31 | NumHub over direct carriers | Aggregated access, easier integration |
| 2026-01-31 | Filament for admin | Rapid development, modern UI |

---

## Open Questions

1. **NumHub Status:** Did the meeting happen? Do we have sandbox credentials?
2. **Resend:** Is there a reason we haven't set this up? Any blockers?
3. **MVP Demo:** Who is the target audience? Investors? Early customers?
4. **Timeline:** What's the hard deadline for launch?
5. **Budget:** Any constraints on third-party services?

---

## Quick Reference

### URLs
- **Production:** https://brandcall.io
- **Admin:** https://brandcall.io/admin
- **Health:** https://brandcall.io/health

### Test Users
| Email | Role | Password |
|-------|------|----------|
| admin@brandcall.io | super-admin | password |
| owner@example.com | owner | password |

### Commands
```bash
# Deploy
composer deploy

# Rollback
composer rollback

# SSH to server
ssh root@178.156.223.166

# Run tests
composer test

# Code quality
composer quality
```

### Server
- **IP:** 178.156.223.166
- **Provider:** Hetzner Cloud
- **Path:** /var/www/brandcall/current

---

## Next Action

**Immediate (today):**
1. Jeremy: Set up Resend and add API key to .env
2. Donna: Test email flow once key is added

**This week:**
1. Complete Phase 1 (Demo-Ready MVP)
2. Clarify NumHub status
3. Define demo/launch target date

---

*This plan is a living document. Update as things change.*
