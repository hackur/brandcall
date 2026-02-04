# NumHub BrandControl Platform Guide

> **BrandCall's branded calling infrastructure is powered by NumHub's BrandControl platform.**

---

## About NumHub

NumHub is the industry leader in phone number ownership and branded calling enablement. With over 30 years of trusted partnership in telecommunications, NumHub serves thousands of businesses, telecom providers, and organizations globally.

**Company Information:**
| | |
|---|---|
| **Legal Entity** | Porting.com LLC dba NumHub |
| **Phone** | 844-4-NUMHUB (844-686-4822) |
| **Address** | 1375 SE Wilson Ave., Suite 125, Bend, OR 97702 |
| **Website** | https://numhub.com |

---

## What is BrandControl?

BrandControl is NumHub's API-driven, cloud-based SaaS platform for Branded Calling ID (BCID) enablement. It streamlines every aspect of branded calling adoption:

- **Customer Registration & Onboarding** - Enterprise client intake with automated verification
- **Agent/Reseller Management** - Multi-tenant support for white-label offerings
- **Vetting Services** - Identity and phone number verification
- **SHAKEN Signing** - Cryptographic call authentication
- **Settlement/Billing** - Usage-based billing and reconciliation
- **Reporting & Analytics** - Delivery confirmation and performance metrics

### Who Uses BrandControl?

**Telecom Service Providers:**
- Launch branded calling services with minimal resources
- Maximize margins while owning customer relationships
- Go-to-market quickly and begin generating revenue
- Maintain full control over pricing and branding

**Enterprise Businesses:**
- Own your brand identity with streamlined onboarding
- Get connected with authorized OSPs for call delivery
- Pay only for successfully delivered branded calls
- Manage brand assets through a flexible portal

---

## How Branded Calling Works

### The Problem

- 80% of unidentified calls go unanswered (Hiya 2025 research)
- Consumers can't distinguish legitimate business calls from spam
- Answer rates for unknown numbers hover around 20%
- Businesses waste money on outbound calls that don't connect

### The Solution

Branded calling displays verified business information directly on the recipient's smartphone screen:

1. **Business Name** - Your verified company name
2. **Logo** - Your brand logo (Rich Call Data)
3. **Call Reason** - Why you're calling (e.g., "Appointment Reminder")

When consumers see a trusted, verified brand on their phone, answer rates increase by up to 70%.

### The Call Flow

```
┌─────────────────┐     ┌─────────────────┐     ┌─────────────────┐
│   Your App      │     │    NumHub       │     │   Consumer's    │
│   (BrandCall)   │────▶│  BrandControl   │────▶│     Phone       │
│                 │     │                 │     │                 │
│ - Brand Name    │     │ - Vetting       │     │ Shows:          │
│ - Logo          │     │ - SHAKEN Sign   │     │ - Logo          │
│ - Call Reason   │     │ - RCD Attach    │     │ - Business Name │
│ - Phone Number  │     │                 │     │ - Call Reason   │
└─────────────────┘     └─────────────────┘     └─────────────────┘
```

---

## STIR/SHAKEN Framework

### What is STIR/SHAKEN?

STIR/SHAKEN is an FCC-mandated framework to combat caller ID spoofing and enhance voice communication security. Think of it like an SSL certificate for phone calls.

- **STIR**: Secure Telephone Identity Revisited
- **SHAKEN**: Signature-based Handling of Asserted information using toKENs

### How It Works

1. **Call Origination** - The originating provider verifies the caller's identity and assigns an attestation level
2. **Identity Header** - A digital signature (PASSporT) is created and attached to the call
3. **Call Transmission** - The call travels through networks with the cryptographic signature
4. **Call Termination** - The receiving provider verifies the signature and displays brand info if valid

### Attestation Levels

| Level | Name | Meaning |
|-------|------|---------|
| **A** | Full Attestation | Provider knows the customer AND verifies their right to use the number |
| **B** | Partial Attestation | Provider knows the customer but cannot verify number rights |
| **C** | Gateway Attestation | Minimal info, typically international calls or unauthenticated sources |

**For branded calling, Level A attestation is required.**

### Benefits of STIR/SHAKEN

- **Authenticity** - Caller ID information is legitimate and trustworthy
- **Spoofing Reduction** - Malicious caller ID manipulation is detected
- **Consumer Trust** - Recipients can trust the calls they receive

---

## Why NumHub Stands Out

### Industry-Led Standards

Unlike proprietary branded calling solutions, NumHub leverages the FCC-mandated STIR/SHAKEN framework. This is the industry-adopted, CTIA-governed solution that represents the future of trusted voice communication.

### Key Differentiators

| Feature | NumHub BrandControl | Legacy Solutions |
|---------|---------------------|------------------|
| **Framework** | STIR/SHAKEN (FCC-mandated) | Proprietary apps/networks |
| **Governance** | CTIA industry standard | Vendor-specific |
| **Brand Display** | No app required | Often requires app downloads |
| **Scalability** | Nationwide/global | Regional limitations |
| **Security** | End-to-end authentication | Varies by vendor |
| **Control** | Full pricing/margin control | Vendor restrictions |

### Four Pillars of BrandControl

1. **Integrates STIR/SHAKEN** - Cryptographic authentication combined with branding verification for end-to-end security

2. **Eliminates Vendor Monopolies** - Creates an open ecosystem where enterprises own their branding and OSPs control their business models

3. **Standardizes Branding** - Ensures consistent display of business names, logos, and call reasons across all major carriers

4. **Provides an Open Marketplace** - Allows businesses to choose their provider, reducing costs and improving service flexibility

---

## Branded Calling ID (BCID) Ecosystem

### What is BCID?

Branded Calling ID (BCID) is an industry-adopted, CTIA-governed framework that enables trusted, secure, and authenticated branded calls for businesses.

### Current Market Status

- Major carriers adopting: T-Mobile (live), Verizon (launching), AT&T (expected)
- Only ~5,000 U.S. businesses currently use branded calling
- Over 32 million legitimate businesses remain without access
- NumHub's mission: Scale from 5,000 to 33 million businesses

### Ecosystem Participants

| Role | Description |
|------|-------------|
| **Onboarding Agent** | Entry point for enterprises; collects customer data |
| **Vetting Agent** | Validates caller identity, phone numbers, brand assets |
| **Signing Agent** | Generates cryptographic SHAKEN PASSporTs with Rich Call Data |
| **OSP** | Originating Service Provider - initiates branded calls |
| **TSP** | Terminating Service Provider - displays brand info to recipient |

NumHub serves as an **Onboarding Agent** and **Vetting Agent**, providing the platform infrastructure for the entire workflow.

---

## Frequently Asked Questions

### What is branded calling?

Branded calling allows businesses to display their familiar brand to customers when making outbound calls. A branded call delivers verified and authenticated branding directly to the recipient's smartphone screen, including:
- Caller's verified business name
- Company logo (Rich Call Data)
- Call reason (e.g., "Appointment Reminder")

### What is the difference between Caller ID and Branded Calling ID?

**Traditional Caller ID:**
- Shows phone number only
- May show CNAM (15-32 character name) if available
- No verification of caller identity
- Easily spoofed

**Branded Calling ID:**
- Shows verified business name
- Displays company logo
- Includes call reason
- Cryptographically authenticated via STIR/SHAKEN
- Cannot be spoofed

### What are the benefits of branded calling?

**For Businesses:**
- Increase call answer rates by up to 70%
- Reduce wasted outbound calling spend
- Build customer trust and recognition
- Prevent your numbers from being labeled as spam
- Comply with telecommunications regulations

**For Consumers:**
- Know who's calling before answering
- Distinguish legitimate calls from spam
- See why the business is calling
- Trust verified caller identity

### How is BCID different from other branded calling solutions?

1. **Industry-Led** - BCID leverages FCC-mandated STIR/SHAKEN standards, making it the solution of the future

2. **Secure** - Governed by CTIA with an ecosystem of trusted and authorized partners

3. **Accessible** - Available for businesses of all sizes with charges only on delivered calls

4. **Scalable** - Requires no additional apps or software on consumer devices

### Do consumers need to download an app?

**No.** Unlike proprietary solutions (First Orion, Hiya apps), BCID-based branded calling works natively on smartphones without any app downloads. The brand information is delivered through the STIR/SHAKEN call path and displayed by the carrier's native phone app.

### What carriers support branded calling?

Major U.S. carriers are actively adopting BCID:
- **T-Mobile** - Fully live
- **Verizon** - Signed contract, launching soon
- **AT&T** - Expected to follow

Coverage continues to expand as regulatory pressure and industry adoption accelerate.

### What does branded calling cost?

Pricing is typically based on:
- Monthly platform fee per brand
- Per-call fee for successfully delivered branded calls
- Annual vetting/verification fees

You only pay when the brand is actually displayed to the called party (confirmed delivery).

---

## Industries That Benefit Most

### Healthcare
- Appointment reminders and confirmations
- Test result notifications
- Telehealth follow-ups
- Prescription refill reminders
- Provider office callbacks

### Insurance
- Open enrollment notifications
- Policy renewal reminders
- Claims status updates
- Agent callbacks
- Verification calls

### Financial Services
- Fraud alerts
- Account notifications
- Collections (compliant)
- Customer service callbacks
- Transaction verifications

### Contact Centers & BPOs
- White-label branded calling for clients
- Multi-brand management
- Campaign-specific branding
- Unified reporting across brands

### Home Services
- Appointment confirmations
- Technician ETA notifications
- Service completion follow-ups
- Scheduling changes

---

## Getting Started with BrandControl

### Enterprise Onboarding Flow

1. **Registration** - Create account and verify email
2. **Company Profile** - Complete business information (industry, call volume, use case)
3. **Brand Assets** - Upload logo, define display name, configure call reasons
4. **KYC Documents** - Submit business verification documents
5. **Number Registration** - Register phone numbers with proof of ownership (LOA)
6. **Vetting Review** - NumHub reviews and verifies all information (24-48 hours)
7. **Approval** - Begin making branded calls

### Required Documents

| Document Type | Purpose |
|---------------|---------|
| Business License | Verify business registration |
| Tax ID (EIN/W-9) | Verify tax status |
| Government ID | Verify authorized representative |
| Letter of Authorization (LOA) | Prove phone number ownership |
| Articles of Incorporation | Verify legal entity (if applicable) |

### Logo Requirements

- **Format**: BMP preferred, PNG/JPG accepted
- **Size**: 200x200 pixels recommended
- **File Size**: Under 50KB
- **Content**: Must match registered business name

---

## Technical Integration

### API-Driven Architecture

BrandControl provides REST APIs for:
- Brand registration and management
- Phone number registration
- Call branding requests
- Status and delivery confirmation
- Webhook event notifications

### Webhook Events

BrandControl can notify your system when:
- Brand verification status changes
- Vetting is approved/rejected
- Calls are successfully branded
- Delivery is confirmed
- Numbers are flagged

### Integration Patterns

**Pattern A: Pre-Call API (Out-of-Band)**
Signal your intent to brand a call before initiating it. Useful for platforms that can make an API call before the voice call.

**Pattern B: SIP Integration (In-Band)**
For carriers and OSPs using SIP trunking, NumHub provides signing services that attach Rich Call Data during call setup.

---

## Compliance & Security

### Regulatory Compliance

- **FCC STIR/SHAKEN** - Fully compliant with call authentication mandates
- **CTIA BCID Standards** - Follows industry governance guidelines
- **TCPA** - Supports consent tracking and compliance
- **KYC/AML** - Robust identity verification processes

### Security Features

- End-to-end call authentication
- Cryptographic signing of call metadata
- Verified caller identity before display
- Real-time fraud monitoring
- Traceback capabilities for suspicious calls

---

## Support & Resources

### Contact NumHub

- **Phone**: 844-4-NUMHUB (844-686-4822)
- **Web**: https://numhub.com/contact
- **Email**: Contact via website form

### Learning Resources

NumHub maintains a comprehensive Learning Center with articles on:
- STIR/SHAKEN implementation
- BCID industry updates
- Regulatory changes
- Best practices for branded calling
- Case studies and success stories

Visit: https://numhub.com/learning-center

---

## Glossary

| Term | Definition |
|------|------------|
| **BCID** | Branded Calling ID - Industry-governed ecosystem for branded calls |
| **BrandControl** | NumHub's BCID enablement platform |
| **CNAM** | Caller Name - Traditional 15-32 character display name |
| **LOA** | Letter of Authorization - Proof of phone number ownership |
| **OSP** | Originating Service Provider - Initiates calls |
| **PASSporT** | Personal Assertion Token - Cryptographic call signature |
| **RCD** | Rich Call Data - Logo, name, call reason bundle |
| **RespOrg** | Responsible Organization for toll-free numbers |
| **SHAKEN** | Signature-based Handling of Asserted information using toKENs |
| **STIR** | Secure Telephone Identity Revisited |
| **TSP** | Terminating Service Provider - Receives calls |
| **VPU** | Voice Pick Up - Call answer rate metric |

---

*Last Updated: 2026-02-04*
