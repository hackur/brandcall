# BrandCall TODO

## Current Status
- **Release**: 18 (production)
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

**Core Dashboard**
- [ ] Design main dashboard for approved users
- [ ] Phone number management UI
- [ ] Caller ID branding configuration
- [ ] Usage & billing display

**🎯 Analytics & Monitoring (From Competitive Analysis)**
- [ ] **Real-time Number Monitoring Dashboard** - Show spam score, attestation, carrier delivery status per number
- [ ] **Answer Rate Analytics** - Track answer rates by brand/number/campaign (key metric from QVD)
- [ ] **Delivery Confirmation Display** - Surface when branded info was actually displayed (BCID feature)
- [ ] **Flagged Number Alerts** - Automated notifications when numbers get flagged as spam
- [ ] **Automated Number Replacement** - Suggest/auto-provision clean numbers when spam-flagged
- [ ] Call analytics/reporting (basic)
- [ ] Reputation score tracking per number
- [ ] A/B testing dashboard (which brand/number performs better)

**📞 Number Management Enhancements**
- [ ] Google Verify integration (free - competitors charge for this)
- [ ] Bulk number provisioning
- [ ] Number reputation history
- [ ] Suggested number rotation based on performance

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

### Marketing & Content (UPDATED 2026-02-07 - Based on Competitive Analysis)

**🎯 HIGH PRIORITY - Content Gaps (Huge SEO Opportunity)**
- [ ] **FAQ Page** - Neither competitor has one! Target "what is branded caller ID", "how does branded calling work"
- [ ] **"What is Branded Caller ID?" Explainer** - Non-technical, customer-facing guide
- [ ] **"Is Your Number Showing as Spam Likely?" Landing Page** - High-intent keyword, conversion-focused
- [ ] **STIR/SHAKEN Explained** - Plain-language guide for non-technical audiences
- [ ] **Publish INDUSTRY-TERMS.md Publicly** - Convert existing doc to web content (SEO gold)
- [ ] **Publish USER-GUIDE.md Publicly** - Getting started guide (competitors lack this)

**🚀 Quick Win Messaging Updates**
- [ ] Add "Minutes, not weeks" messaging (vs BCID's partner-mediated process)
- [ ] Add "No partners needed - direct API access" positioning
- [ ] Add "95% of consumers ignore unknown numbers" stat to homepage hero
- [ ] Add "48%+ answer rate improvement" stat (from QVD research)
- [ ] Create visual comparison: Our 3-step process vs BCID's 7-step partner journey

**📊 Educational Content Hub**
- [ ] Blog system setup
- [ ] Article: "Why Branded Caller ID Matters" (customer education)
- [ ] Article: "How Attestation Levels Affect Call Delivery"
- [ ] Article: "TCPA Compliance for Call Centers"
- [ ] Video: "BrandCall Setup in 3 Minutes" (product demo)
- [ ] Interactive: "Will Your Calls Get Blocked?" calculator/tool

**🎨 Landing Pages (Industry-Specific)**
- [ ] Landing page: Call Centers & BPOs
- [ ] Landing page: Sales Teams & Outbound
- [ ] Landing page: Customer Support
- [ ] Landing page: Healthcare Providers
- [ ] Landing page: Financial Services

**💡 Customer Success**
- [ ] Case studies (3-5 with metrics)
- [ ] Testimonials collection system
- [ ] "Success Stories" section
- [ ] ROI calculator ("Answer rate improvement" → revenue impact)

**🔍 SEO Foundation**
- [ ] SEO optimization (title tags, meta descriptions, structured data)
- [ ] Sitemap generation
- [ ] Schema.org markup for SaaS/product
- [ ] Landing page variations (A/B testing)
- [ ] Backlink strategy (industry directories)

**📚 Developer Resources**
- [ ] API documentation (public)
- [ ] SDKs/code examples (PHP, Node, Python)
- [ ] Webhook documentation
- [ ] Integration guides (Twilio, Five9, etc.)
- [ ] Postman collection

### Infrastructure
- [x] Set up Horizon for queue monitoring (role-gated)
- [x] Configure Pulse for app monitoring (role-gated)
- [x] Telescope for debugging (role-gated)
- [x] Health checks (Spatie Health, role-gated)
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

## Competitive Positioning & Differentiation 🏆

**Based on competitive analysis (BCID.com + Quality Voice & Data):**

**Our Advantages (Market Now)**
- [x] Self-service signup (competitors: weeks-long partner processes)
- [x] Direct REST API (BCID: no API, partner-mediated)
- [x] Transparent pricing (competitors: opaque, sales-heavy)
- [x] Modern tech stack (Laravel 12 + React vs legacy systems)
- [ ] **Educational content dominance** - Fill the massive content gap (FAQ, guides, explainers)
- [ ] **Developer-first experience** - SDKs, webhooks, clear docs

**Key Messaging Pillars**
- [ ] "Minutes, not weeks" - Speed to first branded call
- [ ] "No partners needed" - Direct access vs gatekept ecosystem
- [ ] "95% of consumers ignore unknown numbers" - The problem we solve
- [ ] "48%+ answer rate improvement" - The outcome we deliver

**Product Differentiators to Build**
- [ ] Real-time spam monitoring (they don't have this)
- [ ] Automated number replacement (manual at competitors)
- [ ] Self-service everything (vs sales-driven)
- [ ] Transparent delivery confirmation in dashboard

**Content Marketing Opportunities**
- [ ] Own "what is branded caller ID" (nobody has good content)
- [ ] Own "STIR/SHAKEN explained" (technical → accessible)
- [ ] Own "spam likely fix" keyword space
- [ ] Industry-specific guides (healthcare, finance, call centers)

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
- [x] Dev dashboards (Horizon, Telescope, Pulse, Health) with super-admin gates
- [x] Filament navigation links to dev tools (super-admin only)

---

## Tech Debt

- [ ] Add form request validation classes (instead of inline)
- [ ] Add feature tests for onboarding flow
- [ ] Document API endpoints
- [ ] Add PHPStan static analysis
- [ ] TypeScript strict mode fixes

---

*Last updated: 2026-02-07 (Major content strategy update based on competitive analysis)*
