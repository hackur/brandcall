# BrandCall TODO

## Current Status
- **Release**: 13 (production)
- **URL**: https://brandcall.io
- **Sessions**: Redis
- **Deployment**: Deployer (zero-downtime)

---

## High Priority 🔴

### Email Setup (Blocked)
- [ ] Set up Resend for transactional emails
- [ ] Configure email verification flow
- [ ] Password reset emails
- [ ] Admin notification emails

### Onboarding Flow Fixes
- [ ] Test complete registration → onboarding flow
- [ ] Verify document upload works end-to-end
- [ ] Test support ticket creation
- [ ] Add email verification UI (resend button)
- [ ] Style the email verification notice page

### Admin Dashboard
- [ ] Create Filament admin for KYC document review
- [ ] User management (approve/reject users)
- [ ] Support ticket management
- [ ] Document approval workflow

---

## Medium Priority 🟡

### User Experience
- [ ] Add toast notifications for form submissions
- [ ] Improve error handling/display
- [ ] Add loading states to all forms
- [ ] Profile photo upload
- [ ] Password change in settings

### Dashboard (Post-Approval)
- [ ] Design main dashboard for approved users
- [ ] Phone number management UI
- [ ] Caller ID branding configuration
- [ ] Call analytics/reporting
- [ ] Usage & billing display

### Billing (Stripe)
- [ ] Install Laravel Cashier (already done)
- [ ] Create subscription plans
- [ ] Billing portal integration
- [ ] Usage-based billing setup
- [ ] Invoice history

### Voice Provider Integration
- [ ] TwilioDriver implementation (started)
- [ ] NumHub integration (waiting for API credentials)
- [ ] STIR/SHAKEN attestation
- [ ] Rich Call Data (RCD) support
- [ ] Phone number provisioning

---

## Low Priority 🟢

### Marketing & Content
- [ ] Blog system setup
- [ ] SEO optimization
- [ ] Landing page variations (A/B testing)
- [ ] Case studies
- [ ] API documentation

### Infrastructure
- [ ] Set up Horizon for queue monitoring
- [ ] Configure Pulse for app monitoring
- [ ] Automated backups
- [ ] Error tracking (Sentry/Flare)
- [ ] Rate limiting

### Compliance
- [ ] TCPA compliance documentation
- [ ] Terms of Service
- [ ] Privacy Policy
- [ ] DNC (Do Not Call) list integration
- [ ] Consent tracking

---

## Blocked ⏸️

| Task | Waiting On |
|------|------------|
| Email verification | Resend API key |
| Voice features | NumHub/Twilio credentials |
| Payments | Stripe keys |

---

## Recently Completed ✅

- [x] Production deployment (Hetzner Cloud)
- [x] SSL certificate (Let's Encrypt)
- [x] Zero-downtime deploys (Deployer)
- [x] Redis sessions & caching
- [x] Onboarding pages (Dashboard, Profile, Documents, Tickets, Settings, Docs)
- [x] User status flow (pending → verified → approved)
- [x] Document upload system
- [x] Support ticket system
- [x] Font picker & design system
- [x] Landing page CMS
- [x] 4-step registration flow
- [x] Company profile form

---

## Tech Debt

- [ ] Add form request validation classes (instead of inline)
- [ ] Add feature tests for onboarding flow
- [ ] Document API endpoints
- [ ] Add PHPStan static analysis
- [ ] TypeScript strict mode fixes

---

*Last updated: 2026-02-02*
