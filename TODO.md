# BrandCall TODO

## Priority 1: KYC & Compliance

### Document Requirements
- [x] Business License upload
- [x] Tax ID / EIN document
- [x] Letter of Authorization (LOA)
- [ ] **Driver's License / Government ID** (for authorized signatory)
- [ ] Articles of Incorporation / Business Registration
- [ ] Utility bill or bank statement (address verification)
- [ ] W-9 form (for US businesses)

### KYC Process
- [x] Document upload system
- [x] Document status tracking (pending/approved/rejected)
- [ ] Admin review interface in Filament
- [ ] Document expiry tracking & renewal reminders
- [ ] Audit log for all KYC actions
- [ ] KYC rejection reasons & resubmission flow
- [ ] Automated document verification (future: OCR/AI)

---

## Priority 2: Core Branding API

### Call Flow
```
Client Request → BrandCall API → Supplier/Driver → Branded Call
     ↓                                    ↓
  Bill +$0.03                        Cost -$0.01
     ↓                                    ↓
  Stripe Usage                      Track in DB
```

### API Endpoints
- [ ] `POST /api/v1/calls/brand` - Request branded call
- [ ] `GET /api/v1/calls/{id}` - Get call status
- [ ] `GET /api/v1/calls` - List calls (paginated)
- [ ] `POST /api/v1/brands/{id}/verify` - Verify brand/number
- [ ] Webhook endpoints for supplier callbacks

### Supplier Integration
- [ ] Voice provider interface (TwilioDriver exists)
- [ ] NumHub integration (waiting on API credentials)
- [ ] Telnyx integration
- [ ] Cost tracking per call (-$0.01 per branded call)
- [ ] Supplier health monitoring
- [ ] Failover between suppliers

### Rate Limiting & Quotas
- [ ] Per-tenant rate limits
- [ ] Monthly call quotas by plan
- [ ] Overage handling

---

## Priority 3: Stripe Billing

### Usage-Based Billing
- [ ] Stripe Usage Records API integration
- [ ] Metered billing for calls (+$0.03 per branded call)
- [ ] Usage aggregation (hourly/daily)
- [ ] Invoice line items for call usage

### Subscription Plans
- [ ] Free tier (100 calls/month)
- [ ] Starter ($49/mo - 1,000 calls)
- [ ] Growth ($199/mo - 10,000 calls)
- [ ] Enterprise (custom pricing)
- [ ] Plan upgrade/downgrade flow

### Billing Dashboard
- [ ] Current usage display
- [ ] Billing history
- [ ] Payment method management
- [ ] Invoice downloads
- [ ] Usage alerts (80%, 100% of quota)

---

## Priority 4: Dashboard & Analytics

### Call Tracking
- [ ] Real-time call log
- [ ] Call status (pending, completed, failed)
- [ ] Cost/revenue per call
- [ ] Daily/weekly/monthly summaries
- [ ] Export to CSV/Excel

### Analytics
- [ ] Answer rate by brand
- [ ] Answer rate by time of day
- [ ] Geographic breakdown
- [ ] Carrier-specific metrics
- [ ] A/B testing for call reasons

### Brand Management
- [x] Create brand
- [x] Logo upload
- [ ] Multiple phone numbers per brand
- [ ] Call reason templates
- [ ] Brand performance metrics

---

## Priority 5: Admin Panel (Filament)

### User Management
- [ ] View all users
- [ ] User status (pending/verified/approved/suspended)
- [ ] Manual KYC approval workflow
- [ ] Impersonation for support

### Tenant Management
- [ ] View all tenants
- [ ] Tenant billing status
- [ ] Usage overview
- [ ] Manual adjustments

### KYC Review
- [ ] Document review queue
- [ ] Approve/reject with notes
- [ ] Request additional documents
- [ ] Audit trail

### System Health
- [ ] Supplier status
- [ ] API error rates
- [ ] Queue health (Horizon)
- [ ] Database metrics (Pulse)

---

## Priority 6: Email & Notifications

### Transactional Emails
- [x] Email verification
- [ ] Welcome email after approval
- [ ] KYC status updates
- [ ] Password reset
- [ ] Usage alerts
- [ ] Invoice notifications

### Email Provider
- [ ] Resend setup (DNS records needed)
- [ ] Email templates
- [ ] Unsubscribe handling

---

## Priority 7: Security & Compliance

### API Security
- [ ] API key management
- [ ] Request signing
- [ ] IP allowlisting option
- [ ] Webhook signature verification

### Compliance
- [ ] STIR/SHAKEN attestation tracking
- [ ] TCPA compliance logging
- [ ] Call consent tracking
- [ ] Data retention policies
- [ ] GDPR/CCPA handling

### Audit
- [x] Activity logging (Spatie)
- [ ] Admin action audit trail
- [ ] API request logging
- [ ] Compliance reports

---

## Priority 8: Infrastructure

### Deployment
- [x] Deployer zero-downtime deploys
- [x] GitHub Actions CI/CD
- [x] Redis installed
- [ ] Horizon supervisor setup
- [ ] Queue workers for async jobs
- [ ] Backup automation (Spatie Backup)

### Monitoring
- [x] Pulse dashboard
- [x] Telescope for debugging
- [ ] Health checks for dependencies
- [ ] Uptime monitoring
- [ ] Error alerting (Sentry?)

### Performance
- [ ] Response caching
- [ ] Database query optimization
- [ ] CDN for assets
- [ ] Load testing

---

## Blocked / Waiting

| Item | Waiting On |
|------|------------|
| NumHub integration | API credentials |
| Resend email setup | DNS records from Jeremy |
| Stripe billing | Stripe API keys in .env |
| Voice provider testing | NumHub/Twilio credentials |

---

## Completed ✅

- [x] Landing page with hero carousel
- [x] Multi-step registration flow
- [x] Email normalization (mixed-case accepted)
- [x] Font picker system (7 combos)
- [x] Deployer CI/CD setup
- [x] Onboarding dashboard
- [x] Company profile form
- [x] Document upload system
- [x] Support ticket system
- [x] Documentation page
- [x] Account settings
- [x] Redis installed on server
- [x] TwilioDriver created
- [x] Laravel Cashier installed
- [x] Design system (Tailwind)

---

## Notes

### Pricing Model
- **Cost per call:** $0.01 (paid to supplier)
- **Revenue per call:** $0.03 (billed to client)
- **Margin:** $0.02 per call (66% gross margin)

### Stripe Usage API
```php
// Record usage after each branded call
$subscription->reportUsage(
    quantity: 1,
    timestamp: now(),
);

// Or use Stripe's Meter API for real-time
Stripe\Billing\MeterEvent::create([
    'event_name' => 'branded_call',
    'payload' => [
        'stripe_customer_id' => $tenant->stripe_id,
        'value' => 1,
    ],
]);
```

### Call Flow Sequence
1. Client sends POST /api/v1/calls/brand
2. Validate API key & rate limits
3. Check tenant has active subscription
4. Queue job: SendBrandedCallJob
5. Job calls supplier API (NumHub/Twilio)
6. Supplier returns call ID
7. Record cost in call_logs
8. Report usage to Stripe
9. Return call ID to client
10. Webhook receives call completion
11. Update call status in DB
