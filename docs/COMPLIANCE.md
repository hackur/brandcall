# BrandCall Regulatory Compliance Guide

> **Last Updated:** January 31, 2026  
> **Version:** 1.0  
> **Status:** CRITICAL - Review with Legal Counsel Before Launch

This document outlines the regulatory requirements, compliance obligations, and best practices for operating BrandCall, a branded caller ID service. **This is not legal advice** - consult with telecommunications and privacy attorneys before implementation.

---

## Table of Contents

1. [Executive Summary](#executive-summary)
2. [STIR/SHAKEN Requirements](#stirshaken-requirements)
3. [TCPA Compliance](#tcpa-compliance)
4. [Do Not Call Registry](#do-not-call-registry)
5. [FCC Caller ID Rules](#fcc-caller-id-rules)
6. [State-Specific Regulations](#state-specific-regulations)
7. [Industry-Specific Requirements](#industry-specific-requirements)
8. [Enforcement & Penalties](#enforcement--penalties)
9. [Compliance Checklist](#compliance-checklist)
10. [Customer Agreements & Attestations](#customer-agreements--attestations)
11. [Call Recording & Logging](#call-recording--logging)
12. [Implementation Recommendations](#implementation-recommendations)

---

## Executive Summary

### Critical Compliance Areas for BrandCall

| Area | Risk Level | Key Requirement |
|------|------------|-----------------|
| STIR/SHAKEN | 🔴 HIGH | Must implement call authentication |
| TCPA | 🔴 HIGH | Prior express consent required for many calls |
| Caller ID Spoofing | 🔴 HIGH | Cannot transmit misleading/inaccurate caller ID with intent to defraud |
| Do Not Call | 🟠 MEDIUM | Must honor DNC list scrubbing |
| State Laws | 🟠 MEDIUM | Various state-specific requirements |
| Healthcare (HIPAA) | 🔴 HIGH | PHI protection in call context |
| Financial (GLBA/PCI) | 🔴 HIGH | Financial data protection requirements |

### BrandCall's Legal Position

BrandCall is a **technology platform** that enables businesses to display branded caller ID information. Our customers are the **callers** who bear primary responsibility for compliance with calling regulations. However, as a service provider, BrandCall:

1. **Must not facilitate illegal caller ID spoofing**
2. **Must implement STIR/SHAKEN authentication**
3. **Must require customer attestations and compliance agreements**
4. **Must maintain records for regulatory compliance**
5. **May face secondary liability for facilitating violations**

---

## STIR/SHAKEN Requirements

### Overview

STIR/SHAKEN (Secure Telephone Identity Revisited / Signature-based Handling of Asserted information using toKENs) is an FCC-mandated caller ID authentication framework designed to combat illegal caller ID spoofing.

### Current Requirements & Deadlines

| Deadline | Requirement | Status |
|----------|-------------|--------|
| June 30, 2021 | Large voice service providers must implement STIR/SHAKEN | ✅ Passed |
| June 30, 2022 | Small providers with <100K lines must file robocall mitigation plan | ✅ Passed |
| June 30, 2023 | All originating voice service providers must implement | ✅ Passed |
| Ongoing | All voice service providers must be in FCC Robocall Mitigation Database | 🔄 Active |

### Attestation Levels

| Level | Description | When to Use |
|-------|-------------|-------------|
| **A (Full)** | Carrier can verify caller identity AND that they're authorized to use the calling number | BrandCall should ensure customers can achieve this |
| **B (Partial)** | Carrier verified customer identity but NOT authorization to use the number | Requires additional verification |
| **C (Gateway)** | Call originated from gateway; carrier cannot verify | Higher fraud risk; may be blocked |

### BrandCall Requirements

1. **Work with STIR/SHAKEN-compliant carriers**
   - Ensure upstream providers support full attestation
   - Verify SIP trunking partners are registered in FCC database

2. **Verify caller authorization for numbers**
   - Customers must prove ownership/authorization of displayed numbers
   - Document verification process

3. **Maintain attestation records**
   - Track which attestation level applies to each call
   - Retain records for minimum 2 years

### Implementation Actions

- [ ] Verify carrier partners are STIR/SHAKEN compliant
- [ ] Implement number ownership verification workflow
- [ ] Create attestation tracking system
- [ ] Register in FCC Robocall Mitigation Database if required
- [ ] Document STIR/SHAKEN policies in Terms of Service

---

## TCPA Compliance

### Telephone Consumer Protection Act (47 U.S.C. § 227)

The TCPA regulates telemarketing calls, auto-dialed calls, prerecorded messages, and unsolicited faxes.

### Key Prohibitions

1. **Autodialed Calls to Cell Phones**
   - Requires **prior express consent** for non-emergency calls
   - For telemarketing: requires **prior express written consent**

2. **Prerecorded Voice Messages**
   - Residential lines: Generally prohibited without consent
   - Cell phones: Prohibited without prior express consent
   - Must include opt-out mechanism

3. **Calling Time Restrictions**
   - Calls only permitted between **8:00 AM and 9:00 PM** (recipient's local time)
   - Some states have stricter windows

4. **Call Abandonment**
   - Abandoned calls limited to 3% of answered calls per campaign per 30 days
   - Must have live operator within 2 seconds

### Consent Requirements

| Call Type | Consent Level Required |
|-----------|----------------------|
| Informational (non-marketing) | Prior express consent |
| Telemarketing to landline | Prior express consent |
| Telemarketing to cell phone | **Prior express WRITTEN consent** |
| Emergency calls | No consent required |
| Healthcare appointment reminders | Prior express consent |
| Debt collection | Prior express consent (with exceptions) |

### Written Consent Requirements (47 CFR § 64.1200)

Written consent must include:
- Clear and conspicuous disclosure
- Agreement signature (electronic acceptable)
- Telephone number to receive calls
- Understanding that consent is not required as condition of purchase
- Identification of seller and specific goods/services

### BrandCall Customer Requirements

Customers using BrandCall must:
1. Obtain appropriate consent before placing calls
2. Honor opt-out requests within 30 days
3. Maintain consent records for 5 years
4. Comply with calling time restrictions
5. Transmit accurate caller ID information

---

## Do Not Call Registry

### National DNC Registry Requirements

#### For BrandCall Customers

| Requirement | Details |
|-------------|---------|
| Registry Access | Must access National DNC Registry before calling |
| Update Frequency | Must update list at least every 31 days |
| Safe Harbor | 5-day safe harbor after number added to registry |
| Record Keeping | Maintain DNC list access records |
| Entity-Specific DNC | Must maintain own internal DNC list |

#### Exemptions from National DNC

- Calls with **established business relationship** (18 months from last transaction, 3 months from inquiry)
- Calls with **prior express written permission**
- Non-commercial calls
- Political calls
- Charitable solicitations (partial exemption)
- B2B calls (except nondurable office supplies)
- Survey calls (without sales pitch)

#### Penalties for DNC Violations

- Up to **$51,744 per violation** (adjusted for inflation)
- State attorneys general can also enforce
- Private right of action available in some cases

### BrandCall Requirements

1. **Customer Attestation**: Require customers to attest DNC compliance
2. **Integration Option**: Consider offering DNC list scrubbing as a feature
3. **Record Keeping**: Log customer acknowledgments of DNC obligations
4. **Terms of Service**: Explicitly require DNC compliance

---

## FCC Caller ID Rules

### Truth in Caller ID Act (47 U.S.C. § 227(e))

#### Prohibition on Spoofing

> "It shall be unlawful for any person within the United States, or any person outside the United States if the recipient is within the United States, in connection with any voice service or text messaging service, to cause any caller identification service to knowingly transmit misleading or inaccurate caller identification information **with the intent to defraud, cause harm, or wrongfully obtain anything of value.**"

### Key Points

1. **Intent matters**: Displaying non-assigned numbers is only illegal if done with intent to defraud/harm
2. **Legitimate purposes protected**: Display of a business's main line (even if call originates elsewhere) may be legitimate
3. **Applies to text messages**: As of 2018 amendments, rules apply to text messaging services

### When Branded Caller ID is LEGAL

✅ **Legitimate business purposes:**
- Displaying company main number for callbacks
- Displaying department-specific numbers
- Displaying toll-free customer service numbers
- Displaying legitimate branding associated with the business

### When Branded Caller ID is ILLEGAL

❌ **Prohibited purposes:**
- Impersonating government agencies
- Impersonating financial institutions to phish
- Disguising identity to avoid law enforcement
- Making calls appear to originate from the recipient's area code to deceive
- Any transmission with **intent to defraud, cause harm, or wrongfully obtain anything of value**

### BrandCall Safeguards Required

1. **Number Ownership Verification**
   - Verify customers own or are authorized to use displayed numbers
   - Document verification process

2. **Business Legitimacy Verification**
   - Verify business registration/incorporation
   - Check against known fraud patterns

3. **Prohibited Use Policies**
   - Explicit prohibition on fraudulent use in Terms of Service
   - Right to terminate service for violations

4. **Monitoring & Reporting**
   - Implement abuse detection systems
   - Respond to law enforcement requests
   - Report suspected fraud

---

## State-Specific Regulations

### California

#### California Consumer Privacy Act (CCPA) / CPRA
- Applies if business meets revenue/data thresholds
- Consumers can request data about calls made to them
- Disclosure requirements for data collection

#### California Penal Code § 528.5
- Prohibits impersonation with intent to harm
- Can apply to caller ID manipulation

#### California Business & Professions Code § 17538.41
- Telemarketing restrictions
- Caller ID display requirements

### Florida

#### Florida Telephone Solicitation Act (§ 501.059)
- Registration required for telemarketers
- Stricter calling hour restrictions (varies by county)
- Written contract requirements
- Bond/insurance requirements for some callers

#### Florida Telemarketing Act (§ 501.604)
- Additional restrictions on caller ID display
- Enhanced penalties for spoofing

### Texas

#### Texas Business & Commerce Code Chapter 302
- Do Not Call provisions
- Telemarketing registration requirements
- Caller ID display requirements

#### Texas Health & Safety Code (HIPAA-adjacent)
- Additional protections for health information
- May affect healthcare calling

### Multi-State Considerations

| State | Special Requirement |
|-------|-------------------|
| **New York** | Stricter telemarketing registration |
| **Georgia** | Additional caller ID requirements |
| **Illinois** | Biometric/voice recording consent (BIPA) |
| **Washington** | Automatic renewal disclosure requirements |
| **Massachusetts** | Stricter DNC and calling time rules |

### BrandCall Recommendation

- Default to most restrictive state requirements
- Consider geo-based compliance rules
- Track changing state regulations
- Require customers to attest to state law compliance

---

## Industry-Specific Requirements

### Healthcare (HIPAA)

#### Overview

The Health Insurance Portability and Accountability Act (HIPAA) protects Protected Health Information (PHI).

#### Caller ID Considerations

| Risk Area | Mitigation |
|-----------|------------|
| Caller ID revealing healthcare relationship | Use generic company names, not specialty names |
| Voicemail messages with PHI | Limit information in automated messages |
| Third-party disclosure | Ensure caller ID doesn't disclose patient status |
| Business Associate Agreements | Required if BrandCall handles PHI |

#### HIPAA Safeguards for Calling

1. **Minimum Necessary Standard**: Only include necessary information
2. **Patient Authorization**: May need for certain call types
3. **Accounting of Disclosures**: Track calls containing PHI
4. **Business Associate Agreement**: If BrandCall processes PHI

#### BrandCall Healthcare Requirements

- [ ] Do NOT include medical specialty in branded caller ID (e.g., "Acme Oncology" → "Acme Healthcare")
- [ ] Execute BAA with healthcare customers if PHI transmitted
- [ ] Train customers on HIPAA-compliant calling
- [ ] Recommend against leaving detailed voicemails

### Financial Services

#### Gramm-Leach-Bliley Act (GLBA)

- Requires financial institutions to protect consumer financial information
- Impacts how caller ID displays financial institution names
- Safeguards Rule requirements

#### Fair Debt Collection Practices Act (FDCPA)

For debt collection calls:
- Cannot disguise identity to deceive
- Must identify as debt collector in initial communication
- Caller ID must allow consumer to identify caller
- Cannot use false, deceptive representations

#### PCI-DSS Considerations

If calls involve payment card data:
- Secure transmission requirements
- Recording limitations for card data
- Call center compliance requirements

#### Truth in Lending Act (TILA) / Regulation Z

- Specific disclosures required for credit offerings
- May impact automated call scripts

#### BrandCall Financial Services Requirements

- [ ] Verify customer compliance with GLBA
- [ ] Special terms for debt collectors
- [ ] Prohibit deceptive financial caller ID
- [ ] Enhanced verification for financial institutions

### Debt Collection

#### Special FDCPA Rules

| Requirement | Description |
|-------------|-------------|
| Identify as collector | Cannot disguise as something else |
| Meaningful disclosure | Caller ID must allow identification |
| No harassment | Limits on call frequency |
| Validation notice | Required within 5 days |

---

## Enforcement & Penalties

### FCC Enforcement

| Violation | Penalty Range |
|-----------|--------------|
| Caller ID spoofing (per violation) | Up to **$10,000** |
| Pattern of violations | Up to **$1,000,000** |
| TCPA violations | Up to **$500 - $1,500 per call** |
| Robocall violations | Up to **$10,000 per call** (TRACED Act) |
| Failure to honor DNC | Up to **$51,744 per call** |

### Recent FCC Enforcement Actions (Examples)

1. **Rising Eagle/JSquared (2020)**: $225 million proposed fine for 1 billion illegal robocalls
2. **Various spoofing cases**: Millions in proposed fines for caller ID manipulation
3. **Health insurance scams**: Multi-million dollar enforcement actions

### Private Right of Action

Under TCPA § 227(b)(3):
- $500 per violation
- Up to $1,500 per willful violation
- Class action lawsuits common

### State Enforcement

- State attorneys general can enforce TCPA
- State-specific penalties may apply
- Consumer protection divisions active

### Platform Liability Concerns

As a platform, BrandCall could face:
1. **FCC enforcement** for facilitating spoofing
2. **State AG actions** for consumer protection violations
3. **Secondary liability** in private lawsuits
4. **Reputational damage** from association with bad actors

---

## Compliance Checklist

### Pre-Launch Compliance

#### Legal & Documentation
- [ ] Engage telecommunications attorney
- [ ] Review Terms of Service with counsel
- [ ] Create customer compliance agreements
- [ ] Establish Business Associate Agreement template (HIPAA)
- [ ] Create privacy policy compliant with state laws

#### Technical Requirements
- [ ] Verify STIR/SHAKEN compliance with carriers
- [ ] Implement number ownership verification
- [ ] Build customer attestation workflow
- [ ] Create call logging system (2+ year retention)
- [ ] Implement abuse detection systems
- [ ] Set up DNC integration capability

#### Customer Onboarding
- [ ] Create compliance training materials
- [ ] Build attestation/agreement signing flow
- [ ] Implement business verification process
- [ ] Create industry-specific onboarding (healthcare, financial)

### Ongoing Compliance

#### Regular Activities
- [ ] Monthly review of abuse reports
- [ ] Quarterly compliance policy review
- [ ] Annual legal/regulatory update
- [ ] Track FCC rulemaking and enforcement
- [ ] Monitor state law changes

#### Record Keeping
- [ ] Customer attestations (retain 5 years)
- [ ] Call metadata (retain 2+ years)
- [ ] Abuse reports and resolutions
- [ ] Compliance training records
- [ ] Number verification documentation

---

## Customer Agreements & Attestations

### Required Customer Representations

Every BrandCall customer must attest to:

#### General Compliance Attestation

```
By using BrandCall services, Customer represents and warrants that:

1. CALLER ID ACCURACY: Customer owns or has explicit authorization 
   to display the telephone number(s) and business name(s) configured 
   in BrandCall.

2. NO FRAUDULENT INTENT: Customer will not use BrandCall to transmit 
   caller identification information with intent to defraud, cause harm, 
   or wrongfully obtain anything of value.

3. TCPA COMPLIANCE: Customer has obtained all required consents before 
   placing calls, including prior express written consent where required 
   for telemarketing to wireless numbers.

4. DO NOT CALL COMPLIANCE: Customer accesses and honors the National 
   Do Not Call Registry and maintains an internal Do Not Call list.

5. CALLING TIME COMPLIANCE: Customer will only place calls between 
   8:00 AM and 9:00 PM in the recipient's local time zone.

6. STATE LAW COMPLIANCE: Customer will comply with all applicable 
   state telemarketing and calling regulations.

7. INDUSTRY REGULATIONS: If Customer operates in healthcare, financial 
   services, or other regulated industries, Customer will comply with 
   all applicable industry-specific regulations (HIPAA, GLBA, FDCPA, etc.).

8. ACCURATE INFORMATION: All information provided to BrandCall is 
   accurate and complete.
```

#### Number Authorization Attestation

```
For each telephone number configured for display, Customer certifies:

☐ Customer owns this telephone number outright
  -OR-
☐ Customer has written authorization from the number owner to 
  display this number as outbound caller ID

Customer will provide documentation upon BrandCall's request.
```

#### Industry-Specific Attestations

**Healthcare:**
```
Customer certifies compliance with HIPAA Privacy and Security Rules, 
and agrees to execute a Business Associate Agreement if required. 
Customer will not configure caller ID that discloses PHI or 
patient healthcare relationships.
```

**Financial Services:**
```
Customer certifies compliance with GLBA, FDCPA, and applicable 
financial regulations. Customer will not use caller ID to deceive 
consumers about the nature of calls.
```

**Debt Collection:**
```
Customer will comply with FDCPA requirements, including meaningful 
disclosure of caller identity. Customer will not use caller ID to 
disguise the nature of debt collection calls.
```

---

## Call Recording & Logging

### Regulatory Requirements

| Regulation | Recording Requirement |
|------------|----------------------|
| FCC | Call detail records recommended (2 years) |
| TCPA | Consent records required |
| PCI-DSS | Cannot record full card numbers |
| State Laws | Vary - some require all-party consent |

### BrandCall Logging Requirements

#### Must Log
- Caller (customer) ID
- Called number
- Date and time
- Caller ID displayed
- Call duration
- Attestation level (STIR/SHAKEN)

#### Recommended Logging
- Campaign/account identifier
- Consent type indicated
- DNC check confirmation
- Geographic region of recipient

### Retention Requirements

| Data Type | Minimum Retention |
|-----------|-------------------|
| Call metadata | 2 years |
| Customer attestations | 5 years |
| Consent records | 5 years after last contact |
| Abuse reports | 3 years |
| Business verification docs | Duration of relationship + 2 years |

### Recording Consent Laws

**Two-Party Consent States** (requires all parties to consent):
- California, Connecticut, Florida, Illinois, Maryland, Massachusetts, 
  Michigan, Montana, New Hampshire, Oregon, Pennsylvania, Washington

**One-Party Consent States** (only one party needs to consent):
- All other states

**Recommendation**: Obtain all-party consent to avoid state law variations.

---

## Implementation Recommendations

### Immediate Actions (Pre-Launch)

1. **Legal Review**
   - Engage telecommunications attorney
   - Have Terms of Service reviewed
   - Create compliance framework document

2. **Technical Implementation**
   - Verify STIR/SHAKEN with carrier partners
   - Build number verification system
   - Implement call logging infrastructure
   - Create customer attestation workflow

3. **Documentation**
   - Create customer-facing compliance guide
   - Develop industry-specific addendums
   - Build training materials

### Short-Term Actions (First 90 Days)

1. **Customer Onboarding Process**
   - Implement KYB (Know Your Business) verification
   - Require signed compliance agreements
   - Verify number ownership documentation

2. **Monitoring Systems**
   - Deploy abuse detection
   - Set up complaint handling process
   - Create escalation procedures

3. **Compliance Training**
   - Train support staff on compliance issues
   - Create FAQ for common questions
   - Establish compliance hotline/contact

### Ongoing Actions

1. **Regular Compliance Reviews**
   - Monthly abuse reports
   - Quarterly policy reviews
   - Annual legal updates

2. **Regulatory Monitoring**
   - Track FCC proceedings
   - Monitor state law changes
   - Watch enforcement actions

3. **Customer Communication**
   - Regular compliance newsletters
   - Updates on regulatory changes
   - Best practices guides

### Risk Mitigation Strategies

| Risk | Mitigation |
|------|------------|
| Fraudulent customers | Strong KYB, number verification, monitoring |
| Platform liability | Clear Terms of Service, customer attestations |
| Regulatory changes | Ongoing legal monitoring, flexible systems |
| State law variations | Default to strictest requirements |
| Enforcement actions | Documented compliance, cooperation policy |

---

## Appendices

### A. Relevant Statutes & Regulations

- 47 U.S.C. § 227 (TCPA)
- 47 U.S.C. § 227(e) (Truth in Caller ID)
- 47 CFR § 64.1200 (FCC TCPA Rules)
- 16 CFR Part 310 (FTC Telemarketing Sales Rule)
- TRACED Act (Pub. L. 116-105)
- HIPAA (42 U.S.C. § 1320d et seq.)
- GLBA (15 U.S.C. § 6801 et seq.)
- FDCPA (15 U.S.C. § 1692 et seq.)

### B. Regulatory Contacts

- **FCC Consumer Complaint Center**: consumercomplaints.fcc.gov
- **FTC Do Not Call**: donotcall.gov
- **FCC Robocall Mitigation Database**: fcc.gov/robocall-mitigation-database
- **State AG Offices**: naag.org/find-my-ag/

### C. Industry Resources

- STIR/SHAKEN information: atis.org
- Robocall Mitigation: Industry Traceback Group
- Caller ID authentication: TransNexus, iconectiv

---

## Document History

| Version | Date | Changes |
|---------|------|---------|
| 1.0 | 2026-01-31 | Initial compliance guide |

---

## Disclaimer

**This document is for informational purposes only and does not constitute legal advice.** BrandCall and its operators should consult with qualified telecommunications attorneys and compliance professionals before launching services. Regulatory requirements change frequently; this document should be reviewed and updated regularly.

Compliance with these regulations is ultimately the responsibility of BrandCall's customers, but BrandCall must implement appropriate safeguards, customer agreements, and monitoring to avoid platform liability and maintain the trust of regulators and the public.
