# Branded Caller ID Industry Deep Dive

> **Comprehensive Market Research Report**  
> BrandCall Strategic Intelligence  
> February 2026

---

## Executive Summary

The branded caller ID industry represents a fascinating evolution from the simple caller ID technology invented in 1968 to a multi-billion dollar ecosystem fighting robocall fraud while enabling legitimate businesses to connect with customers. What started as a 15-character landline feature has become a sophisticated authentication and branding platform essential for any business making outbound calls.

**Key Findings:**
- **80% of unidentified calls go unanswered** (Hiya 2025)
- **$41.82 billion** lost to telecommunications fraud globally in 2025
- **STIR/SHAKEN** mandated for US carriers since June 2021
- Market dominated by 4 major players: Hiya, First Orion, TNS, Numeracle
- Significant SMB market gap remains underserved
- AI integration is the next frontier (deepfake detection, voice agents)

---

## Table of Contents

1. [How It Works](#how-it-works)
2. [Why It Exists](#why-it-exists)
3. [Market History & Evolution](#market-history--evolution)
4. [Pricing Structures](#pricing-structures)
5. [Business Models](#business-models)
6. [Marketing Strategies](#marketing-strategies)
7. [Public Perception](#public-perception)
8. [Modern vs Legacy Approaches](#modern-vs-legacy-approaches)
9. [Market Size & Players](#market-size--players)
10. [Future Outlook](#future-outlook)

---

## How It Works

### The Technical Stack

Branded caller ID operates across multiple layers of telecommunications infrastructure:

```
┌─────────────────────────────────────────────────────────────┐
│                     THE BRANDED CALL FLOW                   │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│   ORIGINATION          NETWORK              TERMINATION     │
│                                                             │
│   ┌─────────┐       ┌──────────┐       ┌─────────────────┐ │
│   │ Business│──────▶│ Carrier/ │──────▶│ Recipient Phone │ │
│   │ Dialer  │       │ Provider │       │ (Mobile/PSTN)   │ │
│   └────┬────┘       └────┬─────┘       └────────┬────────┘ │
│        │                 │                      │          │
│   1. Call Init      2. STIR/SHAKEN        4. Display      │
│   + Brand Data         Signing           Brand Info       │
│                                                            │
│        │                 │                      │          │
│   ┌────▼────┐       ┌────▼─────┐       ┌───────▼────────┐ │
│   │ Include │       │Cryptograph│       │ Show to User:  │ │
│   │ Name    │       │ Attestation│      │ - Business Name│ │
│   │ Logo    │       │ A/B/C Level│      │ - Logo         │ │
│   │ Reason  │       │ + RCD Data │      │ - Call Reason  │ │
│   └─────────┘       └──────────┘       └────────────────┘ │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

### Core Technologies

#### 1. STIR/SHAKEN (The Authentication Layer)

**STIR** (Secure Telephone Identity Revisited) and **SHAKEN** (Signature-based Handling of Asserted information using toKENs) form the regulatory foundation.

**How it works:**
1. When a call originates, the carrier examines the caller ID
2. The carrier adds a cryptographic signature (JSON Web Token) to the SIP header
3. This signature includes an "attestation level" indicating trust:
   - **A (Full)**: Carrier knows the customer AND the number belongs to them
   - **B (Partial)**: Carrier knows the customer but can't verify that specific number
   - **C (Gateway)**: Call came from a gateway; origin unknown
4. Receiving carrier verifies the signature using public key cryptography
5. Failed or missing authentication can trigger spam labels

**The name?** Inspired by James Bond's "shaken, not stirred" martini preference. The creators "tortured the English language" to create the SHAKEN acronym.

#### 2. Rich Call Data (RCD)

RCD extends STIR/SHAKEN to carry actual branding information:
- Business name (up to 32 characters)
- Logo image
- Call reason/purpose
- Verified business identity

This is what makes a call go from "Unknown Caller" to "ABC Insurance - Policy Renewal."

#### 3. CNAM (Caller Name Delivery)

The legacy system still in use for landlines:
- Limited to **15 characters**
- Text only, no logos
- Relies on distributed databases (often outdated)
- Not universal—different carriers have different databases

### The Display Experience

When branded calling works, the recipient sees:

**Without Branding:**
```
┌──────────────────────┐
│   INCOMING CALL      │
│                      │
│   (555) 123-4567     │
│                      │
│   [Answer] [Decline] │
└──────────────────────┘
```

**With Branding:**
```
┌──────────────────────┐
│   INCOMING CALL      │
│                      │
│   🏥 [Logo]          │
│   ABC Medical Group  │
│   "Appointment       │
│    Reminder"         │
│                      │
│   ✓ Verified Caller  │
│                      │
│   [Answer] [Decline] │
└──────────────────────┘
```

---

## Why It Exists

### The Robocall Crisis

The industry exists because voice calling became nearly unusable:

| Year | Robocalls (US) | % Increase |
|------|----------------|------------|
| 2017 | 30.5 billion | — |
| 2018 | 47.8 billion | +57% |
| 2019 | 58.5 billion | +22% |
| 2020 | 45.9 billion | -22% (COVID) |
| 2021 | 50.5 billion | +10% |
| 2022 | 50.3 billion | flat |
| 2023 | 55.0 billion | +9% |
| 2024 | 58.0 billion | +5% |

**The result:** People stopped answering the phone.

### Consumer Behavior Shift

**Key statistics:**
- **87%** of consumers won't answer calls from unknown numbers
- **80%** of unidentified calls go completely unanswered
- **48%** of consumers NEVER answer unknown calls, even if expecting one
- **71%** of millennials have let important calls go to voicemail because they didn't recognize the number

### The Business Problem

For legitimate businesses, this created a crisis:
- **Healthcare:** Patients missing appointment reminders, test results
- **Financial Services:** Fraud alerts going unanswered
- **Insurance:** Claims follow-ups ignored
- **Retail:** Delivery coordination failing
- **Collections:** (Ironic) Legitimate debt collection impossible

**The cost:** Businesses estimate **$20-30 per unanswered call** in lost productivity, rescheduling, and missed opportunities.

### Fraud Economics

Why robocallers persist:

```
Cost per robocall:     $0.001 - $0.01
Success rate needed:   0.1%
Average scam value:    $500 - $10,000

ROI = (0.001 × $1,000) - $0.01 = $0.99 per call

At 1 million calls/day = $990,000 daily profit potential
```

This economics made stopping robocalls a regulatory imperative.

---

## Market History & Evolution

### Timeline

#### **1968-1987: Caller ID Invention**
- Ted Paraskevakos patents caller ID concept
- Uses Bell 202 modem signals between rings
- Bell Atlantic trials in 1984 and 1987

#### **1990s: CNAM Era**
- Caller ID becomes widespread in US/Canada
- CNAM databases established for name display
- Limited to 15 characters, landlines only
- Telemarketers begin "spoofing" numbers

#### **2000s: VoIP Revolution**
- Voice over IP makes calling nearly free
- SIP protocol allows easy caller ID manipulation
- Robocall volume begins exponential growth
- First "neighbor spoofing" attacks appear

#### **2009: TRACED Act Precursors**
- FTC gains authority to sue for false caller ID
- Industry begins self-regulation efforts
- First carrier-level spam blocking apps emerge

#### **2014-2017: STIR Development**
- IETF creates STIR working group
- First RFC documents published
- SHAKEN framework developed by ATIS/SIP Forum
- Carrier pilots begin

#### **2019: Regulatory Push**
- TRACED Act signed (Telephone Robocall Abuse Criminal Enforcement and Deterrence)
- FCC mandates STIR/SHAKEN implementation
- First cross-border authenticated call (US-Canada) in December

#### **2021: Mandate Takes Effect**
- June 30: Large US carriers must implement STIR/SHAKEN
- Smaller carriers given extensions
- Branded calling market begins rapid growth

#### **2022-2024: Market Maturation**
- Hiya, First Orion, TNS become dominant
- Answer rates become key marketing metric
- AI/ML integration accelerates
- International expansion begins

#### **2025-2026: AI Era**
- Deepfake voice detection becomes critical
- First Orion expands to Germany (Deutsche Telekom)
- AI voice agents emerge (Hiya Sales Assistant)
- Market consolidation continues

### The Age Question

**How old is the market?**
- Caller ID concept: **58 years** (1968)
- CNAM standard: **~35 years** (early 1990s)
- Modern branded calling (STIR/SHAKEN-based): **~5 years** (2021 mandate)
- Commercial branded calling products: **~8-10 years** (First Orion founded 2008)

**Conclusion:** This is a mature concept with a relatively young modern market. The technology foundation is decades old, but the current commercial ecosystem emerged only in the last 5-8 years.

---

## Pricing Structures

### Pricing Models in Use

#### 1. **Per-Call Pricing**
Most common for enterprise:
```
Base: $0.02 - $0.08 per branded call
Volume discounts:
  - 100K calls/mo: $0.05/call
  - 500K calls/mo: $0.035/call
  - 1M+ calls/mo:  $0.02/call (or negotiated)
```

#### 2. **Subscription + Usage**
Typical structure:
```
Monthly Platform Fee:  $500 - $2,000
Includes:              First 10,000 calls
Overage:               $0.03 - $0.05/call
```

#### 3. **Tiered Bundles**
Example (Hiya model):
```
Starter:    $25 setup + $X/month for 250 calls
Growth:     $X/month for 2,500 calls  
Enterprise: Custom pricing, 12-month contract
```

#### 4. **Per-Number Subscription**
Used for reputation management:
```
Per number/month: $5 - $25
Includes: Monitoring, remediation, brand registration
```

### Pricing by Provider (Estimated)

| Provider | Setup | Monthly Min | Per-Call | Contract |
|----------|-------|-------------|----------|----------|
| **Hiya** | $25 | ~$200 | Tiered | 12 months |
| **First Orion** | Custom | $1,000+ | Custom | 12+ months |
| **TNS** | Custom | $2,000+ | Custom | Enterprise |
| **Numeracle** | Custom | $500+ | Per-number | Flexible |
| **Twilio** | $0 | $0 | $0.013-0.022/min | None |

### Hidden Costs

What vendors often don't include upfront:
- **Vetting/KYC fees:** $200-500 one-time
- **Analytics add-ons:** +20-30% monthly
- **Logo registration:** $100-300 per logo
- **Remediation services:** $50-200 per number incident
- **Implementation/integration:** $5,000-50,000 for enterprise

### The SMB Gap

**The problem:** Most pricing starts at $500+/month
**SMB budget reality:** $50-200/month for calling tools
**Opportunity:** Massive underserved market

---

## Business Models

### Model 1: Carrier Partnership (First Orion, TNS)

```
Revenue Flow:
  Enterprise → Provider → Carrier
                  ↓
            Revenue Share
```

**How it works:**
- Provider negotiates integration with carriers
- Gets paid per-call or via revenue share
- Must maintain carrier compliance standards
- High barrier to entry, high margins

**Economics:**
- Gross margins: 60-80%
- Customer acquisition: Long enterprise sales cycles
- Retention: Very high (switching costs)

### Model 2: Platform/Aggregator (Hiya, Numeracle)

```
Revenue Flow:
  Enterprise → Platform → Multiple Carriers
                  ↓
            Platform takes margin
```

**How it works:**
- Platform aggregates multiple carrier relationships
- Provides unified API/dashboard
- Handles compliance, vetting centrally
- Offers self-service for smaller customers

**Economics:**
- Gross margins: 50-70%
- Customer acquisition: Mixed (self-service + sales)
- Retention: High (integration stickiness)

### Model 3: CPaaS Add-On (Twilio, Bandwidth)

```
Revenue Flow:
  Developer → CPaaS → Carriers
                ↓
          Voice API margin + Branding add-on
```

**How it works:**
- Branded calling as feature within larger platform
- Developers integrate via API
- Usage-based pricing
- Part of broader communication stack

**Economics:**
- Gross margins: 40-60% (lower, competitive market)
- Customer acquisition: Self-service, low friction
- Retention: Platform-dependent

### Model 4: Consumer App (Truecaller)

```
Revenue Flow:
  Consumers (freemium) → Premium subscriptions
  Businesses → Verified caller fees
                  ↓
            Advertising (some markets)
```

**How it works:**
- Free app builds user base (crowdsourced data)
- Businesses pay for verified display to app users
- Not true carrier integration (app-dependent)

**Economics:**
- Gross margins: 80%+ (software)
- Customer acquisition: Viral growth
- Retention: App usage dependent

### Revenue Diversification Strategies

Most providers now offer multiple products:
1. **Branded Calling** (core revenue)
2. **Spam Analytics** (carrier contracts)
3. **Number Reputation Management** (subscription)
4. **Fraud Prevention** (enterprise add-on)
5. **Call Analytics** (SaaS add-on)
6. **Consulting/Professional Services** (implementation)

---

## Marketing Strategies

### Common Positioning Approaches

#### 1. **The Answer Rate Play**
*"Increase answer rates by 30%+"*

Most common message. Backed by case studies:
- First Orion claims: +45% conversion (Telehealth), +76% first-call conversions (Financial)
- Hiya claims: +30% answer rates generally
- Numeracle claims: +15% answer rates, +23% conversion

**Effectiveness:** High—speaks directly to pain point

#### 2. **The Trust/Brand Protection Play**
*"Protect your brand from spam labels"*

Focuses on negative consequences of inaction:
- Your number WILL get flagged eventually
- 25% of legitimate business numbers have been mislabeled
- One spam label = massive revenue loss

**Effectiveness:** Medium—fear-based, but real concern

#### 3. **The ROI Calculator**
*"See exactly how much you're losing"*

TNS and others offer interactive calculators:
- Input: Call volume, current answer rate, value per connection
- Output: Projected savings with branded calling

**Example calculation:**
```
Current: 100,000 calls × 20% answer rate × $50 value = $1,000,000
Branded: 100,000 calls × 35% answer rate × $50 value = $1,750,000
                                                       ─────────
                                              ROI:     $750,000
```

**Effectiveness:** High for enterprise—speaks to CFO

#### 4. **The Compliance/Mandate Play**
*"STIR/SHAKEN is required—are you compliant?"*

Leverages regulatory pressure:
- "Mandated by FCC since June 2021"
- "Non-compliant calls may be blocked"
- "Attestation requirements for all carriers"

**Effectiveness:** Medium—creates urgency but may confuse

#### 5. **The Customer Experience Play**
*"Your customers WANT to know it's you"*

Consumer-centric positioning:
- 87% of consumers prefer identified calls
- Branded calls improve NPS scores
- Part of omnichannel customer experience

**Effectiveness:** High for CX-focused enterprises

### Go-to-Market Approaches

#### Enterprise Sales (First Orion, TNS)
- Long sales cycles (3-12 months)
- Named account targeting
- Industry-specific case studies
- POC/pilot programs
- Dedicated success managers

#### Product-Led Growth (Hiya, Twilio)
- Self-service signup
- Free tier or trial
- Usage-based expansion
- Community/developer focus
- Content marketing heavy

#### Channel/Partner (Numeracle, smaller players)
- White-label solutions
- BPO/contact center focus
- Reseller programs
- Integration partnerships

### Content Marketing Themes

What competitors publish:
1. **State of the Call** annual reports (Hiya)
2. **Industry benchmark studies** (answer rates by vertical)
3. **Regulatory updates** (FCC filings, STIR/SHAKEN changes)
4. **Fraud trend reports** (CFCA data, AI threats)
5. **ROI case studies** (named customers, specific metrics)
6. **How-to guides** (compliance, integration)

---

## Public Perception

### Consumer Awareness

**The good news:**
- High awareness of spam/robocall problem (90%+)
- Positive reception of branded caller ID concept
- Trust increases when business name displayed

**The bad news:**
- Most don't know branded calling exists as a service
- Confusion between carrier spam blocking and business branding
- Fatigue from constant scam attempts

### Trust Dynamics

**Trust Hierarchy (Consumer Research):**
1. Known personal contacts (highest trust)
2. Recognized business with logo + name
3. Business name only
4. "Verified Caller" badge
5. Unknown number (lowest trust)

**Key finding:** Logo + name + call reason approaches personal contact trust levels

### Skepticism Factors

Why consumers remain cautious:
- "Can't scammers fake the brand too?"
- "How do I know this is really Chase Bank?"
- "My phone says 'Scam Likely' but then shows a brand?"

**Industry response:** STIR/SHAKEN attestation provides cryptographic proof, but consumer education lags behind.

### Business Perception

**Among enterprises:**
- ROI-focused: "Show me the metrics"
- Cautious about switching costs
- Concerned about carrier coverage gaps
- Interested in bundled solutions

**Among SMBs:**
- "Sounds expensive"
- "Do I really need this?"
- "My CRM doesn't integrate"
- Limited awareness of options

### Media Coverage

**Positive coverage:**
- "Finally, a solution to robocalls"
- "Businesses fighting back against spam labels"
- "STIR/SHAKEN protecting consumers"

**Negative coverage:**
- "Scammers adapting to new technology"
- "Deepfake voices the next threat"
- "Legitimate businesses still getting blocked"

---

## Modern vs Legacy Approaches

### Legacy: CNAM (Still in Use)

| Aspect | CNAM |
|--------|------|
| **Character limit** | 15 characters |
| **Media** | Text only |
| **Authentication** | None |
| **Coverage** | Landlines primarily |
| **Database** | Distributed, inconsistent |
| **Update speed** | Days to weeks |
| **Cost** | Low ($0.001-0.005/lookup) |

**Limitations:**
- "JOHN SMITH" fits, "FIRST NATIONAL BANK" doesn't
- No verification—anyone can register any name
- Different carriers = different databases
- Updates propagate slowly

### Modern: Rich Call Data (RCD)

| Aspect | RCD/Branded Calling |
|--------|---------------------|
| **Character limit** | 32 characters |
| **Media** | Text + Logo + Call Reason |
| **Authentication** | Cryptographic (STIR/SHAKEN) |
| **Coverage** | Mobile-first, cross-carrier |
| **Database** | Centralized, verified |
| **Update speed** | Real-time to hours |
| **Cost** | Higher ($0.02-0.08/call) |

### Technology Comparison

```
LEGACY CALL FLOW (CNAM):
Phone → PSTN → CNAM Lookup → Display "JOHN SMITH"
                   ↑
            [No verification]
            [May be spoofed]
            [15 char limit]

MODERN CALL FLOW (STIR/SHAKEN + RCD):
Phone → SIP/VoIP → Carrier Signs → RCD Attached → Display Brand
                        ↑               ↑
                 [Cryptographic]  [Verified Logo]
                 [A/B/C Attest]  [Call Reason]
                                 [32 char name]
```

### Hybrid Reality

Most businesses today use BOTH:
- CNAM for landline recipients
- RCD/Branded for mobile recipients
- Different coverage per carrier

**The challenge:** Managing consistent identity across systems

### What "Modern" Really Means

| Feature | Old Approach | Modern Approach |
|---------|--------------|-----------------|
| **Setup** | Call carrier, wait days | Self-service portal, minutes |
| **Vetting** | Manual, paper-based | Automated KYC, instant |
| **Delivery** | Hope it works | Confirmed delivery receipts |
| **Analytics** | Call logs only | Real-time dashboards, AI insights |
| **Reputation** | Reactive (fix when broken) | Proactive monitoring |
| **Integration** | Custom per carrier | Unified API |

---

## Market Size & Players

### Market Sizing

**Total Addressable Market (TAM):**
- Global outbound business calls: ~500 billion annually
- If 10% adopt branded calling at $0.03/call = **$1.5 billion**

**Serviceable Market (US):**
- US business outbound calls: ~150 billion annually
- Current branded calling adoption: ~5%
- Current market: ~$500 million
- Projected 2028: ~$2 billion (15% adoption)

### Market Share Estimates

```
┌─────────────────────────────────────────────────────────────┐
│                 MARKET SHARE (ESTIMATED 2026)               │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│   First Orion     ████████████████████░░░░░  35%            │
│   Hiya            ███████████████░░░░░░░░░░  28%            │
│   TNS             ████████░░░░░░░░░░░░░░░░░  18%            │
│   Numeracle       ████░░░░░░░░░░░░░░░░░░░░░   8%            │
│   Others (CPaaS)  ████░░░░░░░░░░░░░░░░░░░░░  11%            │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

### Competitive Landscape Summary

| Player | Strength | Weakness | Focus |
|--------|----------|----------|-------|
| **First Orion** | AT&T coverage, analytics | No self-service | Enterprise |
| **Hiya** | Platform breadth, AI | No AT&T | Enterprise + Mid-market |
| **TNS** | Infrastructure depth | Opaque | Large Enterprise |
| **Numeracle** | Aggregation, BPO focus | Smaller scale | Mid-market |
| **Twilio** | Developer experience | Not core focus | Developers |

### Recent M&A & Partnerships

- **2024:** Sinch acquires MessageBird (consolidation)
- **2025:** Hiya launches AI Sales Assistant
- **Jan 2026:** First Orion + Deutsche Telekom partnership
- **Ongoing:** Carrier exclusivity battles

---

## Future Outlook

### Near-Term (2026-2027)

1. **AI Voice Detection Becomes Standard**
   - Deepfake threats accelerate adoption
   - Real-time voice authentication
   - Hiya already launched; others will follow

2. **International Expansion Accelerates**
   - EU moving toward STIR/SHAKEN-like standards
   - UK rejected STIR/SHAKEN (Feb 2024) but alternatives emerging
   - APAC markets growing rapidly

3. **Messaging Integration**
   - First Orion launched ENRICH (branded messaging)
   - Omnichannel identity becoming expected
   - SMS + Voice + Email brand consistency

4. **SMB Market Opens**
   - Self-service platforms mature
   - Pricing compression at lower volumes
   - Integration with CRM/sales tools

### Medium-Term (2028-2030)

1. **Rich Media Calls**
   - Interactive call screens
   - Video preview before answering
   - App-like call experiences

2. **Universal Caller ID Registry**
   - Government or industry-wide verification
   - "Verified Business" becomes standard
   - Spoofing becomes much harder

3. **AI Voice Agents**
   - Hiya Sales Assistant is early signal
   - Branded AI calling on behalf of businesses
   - New regulatory questions

### Threats to Watch

1. **Deepfake Voice Cloning**
   - AI can now clone voices from 3 seconds of audio
   - Could undermine trust in voice entirely
   - Detection arms race

2. **Consumer App Fragmentation**
   - Truecaller, Hiya app, carrier apps all compete
   - No universal solution
   - Confusion continues

3. **Regulatory Uncertainty**
   - FCC leadership changes
   - International standards divergence
   - Compliance costs increasing

### BrandCall Opportunity Windows

| Opportunity | Timing | Difficulty |
|-------------|--------|------------|
| SMB self-service | Now | Medium |
| Developer API focus | Now | Medium |
| International markets | 2027+ | High |
| AI integration | 2026-2027 | High |
| Multi-tenant/white-label | Now | Medium |

---

## Appendix: Key Terms

| Term | Definition |
|------|------------|
| **STIR** | Secure Telephone Identity Revisited (RFC standards) |
| **SHAKEN** | Signature-based Handling of Asserted information using toKENs |
| **RCD** | Rich Call Data (name, logo, reason) |
| **CNAM** | Caller Name Delivery (legacy 15-char system) |
| **Attestation** | Verification level (A/B/C) |
| **Answer Rate** | % of calls answered (key KPI) |
| **Contact Rate** | % of attempts reaching intended person |
| **Number Burn** | When a number accumulates spam labels |
| **Remediation** | Removing spam/scam labels from numbers |
| **KYC** | Know Your Customer (vetting process) |

---

## Video Resources & Webinars

### TNS / Juniper Research: Branded Caller ID & Reputation Damage

**Source:** [YouTube - Juniper Research x TNS Webinar](https://www.youtube.com/watch?v=soGHOjV96UQ)  
**Participants:**
- Sam Barker – VP of Telecoms Market Research, Juniper Research
- Jim Tyrrell – VP of Enterprise Product Management, TNS
- Shelley Dunagan – Senior Director, Direct Sales and Partnerships, TNS

**Key Topics Covered:**
1. **Industry Collaboration** — How carriers, enterprises, and solution providers are working together to address call spoofing
2. **Standards & Protocols** — Deep dive into STIR/SHAKEN implementation and its impact on branded caller ID
3. **Healthcare Focus** — How fostering confidence among healthcare users is key to increasing call pickup rates
4. **Brand Reputation Damage** — How call spoofing impacts enterprise brand reputation and customer trust

**Key Insight:** TNS positions branded caller ID solutions as essential for combating reputation damage caused when scammers spoof legitimate business numbers. When customers receive spoofed calls appearing to be from a trusted brand, it erodes trust even when the brand had nothing to do with the call.

**Resource Link:** Full whitepaper available at [tnsi.com/enterprise-branded-calling](https://www.tnsi.com/enterprise-branded-calling)

---

### Gordon Marketing: TCPA Caller ID Compliance

**Source:** [YouTube - Gordon Marketing](https://youtube.com/shorts/kM6VQG0Siss)  
**Channel:** @GordonMarketing (Insurance FMO/IMO, Indianapolis)  
**Published:** June 2025

**Key Message:** Insurance agents and telemarketers cannot use *67 or any caller ID blocking on outbound calls. This constitutes a willful TCPA violation with $1,500 penalty per call.

**Quote from video:**
> "And it's a $1,500 fine if you knowingly or willfully violate the rule. So now I'm telling you about it. So now you knowingly know about it."
>
> "This rule says that if I use my phone to call you that I cannot block my caller ID. I cannot block my name."

**Relevance to BrandCall:** Positions branded caller ID as the compliant alternative to *67 blocking. Agents who want to hide their personal number should use branded calling (shows business name) rather than blocking (illegal for telemarketing).

---

## Sources

- IETF RFC 8224, 8225, 8226, 8588 (STIR/SHAKEN standards)
- Wikipedia: STIR/SHAKEN
- FCC STIR/SHAKEN Implementation Documentation
- CFCA Fraud Loss Survey 2025
- Hiya State of the Call Report 2025
- First Orion Product Documentation
- Juniper Research 2025 Competitor Leaderboard
- Company websites: Hiya, First Orion, TNS, Numeracle, Twilio, Bandwidth
- Industry press releases and news (2024-2026)

---

*Report compiled February 2026*  
*BrandCall Strategic Intelligence*
