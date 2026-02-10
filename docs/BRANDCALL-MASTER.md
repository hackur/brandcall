# BrandCall.io — Master Strategy & Reference Document

> **The definitive guide to BrandCall's product, strategy, technology, and operations.**  
> **Last Updated:** February 9, 2026  
> **Status:** MVP Phase — Pre-Launch  
> **Production URL:** https://brandcall.io

---

## Table of Contents

1. [Executive Summary](#1-executive-summary)
2. [Product Vision & Positioning](#2-product-vision--positioning)
3. [The "Ad to Your Pocket" Paradigm](#3-the-ad-to-your-pocket-paradigm)
4. [Pricing Strategy & ROI](#4-pricing-strategy--roi)
5. [Dead Data Revival Program](#5-dead-data-revival-program)
6. [Multi-Touch Campaign System](#6-multi-touch-campaign-system)
7. [Vertical Playbooks](#7-vertical-playbooks)
8. [Marketing & Sales Strategy](#8-marketing--sales-strategy)
9. [Competitive Analysis](#9-competitive-analysis)
10. [Brand Identity & Design System](#10-brand-identity--design-system)
11. [Features & Capabilities](#11-features--capabilities)
12. [Technical Architecture](#12-technical-architecture)
13. [NumHub Integration](#13-numhub-integration)
14. [Compliance & Certifications](#14-compliance--certifications)
15. [Development & Operations](#15-development--operations)
16. [Development Roadmap](#16-development-roadmap)
17. [Industry Research & Education](#17-industry-research--education)
18. [Appendices](#18-appendices)

---

## 1. Executive Summary

BrandCall is a **Branded Caller ID SaaS platform** that displays business names, logos, and call reasons on outbound calls. We're not just a telecom tool — **we're a performance marketing channel.**

### The Problem
- **80%** of unidentified calls go unanswered (Hiya 2025)
- **48%** of consumers NEVER answer unknown calls
- **$41.82B** lost to telecom fraud globally (2025)
- Businesses waste millions on outbound calls that never connect

### Our Solution
BrandCall transforms every outbound call into a **branded impression** — a logo and business name pushed directly to someone's lock screen. No app install. No ad spend. No scroll-past. **100% viewability.**

### Why Now
- STIR/SHAKEN mandated since June 2021 — infrastructure is ready
- Answer rates at all-time lows — businesses are desperate
- SMB market completely underserved — enterprise-only competitors
- AI voice fraud increasing distrust — verified identity matters more than ever

### Current State
- ✅ Production infrastructure live (Hetzner Cloud, SSL, zero-downtime deploys)
- ✅ Core onboarding flow built (registration, KYC, admin panel)
- ✅ Laravel 12 + React 19 + Filament 3 stack
- ⏸️ Blocked on: Resend API (email), NumHub credentials (voice), Stripe (payments)
- 🎯 Focus: Get to "demo-able MVP" state

---

## 2. Product Vision & Positioning

### Core Thesis

> **BrandCall isn't a telecom tool. It's a performance marketing channel.**

Every branded call is:
1. **An ad impression** — your logo on their phone screen
2. **A trust signal** — verified identity builds confidence
3. **A conversion opportunity** — 3x more likely to answer
4. **A data reactivator** — revives dead/aged lead lists

### Positioning Statement

> "Enterprise-grade branded calling with startup-speed simplicity. Standards-compliant infrastructure, self-service access, transparent pricing. Minutes to first branded call, not weeks."

### Key Differentiators

| Us | Them (Hiya/First Orion/Numeracle) |
|----|-------------------------------------|
| Self-service signup in minutes | Weeks-long partner onboarding |
| Direct REST API | Partner-mediated, no API |
| Transparent per-call pricing | Opaque, "contact sales" |
| SMB-friendly ($0.05/call) | Enterprise minimums ($25K+/yr) |
| Modern stack (Laravel 12 + React) | Legacy platforms |
| Multi-tenant, white-label ready | Single-tenant, no reseller model |
| Built-in fraud prevention | Bolted-on or absent |
| Developer-first experience | Sales-team-first |

### The "Three Pillars" Value Prop

1. **"Brand Your Calls"** — Make people actually pick up the phone
2. **"Revive Dead Data"** — Turn aged leads back into connections
3. **"Ad to Your Pocket"** — Every call is a brand impression with 100% viewability

---

## 3. The "Ad to Your Pocket" Paradigm

### Reframing BCID as Mobile Advertising

Every branded call pushes your logo + business name directly to someone's lock screen. This is the **only ad format** that:
- Has **100% viewability** — the phone demands attention
- Includes a **built-in CTA** — answer the call
- Requires **no app install** — works on native dialer
- Can't be **scrolled past** or **ad-blocked**

### CPM Comparison

| Channel | CPM | Viewability | Has CTA? | Requires Install? |
|---------|----:|:----------:|:--------:|:-----------------:|
| Display Ads | $5-15 | ~50% | ❌ | ❌ |
| Social Ads | $8-20 | ~60% | ⚠️ | ❌ |
| Video Ads | $15-40 | ~70% | ❌ | ❌ |
| **BCID ($0.05/call)** | **$50** | **100%** | **✅ (Answer)** | **❌** |

At $0.05/call, BCID has a higher CPM — but the **quality of impression is unmatched**. You're not fighting for attention in a feed. You're demanding it with a ringing phone and your brand front-and-center.

### The Brand Recognition Accelerator

Marketing science: **3-5 touches** before someone recognizes your brand. BCID accelerates this because **each call is a visual brand impression** — even if they don't answer, they see your name and logo.

Stack it with SMS for devastating effectiveness:
- **Day 1:** BCID call → Brand impression #1 (maybe they answer)
- **Day 2:** SMS follow-up → Impression #2 (they recognize the name now)
- **Day 3:** BCID call → Impression #3 (higher answer probability)
- **Day 4-5:** SMS + call combo → Impressions #4-5

**By end of Week 1:** That lead has seen your brand **5-7 times** across two channels. They're not cold anymore — they're warm.

### Tom Cutting's Insight: Identity-Confirmed Engagement

*Inspired by Reserve Tech's "Email Open Leads" product:*

The principle: **Multi-channel identity confirmation creates highest-intent leads.**

Reserve Tech's model:
1. Match IP → identity via opt-in database (oRTB stream)
2. Bucket by behavior ("in-market sports," "reverse mortgage interest")
3. Send email → upon open, "confirm identity"
4. Sell as verified, in-market lead ($50 leads at $5 cost)

**BrandCall application:** BCID + SMS creates a similar identity-confirmation loop:
- BCID call → They see your brand (awareness)
- SMS → They open/read (engagement)
- BCID call again → They answer (conversion)
- Each touchpoint **confirms** they're receiving and engaging with your brand

**The economics:** You might pay more per dial ($0.05), but you're paying **far less** than acquiring fresh data. A 50K aged lead list reactivated at 10% = 5,000 connections worth $25K-$250K to acquire fresh.

---

## 4. Pricing Strategy & ROI

### Pricing Philosophy
- **Value-based:** Price to impact, not just features
- **Transparent:** No hidden fees, clear pricing page
- **Scalable:** Grow with customers, don't price them out
- **Competitive:** Undercut enterprise-only players on entry

### Current Pricing: $0.05/Call

At $0.05/call, the ROI math sells itself:

```
Client: 10,000 calls/month
Cost: $500/month

Without BCID: 10,000 calls × 15% answer rate = 1,500 conversations
With BCID:    10,000 calls × 45% answer rate = 4,500 conversations

Result: 3x more conversations for $500
```

### Tiered Pricing Model

| Tier | Per Call | Includes | Best For |
|------|--------:|----------|----------|
| **Standard BCID** | $0.05 | Branded calling, basic analytics | Single-channel outbound |
| **BCID + SMS Bundle** | $0.08 | Branded calling + SMS follow-up | Multi-touch campaigns |
| **Full Campaign** | $0.12 | BCID + SMS + campaign orchestration + analytics | Enterprise campaign management |

### Subscription Tiers (Platform Access)

| Tier | Monthly | Numbers | Calls Included | Features |
|------|--------:|--------:|---------------:|----------|
| **Starter** | $299 | Up to 5 | — | Reputation monitoring, basic analytics, email support |
| **Growth** | $999 | Up to 50 | 1,000 | + Remediation, API access, priority support |
| **Business** | $2,499 | Up to 250 | 10,000 | + Advanced analytics, fraud prevention, dedicated CSM |
| **Enterprise** | Custom | Unlimited | Unlimited | + SLA, white-label, custom fraud rules |

### Usage-Based Add-ons
- Additional branded calls: $0.03-$0.05/call (volume-dependent)
- Additional numbers: $5/number/month
- Premium remediation: $50/incident
- Advanced fraud engine: $500/month
- SMS messaging: Market rate + markup

### ROI Calculator (Build This as a Feature)

**Input:** Calls/month, current answer rate, revenue per connection  
**Output:** Projected answer rate lift, additional conversations, cost-per-connected-call comparison, ROI timeline

**Example calculation:**
```
Input:  100,000 calls × 20% answer × $50/connection = $1,000,000 revenue
Output: 100,000 calls × 40% answer × $50/connection = $2,000,000 revenue
BCID Cost: 100,000 × $0.05 = $5,000
Net Gain: $995,000
ROI: 199x
```

### Competitive Pricing Position

| Tier | BrandCall | Typical Competitor |
|------|-----------|-------------------|
| Entry | $299/mo + $0.05/call | $1,000+/mo or enterprise-only |
| Mid | $999/mo | $3,000-5,000/mo |
| Enterprise | Custom (lower) | Custom (higher) |

**Positioning:** "Enterprise features at mid-market prices"

### Pay-Per-Confirmed-Delivery Option

Following the BCID ecosystem model: consider offering a billing option where clients **only pay when branded info is confirmed delivered** to the recipient. This is BCID's biggest selling point and differentiates from per-attempt billing.

---

## 5. Dead Data Revival Program

### The Concept

Lead lists degrade. People stop answering unknown numbers. Data ages out. Companies spend $5-$50 per fresh lead to replace them.

**BCID resurrects those lists** because:
- Branded display builds trust → people answer numbers they'd normally ignore
- You're not buying new leads — you're reactivating ones you already own
- Even paying $0.05/dial, you save **thousands** on fresh data acquisition

### The Math

```
Scenario: Company has 50,000 aged leads (6+ months old)

Without BCID:
  - Answer rate on aged data: ~5%
  - Connections: 2,500
  - To replace with fresh leads at $25/lead: $1,250,000

With BCID:
  - Answer rate on aged data: ~15-20%
  - Connections: 7,500-10,000
  - Cost: 50,000 × $0.05 = $2,500
  - Fresh lead equivalent value: $187,500 - $250,000

Savings: $1,000,000+ vs. buying fresh data
```

### Productizing Dead Data Revival

**Standalone offering:** Clients upload aged lead lists → BrandCall runs branded campaigns → charge per reactivated lead (premium pricing, performance-based)

**Campaign structure:**
1. Upload aged lead list
2. Configure branded calling profile
3. Run multi-touch BCID campaign (3-5 attempts per lead)
4. Track answer rates, conversations, conversions
5. Bill per connected conversation or flat campaign fee

**Pricing model:** $1-5 per reactivated lead (connected conversation) vs. $25-50 for fresh leads

### Marketing Angle

> "Stop buying new leads. Start answering old ones."

> "Your aged data isn't dead — it's just not picking up for unknown numbers."

> "50,000 leads × $0.05/call = $2,500. The same leads fresh? $1.25M. Do the math."

---

## 6. Multi-Touch Campaign System

### Vision: Campaign Platform, Not Just Caller ID

BrandCall evolves from "branded caller ID service" to **"multi-touch campaign platform"** by orchestrating BCID + SMS sequences.

### Campaign Builder Feature

**Day-by-day sequence builder:**
```
Day 1: BCID Call → If no answer → Queue for Day 2
Day 2: SMS "Hi {name}, we tried calling from {brand}. Reply YES to schedule."
Day 3: BCID Call (higher answer probability — they've seen the brand twice)
Day 4: SMS follow-up with content/offer
Day 5: Final BCID Call + SMS combo
```

**Week 1 result:** 5-7 brand impressions, dramatically higher connection rate

### A/B Testing Built In

Let clients test:
- Branded vs. unbranded calls (prove the lift)
- Different call reasons ("Appointment Reminder" vs "Important Update")
- Different logos/display names
- SMS timing and copy
- Multi-touch vs. single-touch

### Analytics Dashboard

Track per campaign:
- **Answer rate lift** (branded vs. unbranded baseline)
- **Brand impressions delivered** (even unanswered calls = impressions)
- **Cost per connected conversation**
- **Reactivation rate** (for aged data campaigns)
- **Conversion attribution** (which touch converted?)

### SMS Integration Partners

To build multi-touch campaigns, integrate with:
- Twilio (SMS API)
- Bandwidth (carrier-grade SMS)
- Telnyx (cost-effective)

Or build SMS capability natively using NumHub's infrastructure.

---

## 7. Vertical Playbooks

### Tier 1: Highest-Value Verticals

#### Healthcare
- **Pain:** Patients miss appointment reminders, test results, prescription callbacks
- **BCID Value:** "Patients answer when they see 'Regional Medical Center' vs random number"
- **Proof:** +45% conversion rate (First Orion telehealth case)
- **Deal Size:** $25K-$100K/year
- **Campaign:** Appointment reminder BCID + SMS confirmation sequence
- **Compliance:** HIPAA — use generic names ("Acme Healthcare" not "Acme Oncology")

#### Insurance
- **Pain:** Open enrollment windows are time-sensitive, policy renewals ignored
- **BCID Value:** "Connect during moments that matter — enrollment, claims, renewals"
- **Proof:** +68% increase in long call duration (property insurance)
- **Deal Size:** $50K-$250K/year
- **Campaign:** Renewal BCID + SMS with policy details + follow-up call

#### Financial Services
- **Pain:** Fraud alerts go unanswered (ironic), collections at all-time-low answer rates
- **BCID Value:** "Make fraud alerts actually work. Make collections calls connectable."
- **Proof:** +76% first-call conversions (financial services call center)
- **Deal Size:** $100K-$500K/year
- **Campaign:** Fraud alert BCID (urgent) + account notification sequences

### Tier 2: Growth Verticals

#### Contact Centers & BPOs
- **Pain:** Numbers burn fast, multiple client brands, low contact rates
- **BCID Value:** White-label branded calling, number protection, client retention
- **Deal Size:** $20K-$150K/year (scales with clients)
- **Campaign:** Per-client branded profiles, automated number rotation

#### Real Estate
- **Pain:** Hot leads go cold in minutes, showing confirmations ignored
- **BCID Value:** Answer rate = deal velocity
- **Campaign:** New listing BCID + SMS with property photos + showing reminders

#### Solar/Home Services
- **Pain:** Appointment confirmations unanswered, technician ETA calls missed
- **BCID Value:** "$100+ to revisit a missed delivery. Make every appointment count."
- **Campaign:** Appointment confirm BCID + SMS with time window + day-of call

#### Auto Dealers
- **Pain:** Internet leads go cold, F&I follow-ups fall through
- **BCID Value:** Reach buyers in the buying window
- **Campaign:** Post-inquiry BCID + SMS with vehicle details + financing callback

### Vertical-Specific Landing Pages (Build These)
- `/industries/healthcare` — HIPAA-compliant branded calling
- `/industries/insurance` — Open enrollment & claims campaigns
- `/industries/financial` — Fraud alerts & collections
- `/industries/call-centers` — White-label for BPOs
- `/industries/real-estate` — Hot lead connection
- `/industries/solar` — Appointment confirmation

---

## 8. Marketing & Sales Strategy

### Go-to-Market Approach

**Phase 1 (Now):** Developer-first, self-service  
**Phase 2 (Q2):** Content marketing, SEO dominance  
**Phase 3 (Q3):** Vertical-specific outbound, partnerships  
**Phase 4 (Q4):** Enterprise sales team, channel partners

### Inbound Strategies

#### Content Marketing (SEO/Thought Leadership)

**Target Keywords:**
- "branded caller id" / "branded calling"
- "caller id reputation management"
- "spam labeled phone numbers" / "spam likely fix"
- "increase call answer rates"
- "STIR SHAKEN compliance"
- "what is branded caller id" (FAQ opportunity — no competitor has good content)

**Content to Create:**
| Content | Priority | Funnel Stage |
|---------|----------|-------------|
| FAQ Page | 🔴 CRITICAL | Top (no competitor has one) |
| "What is Branded Caller ID?" explainer | High | Top |
| "Is Your Number Showing as Spam Likely?" landing page | High | Top |
| ROI Calculator (interactive) | High | Middle |
| "Answer Rate Statistics 2026" | Medium | Top |
| Industry-specific case studies | High | Bottom |
| STIR/SHAKEN explained (non-technical) | High | Middle |
| Publish INDUSTRY-TERMS.md as web content | Medium | Middle |

**Free Tools (Lead Magnets):**
- **Number Check:** Free spam label check tool
- **Answer Rate Calculator:** Input metrics, show improvement
- **Compliance Checklist:** TCPA/STIR-SHAKEN readiness assessment

#### Paid Advertising

**Google Ads:** Intent keywords ("branded caller id pricing"), competitor keywords ("Hiya alternative"), problem keywords ("phone number spam labeled")

**LinkedIn Ads:** Target call center managers, VP Operations, VP Sales in healthcare, insurance, financial services

### Outbound Strategies

#### Account-Based Marketing

**Target profile:**
- Companies with 10+ call center seats
- High call volume industries
- Recent hires: Call Center Manager, Director of Operations
- Using competitor or legacy solutions

**Multi-Touch ABM Sequence:**
```
Week 1: LinkedIn connection + personalized message
Week 2: Email with industry-specific case study
Week 3: Direct mail with ROI calculator
Week 4: SDR call with demo offer (using BCID — eat our own cooking!)
Week 5: Retargeting ads
Week 6: Follow-up email + calendar link
```

### Key Messaging

**Primary Messages:**
1. "Minutes, not weeks" — Speed to first branded call
2. "No partners needed" — Direct access vs. gatekept ecosystem
3. "95% of consumers ignore unknown numbers" — The problem
4. "48%+ answer rate improvement" — The outcome
5. "Your logo on their phone is an ad they can't skip" — Ad to Pocket

**Quick Win Messaging Updates:**
- Add "95% of consumers ignore unknown numbers" stat to homepage hero
- Add "48%+ answer rate improvement" stat prominently
- Create visual comparison: Our 3-step process vs. BCID's 7-step partner journey
- Add "Minutes, not weeks" and "No partners needed" messaging

### Sales Playbook

#### Qualification (BANT+)

| Strong Signal | Weak Signal |
|--------------|-------------|
| Spending on Hiya/FO/Numeracle | "Free solution only" |
| VP+ direct decision maker | Individual contributor |
| Spam labels hurting NOW | "Exploring options" |
| Active RFP, <90 days | "Next year maybe" |
| >10,000 calls/month | <1,000 calls/month |

#### Discovery Questions
1. "What percentage of your outbound calls actually get answered?"
2. "How many numbers are currently flagged as spam?"
3. "What's it costing you when calls don't connect?"
4. "How much are you spending on fresh lead acquisition?"
5. "Have you tried multi-touch campaigns combining calls and SMS?"

#### Objection Handling

**"We already use Hiya/First Orion"**
> "Great — so you see the value. What we hear from switchers is: no self-service, opaque pricing, and weeks to onboard. Want to see how we compare in 15 minutes?"

**"It's too expensive"**
> "At $0.05/call, if you make 10K calls/month, that's $500. If we triple your answer rate, how many more deals does that close? Most see payback in 30 days."

**"We'll just keep rotating numbers"**
> "Each burned number costs $500+. What if you never had to burn a number again?"

#### Demo Flow
1. **Hook** (2 min): Show how their number currently displays → show branded version
2. **Problem** (5 min): Spam label examples, unanswered call stats for their industry
3. **Solution** (10 min): Live branded call demo, dashboard tour, API overview
4. **ROI** (5 min): Calculate with their actual numbers
5. **Next Steps** (3 min): Trial offering, timeline

### Referral Program
- 20% discount for referrer + referee
- Tiered rewards for multiple referrals
- Case study participation bonuses
- Revenue share for partner referrals

---

## 9. Competitive Analysis

### Market Landscape

| Tier | Competitors | Market Focus | Our Advantage |
|------|------------|-------------|---------------|
| **Tier 1 — Leaders** | Hiya, First Orion, TNS | Enterprise, Carrier | Self-service, transparent pricing |
| **Tier 2 — Specialists** | Numeracle | Mid-market, BPOs | Developer experience, multi-touch |
| **Tier 3 — CPaaS** | Twilio, Bandwidth, Telnyx | Developers | Full branded calling (not just STIR/SHAKEN) |
| **Tier 4 — Ecosystem** | CTIA BCID | Industry framework | Direct access, no partner required |

### Detailed Competitor Profiles

#### Hiya
- **Strengths:** 500M+ consumer app users, AI Voice Detection, free number registration
- **Weaknesses:** No AT&T coverage, enterprise-focused pricing, no self-service
- **Our angle:** "Hiya protects consumers. BrandCall protects businesses. We give you visibility and control they can't."

#### First Orion
- **Strengths:** Only provider with AT&T coverage, strongest carrier relationships, best case studies
- **Weaknesses:** Enterprise only, complex onboarding, no self-service, high minimums
- **Our angle:** "First Orion invented branded calling. BrandCall reinvented it for modern businesses."

#### Numeracle
- **Strengths:** Only true aggregated platform, remediation expertise, white-label for BPOs
- **Weaknesses:** Service-heavy (less platform), no fraud prevention, higher-touch sales
- **Our angle:** "Numeracle is great at fixing problems. BrandCall prevents them in the first place."

#### CTIA BCID Ecosystem
- **Strengths:** Industry-governed, standards-based, cross-network, confirmed delivery
- **Weaknesses:** Partner-mediated only, no self-service, no FAQ/education, opaque pricing
- **Our angle:** "Same BCID-compliant infrastructure. Direct access. Self-service. Minutes, not weeks."

#### Quality Voice & Data (QVD)
- **Strengths:** Carrier status, curated number inventory, integrated SIP + monitoring + branding
- **Weaknesses:** Sales-heavy approach, legacy UX, telecom-centric positioning
- **Our angle:** Modern developer experience, transparent pricing, platform flexibility

### Feature Comparison

| Feature | BrandCall | Hiya | First Orion | Numeracle | BCID |
|---------|:---------:|:----:|:-----------:|:---------:|:----:|
| Self-service signup | ✅ | ✅ | ❌ | ❌ | ❌ |
| Direct REST API | ✅ | ✅ | ⚠️ | ✅ | ❌ |
| Business name display | ✅ | ✅ | ✅ | ✅ | ✅ |
| Logo display | ✅ | ✅ | ✅ | ✅ | ✅ |
| Call reason display | ✅ | ✅ | ✅ | ✅ | ✅ |
| Transparent pricing | ✅ | ❌ | ❌ | ❌ | ❌ |
| Multi-tenant | ✅ | ❌ | ❌ | ❌ | ❌ |
| SMS integration | 📋 | ❌ | ✅ (ENRICH) | ❌ | ❌ |
| Fraud prevention | ✅ | ❌ | ❌ | ❌ | ❌ |
| Multi-touch campaigns | 📋 | ❌ | ❌ | ❌ | ❌ |
| Dead data revival | 📋 | ❌ | ❌ | ❌ | ❌ |
| A/B testing | 📋 | ❌ | ❌ | ✅ | ❌ |
| ROI calculator | 📋 | ❌ | ❌ | ❌ | ❌ |
| AT&T coverage | 🔄 | ❌ | ✅ | ✅ | ✅ |

### Our Unique Differentiation

1. **"Ad to Pocket" positioning** — Reframe BCID from telecom to marketing
2. **Dead Data Revival** — Standalone product no competitor offers
3. **Multi-Touch Campaigns** — BCID + SMS orchestration platform
4. **Fraud Prevention Native** — Built in, not bolted on
5. **Self-Service + Developer-First** — Minutes to first branded call
6. **Transparent Pricing** — $0.05/call, no hidden fees

---

## 10. Brand Identity & Design System

### Brand Essence

**Mission:** Empower businesses to build trust with every call through branded caller identification.

**Personality:** Professional, Trustworthy, Modern, Confident

**Voice:** Clear and direct. Technical when needed, approachable always. Authoritative on compliance. No jargon for jargon's sake.

### Color Palette

| Role | Color | Hex | Usage |
|------|-------|-----|-------|
| Primary | Indigo 600 | `#4F46E5` | Buttons, links, focus |
| Primary Hover | Indigo 700 | `#4338CA` | Hover states |
| Primary Light | Indigo 500 | `#6366F1` | Light accents |
| Accent | Violet 600 | `#7C3AED` | Gradients |
| Background Dark | Slate 950 | `#020617` | Darkest background |
| Background | Slate 900 | `#0F172A` | Dark background |
| Card BG | Slate 800 | `#1E293B` | Elevated surfaces |
| Border | Slate 700 | `#334155` | Borders, dividers |
| Body Text | Slate 400 | `#94A3B8` | Body text |
| Headings | White | `#FFFFFF` | High emphasis text |
| Success | Green 500 | `#22C55E` | Success states |
| Warning | Amber 500 | `#F59E0B` | Warnings |
| Error | Red 500 | `#EF4444` | Errors |
| Info | Blue 500 | `#3B82F6` | Info messages |

### Typography

**Font:** Inter (system-ui fallback)  
**Scale:** 1.25 ratio (Major Third)

| Element | Size | Weight | Line Height |
|---------|------|--------|-------------|
| Display | 4.5rem (72px) | 800 | 1.1 |
| H1 | 3rem (48px) | 700 | 1.2 |
| H2 | 2.25rem (36px) | 700 | 1.25 |
| H3 | 1.5rem (24px) | 600 | 1.3 |
| Body | 1rem (16px) | 400 | 1.6 |
| Small | 0.875rem (14px) | 400 | 1.5 |

### Spacing

**Base unit:** 8px. All spacing is multiples of 8px.

### Responsive Breakpoints

| Name | Width | Usage |
|------|-------|-------|
| xs | 320px | Small phones |
| sm | 640px | Large phones |
| md | 768px | Tablets |
| lg | 1024px | Small laptops |
| xl | 1280px | Desktops |
| 2xl | 1536px | Large screens |

*Full design system details in BRAND-IDENTITY.md and BRANDING.md.*

---

## 11. Features & Capabilities

### Current (Built)

| Feature | Status |
|---------|--------|
| Production server (Hetzner CPX21) | ✅ Live |
| SSL/HTTPS (Let's Encrypt) | ✅ |
| Zero-downtime deploys (Deployer) | ✅ |
| Landing page (CMS, 8 layouts, 7 color schemes) | ✅ |
| 4-step registration flow | ✅ |
| Onboarding dashboard (Profile, Documents, Tickets, Settings, Docs) | ✅ |
| Document upload (Spatie Media Library) | ✅ |
| Support tickets (create, view, reply) | ✅ |
| Filament admin panel | ✅ |
| Dev dashboards (Horizon, Telescope, Pulse, Health) | ✅ |
| 51 passing tests | ✅ |
| Multi-tenant architecture (TenantScope) | ✅ |
| RBAC (Spatie Permission: super-admin, owner, admin, member) | ✅ |
| API structure (REST, per-brand API keys) | ✅ |
| Webhook support | ✅ |

### Planned (Next)

| Feature | Priority | Blocked By |
|---------|----------|------------|
| Email verification & password reset | 🔴 HIGH | Resend API key |
| NumHub voice API integration | 🔴 HIGH | NumHub credentials |
| Admin KYC review workflow | 🔴 HIGH | — |
| Real-time number monitoring dashboard | 🎯 HIGH | NumHub |
| Answer rate analytics per brand/number | 🎯 HIGH | NumHub |
| Confirmed call delivery display | 🎯 HIGH | NumHub |
| ROI Calculator (interactive tool) | 🎯 HIGH | — |
| FAQ page | 🔴 CRITICAL | — |
| A/B testing dashboard | 🟡 MEDIUM | — |
| Multi-Touch Campaign Builder | 🟡 MEDIUM | SMS integration |
| Dead Data Revival product | 🟡 MEDIUM | Core platform |
| Google Verify integration (free) | 🟡 MEDIUM | — |
| Automated flagged number alerts | 🟡 MEDIUM | NumHub |
| Stripe billing integration | 🟢 LOW | — |
| SMS/MMS messaging | 📋 Future | — |
| White-label for BPOs | 📋 Future | — |

### Feature Matrix vs. Competitors

| Feature | BrandCall | Hiya | First Orion | Numeracle |
|---------|:---------:|:----:|:-----------:|:---------:|
| Answer rate tracking | 📋 | ✅ | ✅ | ✅ |
| Campaign comparison | 📋 | ✅ | ✅ | ✅ |
| Real-time dashboard | 📋 | ✅ | ✅ | ✅ |
| Spam label monitoring | 📋 | ✅ | ✅ | ✅ |
| Label remediation | 📋 | ✅ | ✅ | ✅ |
| Number preview (device screenshots) | 📋 | ❌ | ❌ | ✅ |
| AI voice detection | 📋 | ✅ | ❌ | ❌ |
| Spoof protection | 📋 | ✅ | ✅ | ✅ |
| Developer docs | ✅ | ✅ | ⚠️ | ⚠️ |
| No-code setup | ✅ | ✅ | ❌ | ❌ |
| White-label | 📋 | ❌ | ❌ | ✅ |

---

## 12. Technical Architecture

### Stack

| Layer | Technology |
|-------|-----------|
| Backend | Laravel 12, PHP 8.2+ |
| Frontend | React 19, TypeScript, Tailwind CSS v4 |
| Admin Panel | Filament 3 |
| SPA Bridge | Inertia.js |
| Auth | Sanctum + Spatie Permission |
| Database | SQLite (dev), MariaDB (prod) |
| Cache/Queue | Redis |
| Monitoring | Horizon, Pulse, Telescope, Spatie Health |
| Deployment | Deployer (zero-downtime) |

### Production Server

| Property | Value |
|----------|-------|
| Provider | Hetzner Cloud |
| IP | 178.156.223.166 |
| Type | CPX21 (3 vCPU, 4GB RAM, 80GB SSD) |
| Location | Ashburn, VA (us-east) |
| OS | Ubuntu 24.04 |
| Stack | Nginx 1.24, PHP 8.3, MariaDB 10.11, Node 22, Redis |
| Cost | €10.59/mo (~$11.50/mo) |
| SSH | `ssh -i ~/.ssh/id_rsa root@178.156.223.166` |

### Multi-Tenancy

Row-level security via `TenantScope` global scope. All tenant-scoped models automatically filter by `tenant_id`.

### Voice Provider Abstraction

Driver pattern for voice providers — NumHub is the primary driver, with Twilio as fallback:

```php
// config/voice.php
VOICE_DRIVER=numhub  // or telnyx, twilio
```

### API Structure

```
POST /api/v1/brands/{slug}/calls     # Initiate branded call
GET  /api/v1/brands/{slug}/calls     # List call history
GET  /api/v1/brands/{slug}/analytics # Brand analytics
```

### Webhook Events

- `call.initiated` — Call started
- `call.answered` — Call answered
- `call.completed` — Call ended
- `brand.status_changed` — Brand status update

### Quality Commands

```bash
composer dev       # Start all dev servers
composer quality   # Lint + static analysis
composer test      # Run tests
composer smoke     # Smoke tests
composer deploy    # Deploy to production
composer rollback  # Rollback to previous release
```

*Full technical details in DEVELOPER.md, DEPLOYMENT.md, and ECOSYSTEM.md.*

---

## 13. NumHub Integration

### Overview

NumHub provides white-label BCID infrastructure via their BrandControl platform. They handle vetting, signing, and carrier relationships. We handle customer acquisition and UX.

### Status: 🟡 Ready for Testing

| Area | Status |
|------|--------|
| API Docs | ✅ Downloaded (Swagger) |
| Test Portal | ✅ Account created (Google OAuth) |
| API Credentials | 🟡 Pending (need to explore portal) |
| Authentication | 📋 TokenManager designed |
| Application Management | 📋 Designed |
| Display Identity | 📋 Planned |
| Webhooks | 📋 Planned |

### Portal Access

- **URL:** `https://www.atlaas.app/signin`
- **Login:** Google OAuth (jcsarda@gmail.com)
- **API Base:** `https://brandidentity-api.numhub.com/api/v1/`
- **Rate Limit:** 100 requests/minute

### NumHub Contacts

| Name | Role | Email |
|------|------|-------|
| Daniela Villamar | Product Manager | dvillamar@numhub.com |
| Drew Andersen | Director of Sales | dandersen@numhub.com |
| Sara Hutchinson | Director of Operations | shutchinson@numhub.com |
| Support | | BCIDsupport@numhub.com |

### Integration Phases

1. **Authentication** (Week 1): TokenManager, API credentials, config
2. **Application Management** (Week 2): Create/update BCID applications, document uploads, OTP
3. **Display Identity** (Week 3): Caller ID management, phone numbers, logos
4. **Attestation** (Week 4): STIR/SHAKEN A-level attestation
5. **Reports & Notifications** (Week 5): Settlement reports, alerts
6. **OSP/Reseller Features** (Week 6): Deals, fee management

### Database Schema

New tables for NumHub integration:
- `numhub_tokens` — OAuth token cache
- `numhub_entities` — Business → NumhubEntityId mapping
- `numhub_identities` — Phone → NumhubIdentityId mapping
- `numhub_documents` — Document tracking (metadata only, pass-through)
- `numhub_sync_logs` — API audit trail

### Document Upload Strategy

**Pass-through to NumHub (recommended):** User uploads → BrandCall validates format/size → forwards immediately to NumHub API → deletes local copy → stores only metadata. Minimizes PII storage and compliance burden.

### Filament Admin Pages (Planned)

- NumHub Dashboard (stats, recent applications, alerts)
- Entity Mappings (business ↔ NumHub entity CRUD)
- API Sync Log (audit trail)
- Settlement Reports (billing)
- NumHub Settings (credentials, connection test)

*Full integration details in NUMHUB-INTEGRATION-STATUS.md, NUMHUB-DATABASE-SCHEMA.md, NUMHUB-CLIENT-DESIGN.md, NUMHUB-DATA-MODELS.md, NUMHUB-ADMIN-DESIGN.md, and NUMHUB-TESTING-STRATEGY.md.*

---

## 14. Compliance & Certifications

### Regulatory Overview

| Area | Risk | Key Requirement |
|------|------|-----------------|
| STIR/SHAKEN | 🔴 HIGH | Must implement call authentication |
| TCPA | 🔴 HIGH | Prior express consent for many calls |
| Caller ID Spoofing | 🔴 HIGH | Cannot transmit misleading caller ID with intent to defraud |
| Do Not Call | 🟠 MEDIUM | Must honor DNC list scrubbing |
| State Laws | 🟠 MEDIUM | Various state-specific requirements |
| HIPAA | 🔴 HIGH | PHI protection for healthcare customers |
| GLBA/PCI | 🔴 HIGH | Financial data protection |

### STIR/SHAKEN

- **Attestation Levels:** A (Full — required for BCID), B (Partial), C (Gateway)
- Only Level A calls can display branded information
- NumHub's vetting process ensures Level A attestation
- All voice service providers must be in FCC Robocall Mitigation Database

### TCPA Key Rules

- Calling hours: **8 AM - 9 PM** recipient's local time
- Cell phone marketing: Requires **prior express written consent**
- Prerecorded messages: Must include opt-out mechanism available throughout
- Call abandonment: Limited to 3% of answered calls per campaign per 30 days
- Penalties: **$500-$1,500 per violation** (private right of action)

### KYC/Certification Requirements

| Document | Purpose | Format |
|----------|---------|--------|
| Business License | Verify legal entity | PDF, JPG |
| Tax ID (EIN/W-9) | Verify tax registration | PDF |
| Government ID | Verify authorized representative | PDF, JPG |
| Phone Number LOA | Prove number authorization | PDF only |
| Brand Logo | Display on recipient phones | BMP (200×200px) |

### Certification Levels

| Level | Requirements | Unlocks |
|-------|-------------|---------|
| Basic | Business License, Tax ID | Account, dashboard |
| Verified | + Identity verification | Brand profile creation |
| Full | + Phone LOA, Logo | Live branded calling |
| Enterprise | + Additional vetting | Multi-brand, white-label |

### SOC 2 Compliance (Planning Phase)

Target: **SOC 2 Type II** (Security + Availability + Confidentiality)

Estimated timeline: 12-18 months  
Estimated cost: $60K-$150K first year, $40K-$100K ongoing

*Full compliance details in COMPLIANCE.md, CERTIFICATIONS.md, SOC2-COMPLIANCE.md, and TCPA-VIOLATIONS-GUIDE.md.*

---

## 15. Development & Operations

### Development Setup

```bash
git clone https://github.com/your-org/brandcall.git
cd brandcall
composer install && npm install
cp .env.example .env && php artisan key:generate
php artisan migrate --seed
composer dev  # Start all dev servers
```

### Deployment

```bash
npm run build        # Build frontend
composer deploy      # Zero-downtime deploy via Deployer
composer rollback    # Rollback if needed
```

### Server Structure

```
/var/www/brandcall/
├── current -> releases/N     # Symlink to active release
├── releases/                 # Release history (keep 5)
└── shared/
    ├── .env
    └── storage/
```

### Monitoring

| URL | Purpose | Access |
|-----|---------|--------|
| `/admin` | Filament admin | Super-admin only |
| `/horizon` | Queue monitoring | Admin email only |
| `/pulse` | Performance monitoring | Admin email only |
| `/telescope` | Debug dashboard | Local env only |
| `/health` | Health checks | Authenticated users |

### Test Accounts

| Email | Password | Role |
|-------|----------|------|
| admin@brandcall.io | password | super-admin |
| owner@example.com | password | owner |

*Full details in DEVELOPER.md, DEPLOYMENT.md, and ECOSYSTEM.md.*

---

## 16. Development Roadmap

### Phase 0: Unblock Email (1 day)
**Goal:** Enable user verification  
**Blocked on:** Resend API key  
**Action:** Add Resend key → test email verification → style templates

### Phase 1: Demo-Ready MVP (1 week)
- Admin KYC workflow (document review, approve/reject)
- UX polish (toasts, loading states, error messages, mobile check)
- Post-approval mock dashboard ("Coming Soon" sections)
- Content pages (ToS, Privacy, FAQ, Contact, error pages)

### Phase 2: NumHub Integration (2-4 weeks)
- Authentication & token management
- BCID application submission flow
- Document upload (pass-through)
- Display identity management
- Call initiation & delivery confirmation

### Phase 3: Billing & Scale (2 weeks)
- Stripe/Cashier subscription plans
- Billing portal & invoice history
- Usage-based billing
- Team invitations & role management

### Phase 4: Analytics & Campaigns (Ongoing)
- Answer rate tracking & dashboards
- A/B testing
- ROI calculator tool
- Multi-touch campaign builder (BCID + SMS)
- Dead Data Revival product

### Phase 5: Growth Features (Ongoing)
- Blog/content system
- Referral program
- White-label for BPOs
- Advanced analytics & AI insights
- API documentation portal
- Compliance reporting (TCPA)
- International expansion

### Enterprise Roadmap (from authnet-gateway analysis, 14 phases, ~890 hours)

1. Testing & QA
2. Complete trust/credibility pages
3. Advanced dashboard features
4. Authorize.net API integration
5. Merchant onboarding workflow
6. Admin & operational features
7. Security hardening (2FA, rate limiting, audit logging)
8. Email & communication
9. Monitoring & observability
10. Documentation & support
11. Performance optimization
12. Infrastructure & deployment
13. Advanced features (payments, AI, marketplace)
14. Launch preparation

---

## 17. Industry Research & Education

### How Branded Calling Works

```
Business → Carrier Signs Call (STIR/SHAKEN) → RCD Attached (Name, Logo, Reason) → Carrier Delivers → Recipient Sees Brand
```

**Without branding:**
```
📱 (555) 123-4567 — [Answer] [Decline]
```

**With branding:**
```
📱 🏥 [Logo] ABC Medical Group
    "Appointment Reminder"
    ✓ Verified Caller — [Answer] [Decline]
```

### Key Industry Statistics

| Metric | Value | Source |
|--------|-------|--------|
| Unidentified calls unanswered | 80% | Hiya 2025 |
| Consumers who never answer unknown | 48% | Hiya 2025 |
| Global telecom fraud losses | $41.82B | CFCA 2025 |
| Branded call answer rate improvement | +30-76% | First Orion cases |
| US business robocalls monthly | 4B+ | Industry data |

### Market Size

- **TAM:** ~$1.5B (10% of 500B global business calls at $0.03/call)
- **US SAM:** ~$500M current, projected $2B by 2028
- **Adoption:** ~5% currently, growing 25%+ annually
- **SMB gap:** Massive — most solutions start at $500+/month

### Industry Glossary

| Term | Definition |
|------|-----------|
| **BCID** | Branded Calling ID — CTIA-governed ecosystem |
| **RCD** | Rich Call Data — name, logo, call reason payload |
| **STIR/SHAKEN** | Call authentication standard (cryptographic signing) |
| **CNAM** | Caller Name Delivery — legacy 15-char system |
| **Attestation** | Verification level: A (full), B (partial), C (gateway) |
| **VPU** | Voice Pick Up — answer rate metric |
| **Number Burn** | When a number gets spam-labeled and becomes unusable |
| **Remediation** | Removing spam/scam labels from numbers |
| **LOA** | Letter of Authorization — number ownership proof |
| **Splash** | The branded display that appears when phone rings |

*Full industry research in INDUSTRY-RESEARCH.md and INDUSTRY-TERMS.md.*

---

## 18. Appendices

### A. BCID Ecosystem Authorized Partners

**Onboarding Agents:** BBD CallPass, Hiya, Numeracle, NumHub, Twilio, and others  
**Vetting Agents:** Aegis Mobile, Numeracle, NumHub, Twilio, and others  
**Signing Agents:** TransNexus, Numeracle, Twilio, Sansay, and others

### B. Alternative Providers Evaluated

35+ providers analyzed across 7 tiers. Top alternatives to NumHub:
1. **TransNexus** — Best docs, BCID authorized, responsive team
2. **Bandwidth** — Strong API, Hosted Signing Service
3. **Numeracle** — Aggregated approach, multi-carrier
4. **Telnyx** — Developer experience, transparent pricing

*Full analysis in NUMHUB-ALTERNATIVES.md.*

### C. Automation & AI Integration

**DeepAgents + N8N + ComfyUI** integration designed for:
- Automated code review agents
- Image generation for marketing materials
- Workflow automation via N8N webhooks

*Details in DEEPAGENTS-N8N-COMFYUI-INTEGRATION.md.*

### D. Document Index

| Document | Location | Purpose |
|----------|----------|---------|
| BRANDCALL-MASTER.md | docs/ | This document (consolidated master) |
| PLAN.md | root | Phase plan & current state |
| TODO.md | root | Task checklist |
| README.md | root | Project overview & setup |
| CLAUDE.md | root | AI assistant context |
| API.md | docs/ | API reference |
| FEATURES.md | docs/ | Feature comparison & roadmap |
| BRAND-IDENTITY.md | docs/ | Full design system |
| BRANDING.md | docs/ | Brand guidelines |
| LEADS-STRATEGY.md | docs/ | Go-to-market & sales |
| INDUSTRY-TERMS.md | docs/ | Industry glossary |
| INDUSTRY-RESEARCH.md | docs/ | Market research |
| COMPETITOR-ANALYSIS-BRANDED-CALLING-ID.md | docs/ | BCID ecosystem analysis |
| COMPETITOR-ANALYSIS-QUALITY-VOICE-DATA.md | docs/ | QVD analysis |
| COMPETITOR-DEEP-DIVE.md | docs/ | Comprehensive competitor report |
| COMPLIANCE.md | docs/ | Regulatory guide |
| CERTIFICATIONS.md | docs/ | KYC document requirements |
| SOC2-COMPLIANCE.md | docs/ | SOC 2 checklist |
| TCPA-VIOLATIONS-GUIDE.md | docs/ | TCPA reference |
| NUMHUB.md | docs/ | NumHub overview |
| NUMHUB-INTEGRATION.md | docs/ | API patterns |
| NUMHUB-INTEGRATION-STATUS.md | docs/ | Current status |
| NUMHUB-DATABASE-SCHEMA.md | docs/ | DB design |
| NUMHUB-DATA-MODELS.md | docs/ | API data models |
| NUMHUB-CLIENT-DESIGN.md | docs/ | Service class design |
| NUMHUB-ADMIN-DESIGN.md | docs/ | Filament admin design |
| NUMHUB-TESTING-STRATEGY.md | docs/ | Testing approach |
| NUMHUB-ALTERNATIVES.md | docs/ | Provider comparison |
| TODO-NUMHUB-INTEGRATION.md | docs/ | Integration TODO |
| MEETING-BRIEF-NUMHUB-2026-02-04.md | docs/ | Meeting prep |
| DEPLOYMENT.md | docs/ | Deployment guide |
| DEVELOPER.md | docs/ | Developer documentation |
| USER-GUIDE.md | docs/ | End-user guide |
| ECOSYSTEM.md | docs/ | Laravel package reference |
| DEEPAGENTS-N8N-COMFYUI-INTEGRATION.md | docs/ | AI automation design |

---

*This document consolidates all BrandCall documentation into a single cohesive reference. Individual documents remain in place for deep-dive reference. This master document provides the strategic overview and connects all concepts.*

*Last updated: February 9, 2026*
