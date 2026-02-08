# BrandCall Certification Requirements

> Complete guide to all verification documents and certifications required for BCID onboarding.

**Last Updated:** 2026-02-05  
**Version:** 1.0

---

## Table of Contents

1. [Overview](#overview)
2. [Document Categories](#document-categories)
3. [Business Verification](#1-business-verification)
4. [Identity Verification](#2-identity-verification)
5. [Phone Number Authorization](#3-phone-number-authorization)
6. [Brand Assets](#4-brand-assets)
7. [Industry-Specific Requirements](#5-industry-specific-requirements)
8. [Document Storage Strategy](#document-storage-strategy)
9. [NumHub API Document Support](#numhub-api-document-support)
10. [Compliance Checklist](#compliance-checklist)

---

## Overview

### Why Certifications Are Required

Branded Calling ID (BCID) is a regulated ecosystem governed by the CTIA. To prevent fraud and protect consumers, all businesses must complete Know Your Customer (KYC) verification before displaying branded caller ID information.

### Certification Levels

| Level | Requirements | Features Unlocked |
|-------|--------------|-------------------|
| **Basic** | Business License, Tax ID | Account creation, dashboard access |
| **Verified** | + Identity verification | Brand profile creation |
| **Full** | + Phone LOA, Logo | Live branded calling |
| **Enterprise** | + Additional vetting | Multi-brand, white-label |

### NumHub Vetting Timeline

- **Standard review:** 24-48 business hours
- **Complex cases:** 3-5 business days
- **Expedited (additional fee):** Same-day

---

## Document Categories

### Required for All Customers

| Document | Purpose | Format |
|----------|---------|--------|
| Business License | Verify legal business entity | PDF, JPG, PNG |
| Tax ID (EIN/W-9) | Verify tax registration | PDF, JPG, PNG |
| Government ID | Verify authorized representative | PDF, JPG, PNG |
| Phone Number LOA | Prove number ownership/authorization | PDF only |
| Brand Logo | Display on recipient phones | BMP (200x200px) |

### Optional / Situational

| Document | When Required | Format |
|----------|---------------|--------|
| Driver's License | Alternative to Government ID | PDF, JPG, PNG |
| Articles of Incorporation | Corporations, LLCs | PDF only |
| Utility Bill | Address verification | PDF, JPG, PNG |
| W-9 Form | US tax compliance | PDF only |
| HIPAA BAA | Healthcare customers | PDF only |
| PCI-DSS Attestation | Payment processors | PDF only |

---

## 1. Business Verification

### 1.1 Business License / Registration

**Purpose:** Proves the business is legally registered to operate.

**Acceptable Documents:**
- State business license
- Business registration certificate
- DBA ("Doing Business As") filing
- Professional license (where applicable)
- Foreign entity registration (for non-US)

**Requirements:**
- Must show legal business name (must match application)
- Must show registration/license number
- Must be current (not expired)
- Must show registered state/jurisdiction

**File Specifications:**
- Format: PDF, JPG, JPEG, PNG, WebP
- Max size: 10 MB
- Resolution: Minimum 150 DPI for scans

**Common Rejection Reasons:**
- Expired license
- Name doesn't match application
- Unreadable/blurry scan
- Cropped or incomplete document

---

### 1.2 Tax Identification (EIN/TIN)

**Purpose:** Verifies the business's tax registration with the IRS.

**Acceptable Documents:**
- IRS EIN Confirmation Letter (CP 575 or 147C)
- State tax registration certificate
- Business tax return (redacted, showing EIN)

**Requirements:**
- Must show complete EIN (XX-XXXXXXX format)
- Business name must match application
- Must be official IRS document (not third-party)

**File Specifications:**
- Format: PDF, JPG, JPEG, PNG, WebP
- Max size: 10 MB

**Notes:**
- Sole proprietors may use SSN (with proper identity verification)
- Foreign entities provide equivalent tax ID documentation

---

### 1.3 W-9 Form

**Purpose:** Certifies taxpayer identification number and legal name for US entities.

**When Required:**
- All US-based businesses
- Required for compliance with IRS regulations

**Requirements:**
- Must be completed and signed
- Business name must match application
- EIN/TIN must be provided
- Must be current (dated within 12 months)

**File Specifications:**
- Format: PDF only
- Max size: 10 MB

**Security Note:** W-9 contains sensitive tax information. See [Document Storage Strategy](#document-storage-strategy).

---

### 1.4 Articles of Incorporation

**Purpose:** Proves legal formation of the business entity.

**When Required:**
- Corporations (Inc., Corp.)
- LLCs
- Non-profits

**Acceptable Documents:**
- Certificate of Incorporation
- Articles of Organization (LLC)
- Certificate of Formation
- Partnership Agreement (for partnerships)

**Requirements:**
- Must show state of incorporation
- Must show date of formation
- Entity name must match application
- Must include registered agent information

**File Specifications:**
- Format: PDF only
- Max size: 10 MB

---

## 2. Identity Verification

### 2.1 Government-Issued ID

**Purpose:** Verifies the identity of the authorized representative.

**Acceptable Documents:**
- US Passport
- US Passport Card
- State-issued ID card
- Military ID
- Permanent Resident Card (Green Card)
- Foreign passport (with visa if applicable)

**Requirements:**
- Must show full legal name
- Must show clear photo
- Must not be expired
- Must show date of birth
- All four corners visible

**File Specifications:**
- Format: PDF, JPG, JPEG, PNG, WebP
- Max size: 10 MB
- Resolution: Clear enough to read all text

**Privacy Considerations:**
- This document contains highly sensitive PII
- See [Document Storage Strategy](#document-storage-strategy) for handling

---

### 2.2 Driver's License

**Purpose:** Alternative identity verification for authorized representative.

**Requirements:**
- Must be current (not expired)
- Must show full legal name
- Must show clear photo
- Must show date of birth
- Full document visible (front required, back optional)

**State-Specific Notes:**
- Real ID compliant licenses preferred
- Some enhanced driver's licenses are equivalent to passport cards

**File Specifications:**
- Format: PDF, JPG, JPEG, PNG, WebP
- Max size: 10 MB

**Privacy Considerations:**
- Contains sensitive PII (address, DOB, license number)
- See [Document Storage Strategy](#document-storage-strategy)

---

### 2.3 Address Verification (Utility Bill)

**Purpose:** Confirms business or representative's physical address.

**When Required:**
- Address discrepancy between documents
- P.O. Box as primary address
- High-risk industry applications
- International applications

**Acceptable Documents:**
- Electric/gas utility bill
- Water bill
- Internet/cable bill
- Bank statement (showing address)
- Property tax statement
- Lease agreement (first page showing address)

**Requirements:**
- Must be dated within 90 days
- Must show name matching application
- Must show physical address
- Must be from a recognized utility/bank

**File Specifications:**
- Format: PDF, JPG, JPEG, PNG, WebP
- Max size: 10 MB

---

## 3. Phone Number Authorization

### 3.1 Letter of Authorization (LOA)

**Purpose:** Proves ownership or authorization to use phone numbers for branded calling.

**Critical Importance:** Without valid LOA, calls cannot achieve A-level attestation.

**Requirements:**
- Must be on company letterhead (if available)
- Must list all phone numbers to be used
- Must be signed by authorized representative
- Must include date of authorization
- Must state intended use (branded calling/BCID)

**Template Elements:**
```
LETTER OF AUTHORIZATION

Date: [Date]

To Whom It May Concern:

[Company Name], located at [Address], hereby authorizes the use of 
the following telephone number(s) for Branded Calling ID (BCID) 
display through the BrandCall platform:

Phone Number(s):
- +1 (XXX) XXX-XXXX
- +1 (XXX) XXX-XXXX

We confirm that we own or have authorization to use these numbers 
for outbound calling purposes.

Authorized Representative:
Name: [Full Name]
Title: [Title]
Signature: _______________
Date: [Date]

Company Seal (if applicable)
```

**File Specifications:**
- Format: PDF only (no images)
- Max size: 10 MB

**NumHub Template:**
- NumHub provides an LOA template via API: `GET /api/v1/application/{NumhubEntityId}/downloadtemplate`
- Recommend using NumHub template for fastest approval

---

### 3.2 Phone Number Ownership Documentation

**When Required:**
- New number registrations
- Porting numbers from another carrier
- Toll-free numbers

**Acceptable Supporting Documents:**
- Carrier invoice showing number assignment
- Carrier welcome letter
- Number assignment confirmation
- RespOrg letter (for toll-free)

---

## 4. Brand Assets

### 4.1 Brand Logo

**Purpose:** Displayed on recipient's phone as Rich Call Data (RCD).

**Critical Requirements (NumHub Specific):**

| Specification | Requirement |
|---------------|-------------|
| **Format** | BMP (required by NumHub) |
| **Dimensions** | 200x200 pixels (recommended) |
| **Max File Size** | 50 KB |
| **Color Depth** | 24-bit color |
| **Background** | Transparent or white preferred |
| **Content** | Must match registered business name |

**Design Guidelines:**
- Simple, recognizable design
- Avoid thin lines (may not render well)
- High contrast for visibility
- No text-only logos (include icon if possible)
- Test at 48x48px (minimum display size)

**Logo Submission Flow:**
1. Upload source logo (PNG/JPG acceptable for input)
2. BrandCall converts to BMP specification
3. NumHub validates and stores

**Common Rejection Reasons:**
- Logo text doesn't match business name
- Copyrighted/trademarked images (without proof of rights)
- Inappropriate content
- Poor quality/unreadable at small sizes

---

### 4.2 Call Reason Templates

**Purpose:** Pre-approved call reasons displayed to recipients.

**Requirements:**
- Maximum 64 characters
- Must accurately describe call purpose
- No misleading or deceptive language
- Business-appropriate tone

**Pre-Approved Templates:**
| Industry | Example Call Reasons |
|----------|---------------------|
| Healthcare | "Appointment Reminder", "Test Results Available", "Prescription Ready" |
| Financial | "Account Alert", "Fraud Prevention", "Payment Reminder" |
| Insurance | "Policy Update", "Renewal Notice", "Claims Status" |
| Retail | "Order Update", "Delivery Notification", "Customer Service" |
| General | "Customer Follow-up", "Service Confirmation", "Callback Request" |

---

## 5. Industry-Specific Requirements

### 5.1 Healthcare (HIPAA)

**Additional Requirements:**
- HIPAA Business Associate Agreement (BAA)
- Privacy policy acknowledgment
- PHI handling procedures documentation

**Logo Restrictions:**
- Cannot include medical specialty that reveals patient relationship
- Example: "Acme Oncology" → Use "Acme Healthcare" instead

**Call Reason Restrictions:**
- Cannot reveal specific medical conditions
- Cannot include PHI in call reason
- Must be generic: "Appointment Reminder" not "Chemotherapy Appointment"

---

### 5.2 Financial Services (GLBA/PCI)

**Additional Requirements:**
- GLBA compliance attestation
- PCI-DSS compliance certificate (if handling payment data)
- State licensing documentation (if regulated)

**Call Reason Guidelines:**
- "Account Alert" not "Credit Card Overdue"
- "Important Account Information" not "Loan Default Notice"

---

### 5.3 Debt Collection (FDCPA)

**Additional Requirements:**
- Debt collection license (state-specific)
- FDCPA compliance attestation
- Third-party disclosure policies

**Caller ID Requirements:**
- Must accurately identify as debt collection company
- Cannot disguise as other business type
- Cannot use misleading caller ID

**Restrictions:**
- Enhanced monitoring and reporting
- Lower volume limits initially
- Additional vetting time

---

### 5.4 Political / Non-Profit

**Additional Requirements:**
- 501(c) determination letter (non-profits)
- Campaign registration (political)
- Disclaimer acknowledgment

---

## Document Storage Strategy

### The Challenge

BrandCall must handle sensitive documents containing:
- Social Security Numbers (on driver's licenses)
- Tax Identification Numbers
- Personal addresses
- Financial information
- Government ID numbers

### Storage Architecture Options

#### Option A: Store Everything Locally (Current)

```
User → BrandCall Server → Local/S3 Storage
                      → NumHub API (copy)
```

**Pros:**
- Full control over documents
- Can display in admin panel
- Audit trail for compliance

**Cons:**
- We store sensitive PII
- Liability for data breaches
- PCI/SOC2 compliance burden

---

#### Option B: Pass-Through to NumHub (Recommended)

```
User → BrandCall Server → NumHub API (immediate forward)
                      → Delete local temp file
```

**Implementation:**
1. User uploads document to BrandCall
2. BrandCall validates format/size
3. BrandCall immediately forwards to NumHub
4. BrandCall deletes local copy
5. Store only metadata: filename, type, status, NumHub document ID

**Pros:**
- Sensitive documents not stored on our servers
- NumHub handles PCI/compliance for document storage
- Reduced liability
- Faster compliance certification

**Cons:**
- Cannot display documents in our admin panel
- Depends on NumHub API availability
- Need NumHub credentials for implementation

---

#### Option C: Hybrid Approach

Store non-sensitive documents locally, pass-through sensitive ones:

| Document Type | Storage Location |
|---------------|-----------------|
| Business License | Local + NumHub |
| Tax ID (redacted) | Local + NumHub |
| Driver's License | **NumHub only** |
| Government ID | **NumHub only** |
| LOA | Local + NumHub |
| Logo | Local + NumHub |
| W-9 | **NumHub only** |
| Utility Bill | **NumHub only** |

---

### Recommendation: Option B (Pass-Through)

**Rationale:**
1. Minimizes PII storage on our servers
2. Reduces SOC2/compliance scope
3. NumHub already handles this data for BCID ecosystem
4. We only store non-sensitive metadata

**Implementation Requirements:**
- NumHub API credentials (pending)
- Token management service
- Upload proxy endpoint
- Status sync via webhooks

---

## NumHub API Document Support

### Document Upload Endpoint

```http
POST /api/v1/application/{NumhubEntityId}/documents
Content-Type: multipart/form-data
Authorization: Bearer {token}
client-id: {clientId}
X-Auth-Scheme: ATLAASROPG

Files: [binary data]
DocumentType: LOA | LOGO | DOCUMENTS
Description: "Business License for verification"
```

### Supported Document Types

| NumHub Type | Maps To |
|-------------|---------|
| `LOA` | Letter of Authorization (PDF only) |
| `LOGO` | Brand logo (BMP only) |
| `DOCUMENTS` | All other documents (PDF, XLSX, CSV) |

### NumHub Document Formats

| Document | Required Format |
|----------|----------------|
| LOA | PDF |
| Logo | BMP |
| Business License | PDF |
| Government ID | PDF |
| Tax Documents | PDF, XLSX |

### LOA Template Download

NumHub provides pre-formatted LOA templates:

```http
GET /api/v1/application/{NumhubEntityId}/downloadtemplate
```

Returns a PDF template that can be filled and re-uploaded.

### Direct Upload Limitations

**NumHub does NOT support:**
- Client-side direct upload (no signed URLs)
- Pre-signed upload URLs
- Browser-to-NumHub direct POST

**All uploads must be server-side** with authentication headers.

### Document Verification Flow

```
1. User uploads to BrandCall
       ↓
2. BrandCall validates format/size
       ↓
3. BrandCall proxies to NumHub
       ↓
4. NumHub returns document ID
       ↓
5. BrandCall stores metadata only
       ↓
6. NumHub processes/vets document
       ↓
7. Webhook notifies BrandCall of status
       ↓
8. User sees verification status
```

---

## Compliance Checklist

### Pre-Onboarding Checklist (Customer)

- [ ] Gather business license/registration
- [ ] Obtain EIN confirmation letter or W-9
- [ ] Prepare government ID for authorized representative
- [ ] List all phone numbers for branded calling
- [ ] Prepare LOA on company letterhead
- [ ] Create 200x200 BMP logo file
- [ ] Define call reason templates

### Document Quality Checklist

- [ ] All documents are current (not expired)
- [ ] Documents are legible (clear scans/photos)
- [ ] Business name matches across all documents
- [ ] All required fields are visible
- [ ] PDFs are not password-protected
- [ ] Files are under 10 MB

### Technical Integration Checklist

- [ ] NumHub API credentials obtained
- [ ] Token management implemented
- [ ] Document upload endpoint created
- [ ] Format validation working
- [ ] Pass-through to NumHub working
- [ ] Webhook handler for status updates
- [ ] Error handling for upload failures

### Compliance Verification Checklist

- [ ] All required documents collected
- [ ] Identity verification complete
- [ ] Business verification complete
- [ ] Phone number authorization complete
- [ ] Logo approved
- [ ] Call reasons approved
- [ ] Vetting status: APPROVED

---

## Appendix: Document Type Reference

### Complete Mapping

| BrandCall Type | NumHub DocumentType | Purpose | Format |
|----------------|-------------------|---------|--------|
| `business_license` | `DOCUMENTS` | Business verification | PDF, JPG |
| `tax_id` | `DOCUMENTS` | Tax verification | PDF, JPG |
| `drivers_license` | `DOCUMENTS` | Identity verification | PDF, JPG |
| `government_id` | `DOCUMENTS` | Identity verification | PDF, JPG |
| `loa` | `LOA` | Number authorization | PDF only |
| `articles_incorporation` | `DOCUMENTS` | Entity verification | PDF only |
| `utility_bill` | `DOCUMENTS` | Address verification | PDF, JPG |
| `w9` | `DOCUMENTS` | Tax compliance | PDF only |
| `logo` | `LOGO` | Brand display | BMP only |

---

*Document maintained by BrandCall Engineering. For questions: support@brandcall.io*
