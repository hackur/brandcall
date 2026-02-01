# NumHub Alternatives: Comprehensive Provider Analysis

> **BrandCall Strategic Reference Document**  
> Deep-cut analysis of branded caller ID, STIR/SHAKEN, and caller reputation providers  
> Last Updated: January 2026

---

## Table of Contents

1. [Executive Summary](#executive-summary)
2. [Tier 1: Enterprise Branded Calling Specialists](#tier-1-enterprise-branded-calling-specialists)
3. [Tier 2: CPaaS with STIR/SHAKEN & Caller ID](#tier-2-cpaas-with-stirshaken--caller-id)
4. [Tier 3: Telecom Infrastructure Providers](#tier-3-telecom-infrastructure-providers)
5. [Tier 4: Reputation & Remediation Specialists](#tier-4-reputation--remediation-specialists)
6. [Tier 5: Voice Security & Trust Platforms](#tier-5-voice-security--trust-platforms)
7. [Tier 6: Niche & Deep-Cut Providers](#tier-6-niche--deep-cut-providers)
8. [Tier 7: Carrier & Wholesale Providers](#tier-7-carrier--wholesale-providers)
9. [Selection Matrix](#selection-matrix)
10. [Integration Recommendations for BrandCall](#integration-recommendations-for-brandcall)

---

## Executive Summary

This document catalogs **35+ providers** in the branded caller ID, STIR/SHAKEN, and caller reputation ecosystem. Providers are organized by specialization and market focus, with deep-cut options for differentiated integration strategies.

**Key Insight**: The market is fragmented. Enterprise providers (Hiya, First Orion) dominate mindshare but leave gaps for:
- SMB-accessible solutions
- Developer-first APIs
- Transparent pricing
- Aggregated multi-carrier approaches

---

## Tier 1: Enterprise Branded Calling Specialists

These are the primary players most enterprises evaluate first.

### 1. Hiya

| Attribute | Details |
|-----------|---------|
| **Website** | hiya.com |
| **Focus** | Consumer spam blocking + enterprise branded calling |
| **Products** | Hiya Connect (Branded Call, Number Registration) |
| **Strengths** | 500M+ consumer app users, AI Voice Detection, free business number registration |
| **Carrier Coverage** | Verizon, T-Mobile, Samsung (no AT&T) |
| **Pricing Model** | Per-call + subscription, enterprise minimums |
| **API** | REST API available |
| **Best For** | Large enterprises with existing Hiya consumer presence |

**Deep Insight**: Hiya's consumer app install base is their moat. They see call patterns across millions of devices, giving them unparalleled spam detection. However, their enterprise products feel like an afterthought compared to their consumer focus.

---

### 2. First Orion

| Attribute | Details |
|-----------|---------|
| **Website** | firstorion.com |
| **Focus** | INFORM® branded calling, SENTRY® blocking |
| **Products** | INFORM (branding), ENRICH (messaging), AFFIRM (monitoring), SENTRY (blocking), PROTECT+ |
| **Strengths** | Only provider with AT&T coverage, 32-char display + logo, Deutsche Telekom partnership (Jan 2026) |
| **Carrier Coverage** | AT&T, Verizon, T-Mobile (most complete) |
| **Pricing Model** | Enterprise only, custom pricing |
| **API** | Available for enterprise |
| **Best For** | Large call centers needing complete carrier coverage |

**Deep Insight**: First Orion pioneered branded calling. Their AT&T exclusivity is significant—no other provider has it. The Deutsche Telekom partnership signals European expansion. However, they're strictly enterprise with high minimums.

---

### 3. TNS (Transaction Network Services)

| Attribute | Details |
|-----------|---------|
| **Website** | tnsi.com |
| **Focus** | Enterprise call authentication and branded calling |
| **Products** | Enterprise Branded Calling, Call Guardian |
| **Strengths** | Ranked #1 by Juniper Research 2025, global reach, deep carrier relationships |
| **Carrier Coverage** | Global |
| **Pricing Model** | Enterprise custom |
| **API** | Enterprise integration |
| **Best For** | Global enterprises, financial services |

**Deep Insight**: TNS is the quiet giant. They provide infrastructure that powers many other services. Their Juniper Research #1 ranking validates their enterprise positioning but they're invisible to SMBs.

---

### 4. Numeracle

| Attribute | Details |
|-----------|---------|
| **Website** | numeracle.com |
| **Focus** | Number reputation management + branded calling |
| **Products** | Number Reputation Management, Smart Branding, Number Check |
| **Strengths** | Only true aggregated multi-carrier platform, remediation expertise, white-label for service providers |
| **Carrier Coverage** | Aggregated across carriers |
| **Pricing Model** | Subscription + usage |
| **API** | Available |
| **Best For** | BPOs, contact centers, service providers wanting to resell |

**Deep Insight**: Numeracle is the "fixer"—they specialize in cleaning up number reputation after problems occur. Their aggregation approach means you don't need separate relationships with each carrier. Good partner for the white-label/reseller model.

---

### 5. TransNexus

| Attribute | Details |
|-----------|---------|
| **Website** | transnexus.com |
| **Focus** | STIR/SHAKEN + Branded Calling + Fraud Prevention |
| **Products** | ClearIP, NexOSS, BCID Authorized Signing Agent |
| **Strengths** | 25+ years telecom experience, BCID ecosystem expertise, public documentation |
| **Carrier Coverage** | Via BCID ecosystem |
| **Pricing Model** | Platform licensing + usage |
| **API** | Full REST API |
| **Best For** | Service providers, carriers, technical teams |

**Deep Insight**: TransNexus is the most technically transparent option. They publish whitepapers, have clear documentation, and their team actually picks up the phone. As a BCID Authorized Signing Agent, they can sign branded calls within the CTIA ecosystem. Strong technical partner.

---

## Tier 2: CPaaS with STIR/SHAKEN & Caller ID

Communications Platform as a Service providers with caller ID and STIR/SHAKEN capabilities.

### 6. Bandwidth

| Attribute | Details |
|-----------|---------|
| **Website** | bandwidth.com |
| **Focus** | Wholesale VoIP, Hosted Signing Service, CNAM |
| **Products** | Voice, Messaging, Numbers, Hosted Signing Service |
| **Strengths** | U.S.'s largest RespOrg, 38+ countries, API-driven porting, 99.995% uptime |
| **Pricing** | Usage-based, transparent |
| **API** | Full REST API |
| **Best For** | Developers, service providers, enterprises wanting carrier-grade |

**Deep Insight**: Bandwidth owns infrastructure (not just reselling). Their Hosted Signing Service means you can STIR/SHAKEN sign calls without running your own certificate infrastructure. Strong developer experience.

---

### 7. Twilio

| Attribute | Details |
|-----------|---------|
| **Website** | twilio.com |
| **Focus** | Communications APIs |
| **Products** | Programmable Voice, Trusted Calling, SHAKEN |
| **Strengths** | Massive developer ecosystem, documentation, flexibility |
| **Pricing** | Pay-as-you-go, usage-based |
| **API** | Industry-leading REST API |
| **Best For** | Developers, startups, custom integrations |

**Deep Insight**: Twilio is the default choice for developers but their branded calling is limited. They do STIR/SHAKEN verification but don't offer rich branded calling (name, logo, call reason) like dedicated providers.

---

### 8. Telnyx

| Attribute | Details |
|-----------|---------|
| **Website** | telnyx.com |
| **Focus** | Voice API, SIP Trunking, STIR/SHAKEN |
| **Products** | Voice API, TeXML, Mission Control |
| **Strengths** | $0.002/min starting, 140+ countries, AI voice capabilities, transparent pricing |
| **Pricing** | Usage-based, volume discounts |
| **API** | Full REST API + TeXML (TwiML compatible) |
| **Best For** | Cost-conscious developers, Twilio alternatives |

**Deep Insight**: Telnyx positions as the "better Twilio alternative" with lower pricing and their own network. Their documentation is excellent. STIR/SHAKEN support is solid but branded calling is basic.

---

### 9. Plivo

| Attribute | Details |
|-----------|---------|
| **Website** | plivo.com |
| **Focus** | Voice API, SIP Trunking |
| **Products** | Voice API, SIP Trunking, Phone Numbers |
| **Strengths** | 91% cost savings claim vs competitors, 200+ countries, dynamic caller ID |
| **Pricing** | Usage-based |
| **API** | Full REST API |
| **Best For** | Cost-conscious enterprises, international calling |

---

### 10. Sinch (owns Inteliquent)

| Attribute | Details |
|-----------|---------|
| **Website** | sinch.com |
| **Focus** | Enterprise voice, Elastic SIP Trunking |
| **Products** | Voice, Messaging, STIR/SHAKEN |
| **Strengths** | Tier-1 network, 300B minutes annually, 94.5% U.S. coverage, owns infrastructure |
| **Pricing** | Enterprise |
| **API** | Full API suite |
| **Best For** | Enterprise, wholesale voice |

**Deep Insight**: Sinch acquired Inteliquent (major U.S. voice carrier). They own the largest independent voice network in the U.S. If you need carrier-grade reliability at scale, they're a top choice.

---

### 11. SignalWire

| Attribute | Details |
|-----------|---------|
| **Website** | signalwire.com |
| **Focus** | Voice API, FreeSWITCH, AI |
| **Products** | Voice API, AI Agent, Video API |
| **Strengths** | Created by FreeSWITCH team, Twilio-compatible APIs, AI focus |
| **Pricing** | Usage-based |
| **API** | REST + FreeSWITCH |
| **Best For** | Developers wanting FreeSWITCH reliability with modern APIs |

**Deep Insight**: SignalWire was founded by the FreeSWITCH creators. If you've ever used FreeSWITCH, you know its reliability. Their AI Agent product is interesting for voice automation.

---

### 12. Infobip

| Attribute | Details |
|-----------|---------|
| **Website** | infobip.com |
| **Focus** | Omnichannel communications |
| **Products** | Voice, SMS, WhatsApp, CNAM, Branded Calling |
| **Strengths** | 100B+ minutes/year, 195 countries, 300 voice connections, STIR/SHAKEN + CNAM + Branded Calling |
| **Pricing** | Enterprise |
| **API** | Full REST API |
| **Best For** | Global enterprises, omnichannel needs |

**Deep Insight**: Infobip is massive globally but less known in U.S. They explicitly offer CNAM and Branded Calling as features—worth investigating their capabilities.

---

### 13. Vonage (Ericsson)

| Attribute | Details |
|-----------|---------|
| **Website** | vonage.com |
| **Focus** | Enterprise communications |
| **Products** | Voice API, Video, Messaging |
| **Strengths** | Ericsson backing, global presence, Salesforce integration |
| **Pricing** | Enterprise |
| **API** | Nexmo API |
| **Best For** | Enterprise Salesforce users |

---

### 14. Telesign

| Attribute | Details |
|-----------|---------|
| **Website** | telesign.com |
| **Focus** | Voice API, Number Intelligence |
| **Products** | Voice API, Phone ID, Trust Score |
| **Strengths** | 120+ PoPs worldwide, fraud prevention focus, number intelligence |
| **Pricing** | Usage-based |
| **API** | Full REST API |
| **Best For** | Fraud prevention, authentication use cases |

---

## Tier 3: Telecom Infrastructure Providers

These are the companies that carriers and service providers use.

### 15. Somos

| Attribute | Details |
|-----------|---------|
| **Website** | somos.com |
| **Focus** | Numbering administration, trust framework |
| **Products** | RealBrand, TFNRegistry, NANPA, Reassigned Numbers Database |
| **Strengths** | FCC-appointed toll-free registry, Reassigned Numbers Database, industry authority |
| **Pricing** | Enterprise/carrier |
| **Best For** | Service providers, carriers, compliance |

**Deep Insight**: Somos runs the toll-free number system in North America. Their RealBrand product focuses on number identity and reputation. They're the ultimate authority on phone numbers.

---

### 16. iconectiv

| Attribute | Details |
|-----------|---------|
| **Website** | iconectiv.com |
| **Focus** | Telecom infrastructure, caller ID |
| **Products** | Calling Number Verification Service, NPAC, Number Portability |
| **Strengths** | Industry standard-setter, NPAC administrator |
| **Pricing** | Enterprise/carrier |
| **Best For** | Carriers, large service providers |

**Deep Insight**: iconectiv is telecom infrastructure. They run the Number Portability Administration Center (NPAC). If you're building carrier-grade services, you'll eventually talk to them.

---

### 17. Ribbon Communications

| Attribute | Details |
|-----------|---------|
| **Website** | ribboncommunications.com |
| **Focus** | Enterprise voice security, SBCs |
| **Products** | Call Trust Services, STI-CA, Session Border Controllers |
| **Strengths** | Enterprise-grade, hardware + software, global |
| **Pricing** | Enterprise |
| **Best For** | Enterprises with on-prem voice infrastructure |

---

### 18. Peerless Network

| Attribute | Details |
|-----------|---------|
| **Website** | peerlessnetwork.com |
| **Focus** | Carrier voice services, STIR/SHAKEN |
| **Products** | SIP Trunking, STIR/SHAKEN, Peerless CallTrue |
| **Strengths** | Direct carrier, CallTrue IVR for robocall mitigation (beyond STIR/SHAKEN) |
| **Pricing** | Carrier/wholesale |
| **Best For** | Service providers, ITSPs |

**Deep Insight**: Peerless CallTrue is interesting—it goes beyond STIR/SHAKEN to actually verify callers are human using IVR prompts. Defense in depth.

---

### 19. Sansay

| Attribute | Details |
|-----------|---------|
| **Website** | sansay.com |
| **Focus** | Session Border Controllers, STIR/SHAKEN |
| **Products** | VSXi SBC, STIR/SHAKEN, Branded Calling, DNO |
| **Strengths** | Comprehensive SBC platform, STIR/SHAKEN Express, branded calling built-in |
| **Pricing** | Platform licensing |
| **Best For** | Service providers building their own infrastructure |

**Deep Insight**: Sansay is a deep-cut for service providers who want to run their own STIR/SHAKEN and branded calling infrastructure. Their STIR/SHAKEN Express is a turnkey solution.

---

## Tier 4: Reputation & Remediation Specialists

These focus specifically on number reputation monitoring and fixing.

### 20. Caller ID Reputation

| Attribute | Details |
|-----------|---------|
| **Website** | calleridreputation.com |
| **Focus** | Phone number reputation monitoring |
| **Products** | Reputation Monitoring, Remediation Dashboard |
| **Strengths** | 95% flag reduction claim, simplified dashboard, affordable |
| **Pricing** | Subscription |
| **Best For** | SMBs, contact centers wanting simple monitoring |

**Deep Insight**: This is a focused tool—just reputation monitoring and remediation. If you just need to know when numbers get flagged and help fixing them, this is simpler than full platforms.

---

### 21. TrueCNAM

| Attribute | Details |
|-----------|---------|
| **Website** | truecnam.com |
| **Focus** | Spam scoring API |
| **Products** | TrueSpam Scores |
| **Strengths** | Real-time spam scoring, multiple data sources |
| **Pricing** | API usage |
| **Best For** | Carriers, service providers, developers |

**Deep Insight**: TrueCNAM provides spam scores as a service. If you're building your own calling platform and need to know which inbound calls are likely spam, their API delivers real-time scores.

---

### 22. Nomorobo

| Attribute | Details |
|-----------|---------|
| **Website** | nomorobo.com |
| **Focus** | Spam blocking, reputation API |
| **Products** | Nomorobo API, Teams, White Label |
| **Strengths** | 4B+ robocalls stopped, 350K+ honeypot numbers, 8M+ consumer users |
| **Pricing** | API usage + enterprise |
| **Best For** | Reputation data, spam detection |

**Deep Insight**: Nomorobo started as a consumer robocall blocker and built a massive database. Their API provides phone number reputation data based on real-world consumer complaints and honeypot data. Useful for inbound call scoring.

---

## Tier 5: Voice Security & Trust Platforms

These focus on securing voice channels and building call trust.

### 23. SecureLogix

| Attribute | Details |
|-----------|---------|
| **Website** | securelogix.com |
| **Focus** | Call security and trust |
| **Products** | Contact (branded calling), TrueCall (spoofing protection), Reputation Defense |
| **Strengths** | 30%+ answer rate increase, DHS/DoD funded research, unified inbound/outbound platform |
| **Pricing** | Enterprise |
| **Best For** | Enterprises wanting unified call security + branding |

**Deep Insight**: SecureLogix combines outbound call branding with inbound call security. Their TrueCall product identifies and blocks calls spoofing your corporate numbers. Strong for enterprises concerned about brand impersonation.

---

### 24. Mutare

| Attribute | Details |
|-----------|---------|
| **Website** | mutare.com |
| **Focus** | Enterprise voice security |
| **Products** | Voice Traffic Filter, Vishing Protection |
| **Strengths** | Multi-layered protection, vishing defense, enterprise integrations |
| **Pricing** | Enterprise |
| **Best For** | Enterprises concerned about vishing attacks |

**Deep Insight**: Mutare integrates Nomorobo's data into their voice security platform. Focus is on protecting enterprises from inbound threats (vishing, spam, TDoS). Complements outbound branding with inbound protection.

---

## Tier 6: Niche & Deep-Cut Providers

These are lesser-known options for specific use cases.

### 25. BulkVS (Bulk Solutions)

| Attribute | Details |
|-----------|---------|
| **Website** | bulkvs.com |
| **Focus** | Wholesale CNAM, E911, Voice |
| **Products** | CNAM updates, E911, Inbound/Outbound Voice |
| **Strengths** | Extremely low pricing ($0.0003/min inbound), SOMOS partner, CNAM updates |
| **Pricing** | Usage-based, wholesale |
| **Best For** | Cost-conscious service providers, ITSPs |

**Deep Insight**: BulkVS is a deep-cut for wholesale voice and CNAM. Their pricing is aggressively low. If you're building a platform and need cheap CNAM updates, they're worth evaluating.

---

### 26. Sangoma Carrier Services (formerly VoIP Innovations)

| Attribute | Details |
|-----------|---------|
| **Website** | carrierservices.sangoma.com |
| **Focus** | Wholesale SIP Trunking, CPaaS |
| **Products** | Voice, Messaging, E911, Hosted Billing |
| **Strengths** | CLEC, 24/7 monitoring, white-label billing portal |
| **Pricing** | Wholesale |
| **Best For** | ITSPs, MSPs wanting turnkey reseller platform |

**Deep Insight**: Sangoma acquired VoIP Innovations. They offer a complete turnkey solution for starting your own VoIP business—including billing and end-user portals. Interesting for BrandCall's reseller model.

---

### 27. Sipharmony

| Attribute | Details |
|-----------|---------|
| **Website** | sipharmony.com |
| **Focus** | Enterprise SIP Trunking |
| **Products** | SIP Trunking, Unified Messaging, Video |
| **Strengths** | FCC-compliant KYC, 100+ countries, enterprise security |
| **Pricing** | Enterprise |
| **Best For** | Enterprises wanting compliant onboarding |

---

### 28. Flowroute (now Bandwidth)

| Attribute | Details |
|-----------|---------|
| **Website** | flowroute.com |
| **Focus** | Cloud voice, SIP Trunking |
| **Products** | Inbound/Outbound SIP, Numbers, Porting |
| **Strengths** | Unlimited concurrent call capacity, 112 countries |
| **Pricing** | Usage-based |
| **Best For** | Developers wanting simple SIP trunking |

---

### 29. Telgorithm

| Attribute | Details |
|-----------|---------|
| **Website** | telgorithm.com |
| **Focus** | 10DLC messaging (SMS/MMS) |
| **Products** | 10DLC registration, messaging API |
| **Strengths** | 24-hour campaign approvals, 95% first-attempt approval, patented deliverability |
| **Pricing** | Usage-based |
| **Best For** | Platforms needing 10DLC messaging compliance |

**Deep Insight**: While not voice-focused, Telgorithm is interesting for BrandCall if we add SMS capabilities. They specialize in 10DLC registration which is notoriously painful.

---

## Tier 7: Carrier & Wholesale Providers

Direct carrier relationships for large-scale deployments.

### 30. Lumen (formerly CenturyLink)

| Attribute | Details |
|-----------|---------|
| **Focus** | Enterprise voice, Cloud Voice |
| **Strengths** | Tier-1 carrier, global network |
| **Best For** | Large enterprises |

---

### 31. Verizon Business

| Attribute | Details |
|-----------|---------|
| **Focus** | Enterprise voice |
| **Strengths** | Carrier-direct, integrated with Verizon network |
| **Best For** | Large enterprises on Verizon |

---

### 32. AT&T Business

| Attribute | Details |
|-----------|---------|
| **Focus** | Enterprise voice |
| **Strengths** | Carrier-direct |
| **Best For** | Large enterprises on AT&T |

---

### 33. T-Mobile for Business

| Attribute | Details |
|-----------|---------|
| **Focus** | Business voice |
| **Strengths** | 5G network, business plans |
| **Best For** | Mobile-first businesses |

---

### 34. Nextiva

| Attribute | Details |
|-----------|---------|
| **Website** | nextiva.com |
| **Focus** | Business VoIP, UCaaS |
| **Products** | VoIP Phone Service, Contact Center |
| **Strengths** | 99.999% uptime, 92% customer recommendation, easy setup |
| **Pricing** | $25-75/user/month |
| **Best For** | SMBs wanting turnkey VoIP |

---

### 35. CallFire

| Attribute | Details |
|-----------|---------|
| **Website** | callfire.com |
| **Focus** | Voice broadcast, IVR |
| **Products** | Voice Broadcast, IVR, Call Tracking |
| **Strengths** | 4B+ messages delivered, simple interface |
| **Pricing** | Usage-based |
| **Best For** | Voice broadcasting, notifications |

---

## Selection Matrix

### By Use Case

| Use Case | Recommended Providers |
|----------|----------------------|
| **Full branded calling (name, logo, reason)** | TransNexus, First Orion, Hiya, Numeracle |
| **STIR/SHAKEN only** | Bandwidth, Telnyx, Twilio, Sansay |
| **Reputation monitoring** | Caller ID Reputation, Numeracle, SecureLogix |
| **Remediation** | Numeracle, SecureLogix, Caller ID Reputation |
| **Developer-first API** | Bandwidth, Telnyx, Twilio, Plivo |
| **White-label/reseller** | Numeracle, Sangoma, TransNexus |
| **Spam scoring API** | TrueCNAM, Nomorobo, Hiya |
| **Voice security** | SecureLogix, Mutare |
| **Lowest cost** | BulkVS, Telnyx, Plivo |
| **Carrier-grade** | Sinch, Bandwidth, Peerless |

### By Budget

| Budget | Providers |
|--------|-----------|
| **$0-500/mo** | BulkVS, Telnyx, Plivo, Caller ID Reputation |
| **$500-5000/mo** | Bandwidth, TransNexus, Numeracle |
| **$5000+/mo** | Hiya, First Orion, TNS, SecureLogix |

### By Technical Capability

| Capability | Low (No Dev) | Medium (Some Dev) | High (Full Dev) |
|------------|--------------|-------------------|-----------------|
| **Provider** | First Orion, Nextiva | Numeracle, Bandwidth | Telnyx, Twilio, Sansay |

---

## Integration Recommendations for BrandCall

### Primary Integration Candidates

1. **TransNexus** - Best documentation, BCID authorized, technical team responsive
2. **Bandwidth** - Strong API, Hosted Signing Service, reasonable pricing
3. **Numeracle** - Aggregated approach aligns with our multi-carrier vision
4. **Telnyx** - Developer experience, transparent pricing, good fallback

### Secondary / Specialized

1. **SecureLogix** - Add if customers need spoofing protection
2. **Caller ID Reputation** - Simple reputation monitoring add-on
3. **TrueCNAM** - Spam scoring API for inbound call filtering

### Data Enrichment

1. **Nomorobo** - Phone number reputation data
2. **Somos** - Reassigned Numbers Database check
3. **TrueCNAM** - Real-time spam scores

### Recommended Approach

1. **Start with TransNexus or Numeracle** for core branded calling
2. **Add Bandwidth** for STIR/SHAKEN signing if not included
3. **Integrate Nomorobo or TrueCNAM** for reputation data
4. **Consider SecureLogix** for customers with security concerns

---

## Contact Information

| Provider | Contact Method |
|----------|---------------|
| TransNexus | +1 (855) 4SHAKEN, transnexus.com/contact |
| Numeracle | numeracle.com/contact |
| Bandwidth | bandwidth.com/contact |
| Hiya | hiya.com/contact |
| First Orion | +1 (501) 358-4061 |
| SecureLogix | securelogix.com/contact |
| Telnyx | telnyx.com/contact |

---

## Appendix: BCID Ecosystem Participants

The CTIA Branded Calling ID (BCID) ecosystem includes authorized partners:

- **Signing Agents**: TransNexus, (others under NDA)
- **Authorized Partners**: Vetted and audited for trust
- **Terminating Service Providers**: Display branded information to subscribers

To participate, BrandCall would need to either:
1. Partner with an Authorized Signing Agent (TransNexus)
2. Become an Authorized Partner (rigorous vetting process)

---

*Document maintained by BrandCall Strategy Team*
