# Branded Caller ID Industry Terminology & Business Concepts

> **BrandCall Internal Reference Document**  
> Last Updated: January 2026

---

## Table of Contents
1. [Industry Glossary](#industry-glossary)
2. [Business Concepts & Their Technical Implementations](#business-concepts--their-technical-implementations)
3. [Fraud Detection & Prevention Strategies](#fraud-detection--prevention-strategies)
4. [Transparency Requirements & Compliance](#transparency-requirements--compliance)
5. [BrandCall Architecture Notes](#brandcall-architecture-notes)

---

## Industry Glossary

### Core Terminology

#### **Branded Caller ID / Branded Calling**
The display of a verified business name, logo, and/or call reason on a recipient's mobile device when receiving an inbound call. Replaces the generic phone number display with recognizable business identity.

#### **Rich Call Data (RCD)**
Extended call metadata conveyed via STIR/SHAKEN that includes name, logo, and call reason. The "payload" that makes branded calls informative beyond just the phone number.

#### **STIR/SHAKEN**
- **STIR**: Secure Telephone Identity Revisited
- **SHAKEN**: Signature-based Handling of Asserted Information Using toKENs

Industry standards for caller ID authentication that use cryptographic signing to verify calls originate from legitimate sources. Required for all U.S. carriers since June 2021.

#### **CNAM (Caller Name Delivery)**
Legacy caller ID technology for landlines. Limited to 15 characters, no logos, wireline only. Branded caller ID supersedes this for mobile networks.

#### **Splash**
Industry term for the visual "splash screen" or branded display that appears on the recipient's phone when a call comes in. The "moment of truth" when branding either convinces someone to answer or reject.

#### **Voice Pick Up / VPU**
Answer rate metric—the percentage of outbound calls that result in a live human connection. The primary KPI that branded calling aims to improve.

#### **Impact**
Measurable effect of branded calling on business outcomes: answer rates, conversion rates, customer satisfaction (NPS), revenue per call attempt. Goes beyond simple "pick up rate."

#### **Contact Rate**
Percentage of call attempts that result in reaching the intended recipient (answered calls / total attempts). Industry standard hovers around 20-25% for unidentified calls.

#### **Callback Rate**
Percentage of missed calls where the recipient calls back. Branded calls significantly improve this—people trust calling back a number they recognize.

---

### Anti-Spam & Reputation Terminology

#### **"Use Less Volume"**
Industry philosophy: making fewer, higher-quality calls rather than blast dialing. Quality over quantity approach that:
- Reduces carrier flagging triggers
- Improves reputation scores
- Actually increases connection rates
- Costs less than high-volume approaches

#### **"Actually Make People Pick Up"**
Beyond just delivering calls—ensuring calls are *answered*. The shift from "calls delivered" metrics to "conversations started" metrics.

#### **"Don't Spam, Make the Phone Ring"**
Philosophy distinguishing legitimate outreach from spam:
- Spam: High volume, low personalization, ignored
- Making the phone ring: Targeted, recognized, answered

#### **"Actually Connect"**
Moving past vanity metrics (calls placed) to meaningful metrics (conversations had). The end goal isn't dialing—it's connecting.

#### **Spam Label / Spam Likely**
Warning tags applied by carriers and analytics engines (e.g., "Spam Likely", "Scam Risk", "Potential Fraud"). Appears in caller ID and causes most recipients to ignore the call.

#### **Caller ID Reputation**
A "credibility score" for phone numbers determined by:
- Call patterns and volume
- Answer rates and call durations
- Consumer complaints
- Historical behavior
- Carrier analytics engines

#### **Number Reputation Management (NRM)**
Ongoing monitoring and remediation of phone number reputation across carriers and analytics providers. Includes:
- Monitoring for spam labels
- Remediation (removing labels)
- Identity verification (KYC)
- Best practices consulting

#### **Remediation**
The process of actively removing spam/scam labels from phone numbers through carrier relationships. More than just monitoring—actually fixing problems.

---

### Fraud Prevention Terminology

#### **"Fake Fire" Back to Buyer**
When a lead buyer suspects fraud, they can "fire" the lead back to the seller with penalty. Applied when:
- Lead information is fabricated
- Contact attempts reveal disconnected/invalid numbers
- Pattern analysis suggests bot-generated leads
- Caller ID indicates spoofed source

**BrandCall Implementation**: Build lead validation feedback loops that allow downstream buyers to signal bad leads, creating accountability upstream.

#### **Detecting Spammers**
Identifying bad actors in the system through:
- Velocity analysis (too many calls too fast)
- Pattern recognition (same scripts, same cadences)
- Complaint correlation
- Answer rate anomalies (legit businesses have better rates)

#### **"Move the Needle"**
Demonstrating actual impact on business outcomes, not just vanity metrics. Bad actors often show activity without results—legitimate callers move actual business metrics.

#### **Spoofing**
Falsifying caller ID information to appear as a different number. STIR/SHAKEN combats this with cryptographic attestation, but sophisticated spoofing persists.

#### **Attestation Levels**
STIR/SHAKEN verification tiers:
- **A (Full)**: Carrier knows the customer, authorized to use the number
- **B (Partial)**: Carrier knows the customer, but not the number
- **C (Gateway)**: Carrier doesn't know the originator (highest risk)

---

### Throttling & Rate Control Terminology

#### **"Send to Back Office" (1:3 vs 1:1)**
Throttling strategy where not every lead/call gets immediate priority handling:
- **1:1**: Every lead gets immediate, full-resource handling
- **1:3**: Only 1 in 3 leads gets premium treatment; others go to "back office" (delayed handling, lower priority)

Used to:
- Manage call center capacity
- Detect lead quality before over-investing
- Prevent resource exhaustion on questionable sources

#### **Burst Systems**
Rate limiting based on sudden volume spikes:
- **Burst detection**: Flagging when call volume suddenly increases
- **Burst limiting**: Capping how many calls can be made in a short window
- **Burst penalties**: Reputation damage for suspicious burst patterns

Normal calling is relatively steady. Bursts indicate either:
- Legitimate campaign launch (needs pre-registration)
- Spam/robocall operations (should be blocked)

#### **Rate Limiting**
Caps on calls per hour/day from specific numbers or accounts:
- **Per-number limits**: 50-200 calls/day typical for legitimate use
- **Per-campaign limits**: Aggregate limits across number pools
- **Velocity limits**: Maximum calls per minute/hour

#### **Dialing Cadence**
The pattern and timing of outbound calls:
- **Predictive dialing**: Automated, high-velocity (triggers spam flags)
- **Progressive dialing**: Agent-paced, moderate velocity
- **Preview dialing**: Agent reviews before each call (lowest velocity)

Aggressive predictive dialing destroys number reputation.

#### **Number Burn Rate**
How quickly phone numbers accumulate spam labels and become unusable. Bad actors burn through numbers rapidly; legitimate businesses preserve them.

#### **Number Rotation / Cycling**
Practice of rotating through phone number pools to:
- Distribute call volume
- Allow "cooling off" periods
- Manage reputation across numbers

Can be legitimate (load balancing) or abusive (avoiding detection).

---

### Transparency & Compliance Terminology

#### **Black Box vs. Transparency**
Industry tension between opacity and visibility:

**Black Box Problems**:
- Carriers/analytics don't explain why numbers get flagged
- No visibility into delivery success
- Can't prove calls were delivered or branded

**Transparency Push**:
- Confirmation of branded call delivery
- Visibility into spam label causes
- Clear remediation paths
- Attestation level reporting

#### **Confirmed Call Delivery**
Verification that branded information was actually displayed to the recipient. BCID (Branded Calling ID) is the only ecosystem providing this confirmation.

#### **Know Your Customer (KYC)**
Identity verification process for businesses using branded calling:
- Business registration verification
- Use case documentation
- Calling practices audit
- Compliance attestation

Enables carriers to trust that branding requests are legitimate.

#### **TCPA (Telephone Consumer Protection Act)**
Federal law governing telemarketing calls:
- Prior express consent required
- Do Not Call registry compliance
- Time-of-day restrictions
- Penalties up to $1,500/violation

#### **Do Not Call (DNC) Registry**
National database of numbers that have opted out of telemarketing. Compliance is mandatory.

---

## Business Concepts & Their Technical Implementations

### The Answer Rate Problem

**Industry Reality (Hiya State of the Call 2025)**:
- 80% of unidentified calls go unanswered
- 48% of consumers never answer unknown calls
- Deepfake/AI voice fraud is increasing distrust

**What This Means for Businesses**:
- Traditional outbound calling is increasingly ineffective
- Caller identification is now table stakes
- Trust must be established before connection

### Smart Branding Strategy

**Not all calls should be branded**. Strategic approach:

| Call Type | Brand? | Rationale |
|-----------|--------|-----------|
| Appointment reminders | ✅ Yes | Expected, high-value |
| Delivery notifications | ✅ Yes | Time-sensitive, wanted |
| Customer service callbacks | ✅ Yes | Requested, trusted |
| Cold sales calls | ⚠️ Maybe | May reduce answer if unwanted |
| Collections | ❌ No | Negative association risk |
| High-volume campaigns | ❌ No | Cost-prohibitive |

**Rule of Thumb**: Brand ~1/3 of call attempts maximum. Focus on high-value touchpoints.

### Reputation-First Approach

**Order of Operations**:
1. **Fix spam labels first** (remediation)
2. **Protect numbers ongoing** (monitoring + management)
3. **Then add branding** (on clean numbers only)

Branding a spam-labeled number = wasted money. The label still shows.

### The Transparency Value Proposition

**Old Model** (Black Box):
- "Trust us, we delivered your brand"
- No proof of delivery
- No visibility into failures
- Dispute resolution impossible

**New Model** (Transparency):
- Confirmed delivery receipts
- Real-time display previews
- Label detection & remediation
- Clear analytics & attribution

---

## Fraud Detection & Prevention Strategies

### 1. Velocity-Based Detection

**Indicators of Fraud**:
- Sudden volume spikes (burst patterns)
- Consistent high velocity across all hours
- Short call durations (robocall signature)
- Low answer rates + high volume

**Implementation**:
```
if (calls_per_hour > threshold && avg_duration < 10sec) {
  flag_for_review();
  apply_rate_limit();
}
```

### 2. Reputation Scoring

**Factors**:
- Historical answer rates
- Complaint ratios
- Attestation level consistency
- Number age and usage patterns

**Scoring Model**:
- 0-30: High risk (block or heavy throttle)
- 31-70: Medium risk (monitor, soft limits)
- 71-100: Low risk (full access)

### 3. Lead Quality Feedback Loops

**"Fake Fire" Implementation**:
```
When downstream buyer reports bad lead:
  → Increment seller penalty score
  → If score > threshold:
      → Throttle seller's access
      → Require additional verification
      → Escalate for manual review
```

### 4. Pattern Recognition

**Legitimate Business Patterns**:
- Business hours calling
- Reasonable call durations (>30 sec conversations)
- Geographic consistency
- Consent documentation

**Spam/Fraud Patterns**:
- 24/7 automated calling
- Sub-10 second average durations
- Random geographic spread
- No consent trail

### 5. Throttling as Fraud Defense

**Progressive Throttling**:
| Trust Level | Rate Limit | Burst Allowance |
|-------------|------------|-----------------|
| New/Unverified | 10 calls/hour | None |
| Verified Basic | 50 calls/hour | 2x for 15 min |
| Verified Premium | 200 calls/hour | 5x for 30 min |
| Enterprise Trust | Custom | Pre-approved campaigns |

---

## Transparency Requirements & Compliance

### STIR/SHAKEN Compliance

**Required for All Carriers**:
- Cryptographic signing of originating calls
- Attestation level assignment
- Passing signatures through network
- Verification at termination

**BrandCall Must**:
- Work with compliant carriers only
- Maintain attestation records
- Support A-level attestation for customers

### CTIA Branded Calling ID (BCID)

**Industry-Governed Ecosystem**:
- Authorized Partners are vetted and audited
- Enterprise numbers, names, logos verified
- Delivery confirmation required
- Pay-for-delivery model (only pay when delivered)

**Becoming an Authorized Partner**:
- Rigorous vetting process
- Ongoing compliance audits
- Technical integration requirements
- Financial and legal standing

### KYC Requirements

**For BrandCall Customers**:
1. Business registration verification
2. Proof of phone number ownership/rights
3. Use case documentation
4. Calling volume estimates
5. Compliance attestation signing

### Transparency Reporting

**Must Provide to Customers**:
- Branded call delivery confirmation
- Spam label detection alerts
- Remediation status updates
- Answer rate analytics
- Attestation level reports

---

## BrandCall Architecture Notes

### Core Platform Components

```
┌─────────────────────────────────────────────────────────────┐
│                    BRANDCALL PLATFORM                       │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐         │
│  │   Identity  │  │  Reputation │  │   Branding  │         │
│  │   Service   │  │   Manager   │  │   Service   │         │
│  │   (KYC)     │  │   (NRM)     │  │   (RCD)     │         │
│  └──────┬──────┘  └──────┬──────┘  └──────┬──────┘         │
│         │                │                │                 │
│         └────────────────┼────────────────┘                 │
│                          │                                  │
│  ┌───────────────────────┴───────────────────────────┐     │
│  │              Analytics & Fraud Engine              │     │
│  │  - Velocity monitoring                             │     │
│  │  - Pattern detection                               │     │
│  │  - Reputation scoring                              │     │
│  │  - Throttling decisions                            │     │
│  └───────────────────────┬───────────────────────────┘     │
│                          │                                  │
│  ┌───────────────────────┴───────────────────────────┐     │
│  │              Carrier Integration Layer             │     │
│  │  - STIR/SHAKEN compliance                         │     │
│  │  - BCID authorized partner integration            │     │
│  │  - Multi-carrier delivery                         │     │
│  │  - Delivery confirmation                          │     │
│  └───────────────────────────────────────────────────┘     │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

### Fraud Prevention Implementation

**1. Onboarding Gate**
```typescript
async function onboardCustomer(application: Application): Promise<OnboardResult> {
  // Step 1: KYC verification
  const identity = await verifyBusinessIdentity(application);
  if (!identity.verified) return { status: 'rejected', reason: 'identity_failed' };
  
  // Step 2: Use case validation
  const useCase = await validateUseCase(application.intendedUse);
  if (useCase.riskScore > RISK_THRESHOLD) {
    return { status: 'manual_review', reason: 'high_risk_use_case' };
  }
  
  // Step 3: Initial trust assignment
  const trustLevel = calculateInitialTrust(identity, useCase);
  
  return {
    status: 'approved',
    trustLevel,
    initialLimits: getLimitsForTrust(trustLevel)
  };
}
```

**2. Real-Time Throttling**
```typescript
async function processCallRequest(request: CallRequest): Promise<CallDecision> {
  const customer = await getCustomer(request.customerId);
  
  // Check velocity
  const recentCalls = await getRecentCalls(customer.id, WINDOW_MINUTES);
  if (recentCalls.count > customer.limits.perHour) {
    return { allowed: false, reason: 'rate_limit_exceeded' };
  }
  
  // Check for burst patterns
  const burstScore = calculateBurstScore(recentCalls);
  if (burstScore > BURST_THRESHOLD && !customer.approvedBursts.includes(today)) {
    await alertBurstDetected(customer, burstScore);
    return { allowed: false, reason: 'burst_detected' };
  }
  
  // Check reputation
  const reputation = await getNumberReputation(request.fromNumber);
  if (reputation.spamLabeled) {
    return { allowed: false, reason: 'number_flagged', remediation: true };
  }
  
  return { allowed: true, brandingEligible: reputation.score > BRAND_THRESHOLD };
}
```

**3. Feedback Loop (Fake Fire)**
```typescript
async function handleLeadFeedback(feedback: LeadFeedback): Promise<void> {
  const lead = await getLead(feedback.leadId);
  const seller = await getCustomer(lead.sellerId);
  
  if (feedback.type === 'fake_fire') {
    // Increment penalty
    seller.penaltyScore += FAKE_FIRE_PENALTY;
    
    // Check threshold
    if (seller.penaltyScore > PENALTY_THRESHOLD) {
      await throttleCustomer(seller.id, 'excessive_fake_fires');
      await notifyCompliance(seller, 'threshold_exceeded');
    }
    
    // Adjust trust level
    seller.trustLevel = recalculateTrust(seller);
    await updateCustomer(seller);
    
    // Credit buyer
    await creditBuyer(feedback.buyerId, lead.cost);
  }
}
```

### Differentiation Strategy

**vs. Hiya/First Orion (Carrier-Centric)**:
- We offer platform-agnostic integration
- Transparent pricing (not carrier-dependent black boxes)
- Self-service + enterprise options
- Developer-first API approach

**vs. Numeracle (Remediation-Focused)**:
- We combine remediation + branding + fraud prevention
- Proactive protection, not just reactive fixes
- Integrated lead quality management
- Real-time throttling built-in

**BrandCall Unique Value**:
1. **Fraud Prevention Native**: Built into the platform, not bolted on
2. **Transparency First**: Full visibility into delivery, reputation, labels
3. **Smart Throttling**: Protect the ecosystem AND protect customers
4. **Lead Quality Integration**: Connect caller ID to lead lifecycle
5. **Developer API**: Modern, REST-first, easy integration

---

## Key Metrics & Benchmarks

### Industry Benchmarks (2025)
| Metric | Unbranded | Branded | Improvement |
|--------|-----------|---------|-------------|
| Answer Rate | 20% | 35% | +75% |
| Callback Rate | 5% | 25% | +400% |
| Conversion Rate | 2% | 3.5% | +75% |
| Spam Flag Rate | 25% | <5% | -80% |

### BrandCall Target Metrics
- **Customer Answer Rate**: >40% (vs. 20% industry unbranded)
- **Spam Label Rate**: <1% of managed numbers
- **Remediation Time**: <3 days average
- **Fraud Detection Rate**: >95% of bad actors caught
- **Customer Retention**: >90% annual

---

## Appendix: Key Industry Players

### Hiya
- **Focus**: Consumer spam blocking + enterprise branded calling
- **Products**: Hiya Connect (Branded Call, Number Registration)
- **Strength**: Mobile carrier partnerships, consumer app data
- **Market**: Enterprise call centers, carriers

### First Orion
- **Focus**: INFORM® branded calling + call blocking
- **Products**: INFORM (branding), SENTRY (blocking), PROTECT+
- **Strength**: 32-character display, logo support, carrier integrations
- **Market**: Large enterprises, healthcare, financial services

### Numeracle
- **Focus**: Number reputation management + identity verification
- **Products**: Number Reputation Management, Smart Branding, Number Check
- **Strength**: Remediation expertise, aggregated branding, consulting
- **Market**: BPOs, contact centers, enterprises

### CTIA BCID (Branded Calling ID)
- **Focus**: Industry-governed branded calling ecosystem
- **Model**: Authorized Partners who are vetted/audited
- **Strength**: Cross-network interoperability, delivery confirmation
- **Requirement**: Cryptographically signed via STIR/SHAKEN

### TransUnion (TNS)
- **Focus**: Call authentication, caller ID services
- **Products**: Enterprise Branded Calling, Call Guardian
- **Strength**: Deep carrier relationships, data assets
- **Market**: Carriers, large enterprises

---

*Document maintained by BrandCall Product Team*
