# NumHub BrandControl API Integration Guide

> **Last Updated:** 2026-02-04
> **Status:** Research Complete - API Credentials Required for Full Implementation

## Quick Links

- **[NUMHUB.md](./NUMHUB.md)** - Comprehensive platform guide, FAQs, and business documentation
- **This file** - Technical API integration patterns and implementation details

## Executive Summary

NumHub provides the **BrandControl Platform**, a cloud-based SaaS solution for Branded Calling ID (BCID) enablement. Their platform streamlines all aspects of BCID adoption including registration, onboarding, vetting, signing, billing, and reporting.

⚠️ **Important:** NumHub's API documentation is not publicly available. This guide documents expected API patterns based on industry standards. Full API documentation requires contacting NumHub sales (844-4-NUMHUB).

---

## Company Information

| | |
|---|---|
| **Company** | NumHub (affiliated with ATL Communications) |
| **Phone** | 844-4-NUMHUB (844-686-482) |
| **Location** | 1375 SE Wilson Ave, Suite 125, Bend, OR 97702 |
| **Website** | https://numhub.com |
| **Twitter** | @numhubdotcom |

---

## What NumHub BrandControl Provides

Based on their platform description, NumHub BrandControl offers:

1. **Customer Registration & Onboarding** - Enterprise client intake
2. **Agent/Reseller Management** - Multi-tenant support
3. **Vetting Services** - Identity and number verification
4. **SHAKEN Signing** - Cryptographic PASSporT generation
5. **Settlement/Billing** - Usage-based billing reconciliation
6. **Reporting** - Analytics and delivery confirmation

---

## BCID Ecosystem Context

NumHub operates within the **Branded Calling ID (BCID)** ecosystem, the industry-led initiative by CTIA. Understanding this ecosystem is critical for integration:

### Ecosystem Participants

| Role | Description |
|------|-------------|
| **Onboarding Agent** | Entry point for enterprises; collects customer data |
| **Vetting Agent** | Validates caller identity, phone numbers, brand assets |
| **Signing Agent** | Generates cryptographic SHAKEN PASSporTs with Rich Call Data |
| **OSP (Originating Service Provider)** | Originates branded calls |
| **TSP (Terminating Service Provider)** | Displays brand info to called party |

### NumHub's Likely Role
Based on their description, NumHub likely functions as:
- ✅ Onboarding Agent
- ✅ Vetting Agent  
- ✅ Signing Agent (or partners with one)
- ✅ Platform/Billing consolidation

---

## Technical Architecture

### STIR/SHAKEN Fundamentals

Branded Calling ID builds on STIR/SHAKEN call authentication:

```
┌─────────────────┐     ┌─────────────────┐     ┌─────────────────┐
│   Enterprise    │     │    NumHub       │     │   Terminating   │
│   (BrandCall)   │────▶│  BrandControl   │────▶│    Carrier      │
│                 │     │                 │     │                 │
│ - Caller Name   │     │ - Vetting       │     │ - Verification  │
│ - Logo          │     │ - SHAKEN Sign   │     │ - Display       │
│ - Call Reason   │     │ - RCD Attach    │     │                 │
└─────────────────┘     └─────────────────┘     └─────────────────┘
```

### SHAKEN PASSporT with Rich Call Data

When a branded call is made, the SHAKEN PASSporT includes:

```json
{
  "attest": "A",
  "crn": "Appointment Reminder",    // Call Reason
  "dest": {
    "tn": ["18005551234"]
  },
  "iat": 1706745600,
  "orig": {
    "tn": "18005559876"
  },
  "origid": "uuid-v4-call-identifier",
  "rcd": {
    "icn": "https://cdn.brandedcallingid.com/images/brand-logo.bmp",
    "nam": "Your Business Name"
  },
  "rcdi": {
    "/icn": "sha256-hash-of-logo-for-integrity"
  }
}
```

### Attestation Levels

| Level | Meaning |
|-------|---------|
| **A** | Full attestation - caller known, has right to use the number |
| **B** | Partial - caller known, right to number unknown |
| **C** | Gateway - minimal information, often international calls |

For branded calling, **Level A attestation is required**.

---

## Expected API Patterns

Based on industry standards and similar platforms (TransNexus, Twilio), expect these patterns:

### Authentication

Likely methods:
- **API Key** - Simple bearer token authentication
- **OAuth 2.0** - For more sophisticated integrations
- **Mutual TLS** - For signing operations

```http
Authorization: Bearer YOUR_API_KEY
```

or

```http
Authorization: Basic base64(api_key:api_secret)
```

### Brand Registration Endpoints

#### Create/Register a Brand

```http
POST /v1/brands
Content-Type: application/json
Authorization: Bearer YOUR_API_KEY

{
  "legal_business_name": "BrandCall Inc.",
  "display_name": "BrandCall",
  "business_type": "corporation",
  "tax_id": "XX-XXXXXXX",
  "website": "https://brandcall.io",
  "address": {
    "street": "123 Main St",
    "city": "San Francisco", 
    "state": "CA",
    "postal_code": "94102",
    "country": "US"
  },
  "contact": {
    "first_name": "John",
    "last_name": "Doe",
    "email": "john@brandcall.io",
    "phone": "+14155551234"
  }
}
```

**Expected Response:**
```json
{
  "brand_id": "brand_xxxxxxxxxxxxxxxx",
  "status": "pending_verification",
  "created_at": "2026-01-31T12:00:00Z",
  "vetting_status": "in_review"
}
```

#### Upload Brand Logo

```http
POST /v1/brands/{brand_id}/logo
Content-Type: image/png

[binary image data]
```

Requirements (per BCID spec):
- Format: BMP preferred, PNG/JPG accepted
- Size: 200x200 pixels recommended
- Max file size: ~50KB

### Phone Number Registration

```http
POST /v1/brands/{brand_id}/numbers
Content-Type: application/json

{
  "phone_numbers": [
    {
      "number": "+14155551234",
      "display_name": "BrandCall Support",
      "default_call_reason": "Customer Service"
    }
  ],
  "verification_method": "ownership_document"
}
```

### Call Initiation / Branding

Two possible integration patterns:

#### Pattern A: Pre-Call API (Out-of-Band)
For platforms where you signal intent before the call:

```http
POST /v1/calls/brand
Content-Type: application/json

{
  "from": "+14155551234",
  "to": "+18005559876",
  "call_reason": "Appointment Reminder",
  "brand_id": "brand_xxxxxxxxxxxxxxxx",
  "ttl_seconds": 30
}
```

#### Pattern B: SIP Integration (In-Band)
For carriers/OSPs using SIP trunking, NumHub provides signing services that attach Rich Call Data to the SHAKEN PASSporT during call setup.

### Get Call Status

```http
GET /v1/calls/{call_id}
```

**Response:**
```json
{
  "call_id": "call_xxxxxxxxxxxxxxxx",
  "from": "+14155551234",
  "to": "+18005559876",
  "status": "delivered",
  "brand_displayed": true,
  "attestation": "A",
  "delivery_confirmed_at": "2026-01-31T12:05:30Z"
}
```

---

## Webhook Events

Based on BCID ecosystem requirements, expect these webhook event types:

### Brand Status Events

```json
{
  "event": "brand.verified",
  "brand_id": "brand_xxxxxxxxxxxxxxxx",
  "timestamp": "2026-01-31T12:00:00Z",
  "data": {
    "vetting_status": "approved",
    "approved_numbers": ["+14155551234"],
    "approved_call_reasons": ["Customer Service", "Appointment Reminder"]
  }
}
```

### Call Delivery Events

```json
{
  "event": "call.brand_displayed",
  "call_id": "call_xxxxxxxxxxxxxxxx",
  "timestamp": "2026-01-31T12:05:30Z",
  "data": {
    "from": "+14155551234",
    "to": "+18005559876",
    "brand_name": "BrandCall",
    "call_reason": "Appointment Reminder",
    "terminating_carrier": "Verizon Wireless",
    "display_confirmed": true
  }
}
```

### Vetting Events

```json
{
  "event": "vetting.update",
  "brand_id": "brand_xxxxxxxxxxxxxxxx",
  "timestamp": "2026-01-31T12:00:00Z",
  "data": {
    "status": "rejected",
    "reason": "Logo contains text that differs from registered business name",
    "field": "logo"
  }
}
```

---

## Pricing Model (Industry Standard)

Based on industry research, expect:

| Cost Component | Typical Range | Notes |
|---------------|---------------|-------|
| **Platform Fee** | $50-500/month per brand | Fixed monthly fee |
| **Per-Call Fee** | $0.02-0.12 per call | Only charged on successful brand display |
| **Vetting Fee** | $50-200/year per enterprise | Annual re-vetting |
| **TSP Delivery Fee** | $0.01-0.03 per call | Paid to terminating carrier |
| **Signing Fee** | $0.0004-0.001 per call | SHAKEN signature generation |

### Volume Discounts
- 1,000+ calls/month: ~$0.06/call
- 10,000+ calls/month: ~$0.03/call
- 100,000+ calls/month: Negotiated

**Key BCID Benefit:** You only pay when the brand is actually displayed to the called party (confirmed delivery).

---

## Rate Limits (Expected)

| Endpoint | Limit |
|----------|-------|
| Brand registration | 10/minute |
| Number registration | 100/minute |
| Call branding requests | 1,000/minute |
| Status queries | 500/minute |

---

## Integration Checklist

### Prerequisites

- [ ] Contact NumHub sales: 844-4-NUMHUB
- [ ] Request API credentials and documentation
- [ ] Complete business verification/KYC
- [ ] Sign BCID ecosystem participant agreement

### Brand Setup

- [ ] Register legal business entity
- [ ] Submit brand assets (logo, display name)
- [ ] Register phone numbers
- [ ] Define call reasons/templates
- [ ] Pass vetting (typically 24-48 hours)

### Technical Integration

- [ ] Configure API authentication
- [ ] Set up webhook endpoint for events
- [ ] Implement call branding flow
- [ ] Handle delivery confirmation callbacks
- [ ] Implement error handling for vetting rejections

### Testing

- [ ] Test in sandbox/staging environment
- [ ] Verify brand display on major carriers
- [ ] Confirm webhook delivery
- [ ] Test attestation levels

### Production

- [ ] Migrate to production credentials
- [ ] Monitor delivery confirmation rates
- [ ] Set up billing reconciliation
- [ ] Implement analytics/reporting

---

## Questions for NumHub Sales

When contacting NumHub, clarify:

1. **API Access**
   - Is there public API documentation?
   - What authentication methods are supported?
   - Is there a sandbox environment?

2. **Integration Model**
   - Do you support REST API for call branding?
   - Is SIP trunk integration required?
   - Can we integrate as an Onboarding Agent's client?

3. **Vetting Process**
   - What documentation is required for business verification?
   - What are the logo/display name requirements?
   - How long does initial vetting take?

4. **Pricing**
   - What's the per-call pricing at our expected volume?
   - Are there minimum commitments?
   - How is TSP delivery fee billed?

5. **Technical**
   - Do you provide SDKs?
   - What webhook events are available?
   - What's your SLA for API availability?

6. **Compliance**
   - What TCPA compliance is required?
   - How are consent records managed?
   - What happens if a brand is flagged?

---

## Alternative Providers

If NumHub doesn't meet requirements, consider:

| Provider | Strengths |
|----------|-----------|
| **TransNexus** | BCID Authorized Signing Agent, ClearIP platform |
| **Twilio** | BCID Authorized Onboarding Agent, Trust Hub integration |
| **First Orion** | INFORM branded calling (proprietary) |
| **Hiya** | Hiya Connect (proprietary + BCID) |

---

## Resources

- [BCID Ecosystem](https://brandedcallingid.com) - Official BCID information
- [CTIA Best Practices PDF](https://api.ctia.org/wp-content/uploads/2022/11/Branded-Calling-Best-Practices.pdf) - Industry standards
- [TransNexus BCID Whitepaper](https://transnexus.com/whitepapers/branded-calling-id/) - Technical deep-dive
- [FCC STIR/SHAKEN](https://www.fcc.gov/call-authentication) - Regulatory background

---

## Appendix: STIR/SHAKEN Verification Statuses

When receiving calls, these statuses indicate authentication:

| Status | Meaning |
|--------|---------|
| `TN-Validation-Passed-A` | Full verification, highest attestation |
| `TN-Validation-Passed-B` | Verified, caller known, number rights unknown |
| `TN-Validation-Passed-C` | Verified, minimal attestation |
| `TN-Validation-Failed-*` | Signature invalid or certificate unavailable |
| `No-TN-Validation` | No PASSporT present or malformed |

---

## Next Steps for BrandCall

1. **Immediate:** Contact NumHub sales (844-4-NUMHUB) to request:
   - API documentation
   - Pricing for your expected call volume
   - Sandbox/test credentials

2. **Short-term:** Evaluate if NumHub is the right fit, or consider alternatives like TransNexus (who provides detailed public documentation).

3. **Parallel track:** Begin business entity verification and brand asset preparation (logo, call reasons) to accelerate onboarding once API access is obtained.
