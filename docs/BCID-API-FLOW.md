# Branded Caller ID - API Call Flow

> Technical reference for how branded calls are initiated, authenticated, and delivered through the NumHub BrandControl platform.

---

## Overview

Branded Caller ID (BCID) requires coordination between multiple parties to display verified business information on outbound calls. This document describes the API communication flow from call initiation to brand display.

---

## High-Level Architecture

```mermaid
flowchart TB
    subgraph "Your Platform (BrandCall)"
        A[Customer Dashboard]
        B[BrandCall API]
        C[Voice Provider Integration]
    end
    
    subgraph "NumHub BrandControl"
        D[REST API Gateway]
        E[Vetting Service]
        F[SHAKEN Signing Service]
        G[RCD Registry]
    end
    
    subgraph "Carrier Network"
        H[Originating Carrier]
        I[STIR/SHAKEN Gateway]
        J[Terminating Carrier]
    end
    
    subgraph "Consumer"
        K[Mobile Device]
    end
    
    A --> B
    B --> D
    D --> E
    D --> F
    D --> G
    C --> H
    F --> I
    I --> H
    H --> J
    J --> K
```

---

## Call Flow Sequence

### Pattern A: Pre-Call Branding (Out-of-Band)

Use this pattern when you can make an API call before initiating the voice call.

```mermaid
sequenceDiagram
    autonumber
    participant App as BrandCall App
    participant API as NumHub API
    participant Sign as SHAKEN Signer
    participant OSP as Originating Carrier
    participant TSP as Terminating Carrier
    participant Phone as Consumer Phone

    Note over App,Phone: Phase 1 - Brand Registration
    App->>API: POST /brands
    API->>API: Validate and store brand
    API-->>App: 201 Created with brandId
    
    Note over App,Phone: Phase 2 - Pre-Call Intent
    App->>API: POST /calls/intent
    Note right of App: brandId, fromNumber<br/>toNumber, callReason
    API->>Sign: Generate PASSporT + RCD
    Sign-->>API: Signed token
    API-->>App: 200 OK with callToken
    
    Note over App,Phone: Phase 3 - Voice Call
    App->>OSP: SIP INVITE with callToken
    OSP->>OSP: Attach SHAKEN Identity header
    OSP->>TSP: Route call with signed identity
    TSP->>TSP: Verify SHAKEN signature
    TSP->>Phone: Deliver call + brand display
    
    Note over App,Phone: Phase 4 - Delivery Confirmation
    TSP-->>API: Webhook call_delivered
    API-->>App: Webhook brand_displayed
```

### Pattern B: Real-Time Signing (In-Band)

For carriers and high-volume integrations using SIP trunking.

```mermaid
sequenceDiagram
    autonumber
    participant PBX as Your PBX/SBC
    participant NumHub as NumHub SIP Proxy
    participant Sign as SHAKEN Signer
    participant TSP as Terminating Carrier
    participant Phone as Consumer Phone

    PBX->>NumHub: INVITE SIP request
    Note right of PBX: From and To numbers<br/>plus X-Brand-ID header
    
    NumHub->>NumHub: Lookup brand profile
    NumHub->>Sign: Sign call in real-time
    Sign-->>NumHub: PASSporT + RCD attached
    
    NumHub->>TSP: INVITE with Identity header
    Note right of NumHub: Identity header contains<br/>signed PASSporT token
    
    TSP->>TSP: Verify attestation level
    TSP->>Phone: Display branded call
    Phone-->>TSP: 200 OK answered
    TSP-->>NumHub: Call connected
    NumHub-->>PBX: 200 OK
```

---

## Brand Registration Flow

```mermaid
sequenceDiagram
    autonumber
    participant Cust as Customer
    participant App as BrandCall
    participant API as NumHub API
    participant Vet as Vetting Service
    participant Reg as Brand Registry

    Cust->>App: Submit brand application
    Note right of Cust: Company name and logo<br/>phone numbers and LOA docs
    
    App->>API: POST /brands
    API->>Vet: Initiate vetting
    Vet->>Vet: Verify business identity
    Vet->>Vet: Validate phone ownership (LOA)
    Vet->>Vet: Check logo requirements
    
    alt Vetting Approved
        Vet-->>API: vetting_approved
        API->>Reg: Register brand in BCID ecosystem
        Reg-->>API: Brand active
        API-->>App: Webhook brand.approved
        App-->>Cust: Brand ready for calls
    else Vetting Rejected
        Vet-->>API: vetting_rejected with reasons
        API-->>App: Webhook brand.rejected
        App-->>Cust: Please correct issues
    end
```

---

## Event Reference

### Webhook Events

| Event | Trigger | Payload | Use Case |
|:------|:--------|:--------|:---------|
| `brand.created` | Brand submitted | `{brandId, status: "pending"}` | Start onboarding UI |
| `brand.vetting_started` | Vetting begins | `{brandId, estimatedCompletion}` | Show progress |
| `brand.approved` | Vetting passed | `{brandId, status: "active"}` | Enable calling |
| `brand.rejected` | Vetting failed | `{brandId, reasons[], canRetry}` | Show correction needed |
| `brand.suspended` | Policy violation | `{brandId, reason, appealUrl}` | Alert customer |
| `call.intent_created` | Pre-call registered | `{callToken, expiresAt}` | Proceed with call |
| `call.branded` | Brand displayed | `{callId, carrier, attestation}` | Confirm delivery |
| `call.delivered` | Call connected | `{callId, duration, answered}` | Track success |
| `call.failed` | Brand not shown | `{callId, reason, fallback}` | Debug issues |
| `number.flagged` | Spam label detected | `{number, carrier, label}` | Remediation alert |
| `number.cleared` | Label removed | `{number, carrier}` | Confirm fix |

### API Status Codes

| Code | Meaning | Action Required |
|:----:|:--------|:----------------|
| `200` | Success | Process response |
| `201` | Created | Store returned ID |
| `202` | Accepted (async) | Poll or wait for webhook |
| `400` | Bad request | Fix request payload |
| `401` | Unauthorized | Refresh API token |
| `403` | Forbidden | Check permissions/quotas |
| `404` | Not found | Verify resource ID |
| `409` | Conflict | Resource already exists |
| `422` | Validation failed | Check `errors[]` array |
| `429` | Rate limited | Back off, retry later |
| `500` | Server error | Retry with backoff |
| `503` | Service unavailable | Retry later |

---

## API Endpoints

### Brand Management

| Method | Endpoint | Description |
|:-------|:---------|:------------|
| `POST` | `/v1/brands` | Create new brand |
| `GET` | `/v1/brands` | List all brands |
| `GET` | `/v1/brands/{id}` | Get brand details |
| `PATCH` | `/v1/brands/{id}` | Update brand |
| `DELETE` | `/v1/brands/{id}` | Deactivate brand |
| `POST` | `/v1/brands/{id}/logo` | Upload logo |
| `POST` | `/v1/brands/{id}/numbers` | Add phone numbers |
| `DELETE` | `/v1/brands/{id}/numbers/{number}` | Remove number |

### Call Operations

| Method | Endpoint | Description |
|:-------|:---------|:------------|
| `POST` | `/v1/calls/intent` | Register call intent (pre-call) |
| `GET` | `/v1/calls/{id}` | Get call details |
| `GET` | `/v1/calls` | List recent calls |
| `GET` | `/v1/calls/stats` | Call statistics |

### Vetting & Compliance

| Method | Endpoint | Description |
|:-------|:---------|:------------|
| `POST` | `/v1/brands/{id}/vetting` | Submit for vetting |
| `GET` | `/v1/brands/{id}/vetting` | Vetting status |
| `POST` | `/v1/brands/{id}/documents` | Upload KYC document |
| `GET` | `/v1/brands/{id}/compliance` | Compliance status |

### Number Management

| Method | Endpoint | Description |
|:-------|:---------|:------------|
| `GET` | `/v1/numbers` | List registered numbers |
| `GET` | `/v1/numbers/{number}/reputation` | Check spam status |
| `POST` | `/v1/numbers/{number}/remediate` | Request label removal |

---

## Call Intent Request/Response

### Request

```json
POST /v1/calls/intent
Authorization: Bearer {api_key}
Content-Type: application/json

{
  "brandId": "brand_abc123",
  "fromNumber": "+15551234567",
  "toNumber": "+15559876543",
  "callReason": "Appointment Reminder",
  "callReasonCode": "APPT_REMINDER",
  "metadata": {
    "campaignId": "camp_xyz",
    "agentId": "agent_001"
  }
}
```

### Response

```json
{
  "callId": "call_def456",
  "callToken": "eyJhbGciOiJFUzI1NiIs...",
  "expiresAt": "2026-02-04T20:35:00Z",
  "brand": {
    "id": "brand_abc123",
    "displayName": "Acme Healthcare",
    "logoUrl": "https://cdn.numhub.com/logos/abc123.png"
  },
  "attestation": "A",
  "carriers": {
    "tmobile": "supported",
    "verizon": "supported",
    "att": "pending"
  }
}
```

---

## STIR/SHAKEN Identity Flow

```mermaid
flowchart LR
    subgraph "Call Origination"
        A[Your App] --> B[NumHub Signs Call]
        B --> C[PASSporT Created]
    end
    
    subgraph "PASSporT Contents"
        C --> D[Header]
        C --> E[Payload]
        C --> F[Signature]
        
        D --> D1["alg: ES256"]
        D --> D2["typ: passport"]
        D --> D3["x5u: cert URL"]
        
        E --> E1["orig: {tn: '+15551234567'}"]
        E --> E2["dest: {tn: ['+15559876543']}"]
        E --> E3["iat: timestamp"]
        E --> E4["attest: 'A'"]
        E --> E5["origid: UUID"]
    end
    
    subgraph "Rich Call Data (RCD)"
        C --> G[RCD Extension]
        G --> G1["nam: 'Acme Healthcare'"]
        G --> G2["ico: logo URL"]
        G --> G3["rsn: 'Appointment Reminder'"]
    end
    
    subgraph "Verification"
        F --> H[Terminating Carrier]
        H --> I{Signature Valid?}
        I -->|Yes| J[Display Brand]
        I -->|No| K[Show Number Only]
    end
```

---

## Attestation Levels

```mermaid
flowchart TD
    A[Call Originated] --> B{Provider knows caller?}
    B -->|No| C["Level C: Gateway"]
    B -->|Yes| D{Verified number ownership?}
    D -->|No| E["Level B: Partial"]
    D -->|Yes| F["Level A: Full"]
    
    C --> G[❌ No branding allowed]
    E --> H[⚠️ Limited branding]
    F --> I[✅ Full branding + logo]
    
    style F fill:#22c55e
    style E fill:#f59e0b
    style C fill:#ef4444
```

| Level | Name | Requirements | Brand Display |
|:-----:|:-----|:-------------|:--------------|
| **A** | Full | Provider verified caller identity AND phone number ownership | ✅ Name + Logo + Reason |
| **B** | Partial | Provider knows caller but cannot verify number rights | ⚠️ Name only (no logo) |
| **C** | Gateway | Minimal info (international, unauthenticated) | ❌ No branding |

---

## Error Handling

### Common Error Responses

```json
{
  "error": {
    "code": "BRAND_NOT_ACTIVE",
    "message": "Brand must be active to make branded calls",
    "details": {
      "brandId": "brand_abc123",
      "currentStatus": "pending_vetting",
      "requiredStatus": "active"
    },
    "helpUrl": "https://docs.numhub.com/errors/brand-not-active"
  }
}
```

### Error Codes

| Code | Description | Resolution |
|:-----|:------------|:-----------|
| `BRAND_NOT_FOUND` | Brand ID doesn't exist | Verify brand ID |
| `BRAND_NOT_ACTIVE` | Brand pending or suspended | Complete vetting |
| `NUMBER_NOT_REGISTERED` | Phone number not in brand | Add number to brand |
| `NUMBER_OWNERSHIP_EXPIRED` | LOA needs renewal | Submit new LOA |
| `CALL_REASON_INVALID` | Unrecognized call reason code | Use valid reason code |
| `RATE_LIMIT_EXCEEDED` | Too many requests | Implement backoff |
| `INSUFFICIENT_CREDITS` | Account balance low | Add payment method |
| `CARRIER_NOT_SUPPORTED` | Destination carrier unsupported | Check carrier coverage |

---

## Integration Checklist

```mermaid
flowchart LR
    A[1. API Keys] --> B[2. Brand Setup]
    B --> C[3. Vetting]
    C --> D[4. Number Registration]
    D --> E[5. Test Calls]
    E --> F[6. Go Live]
    
    A -.-> A1["Obtain sandbox + prod keys"]
    B -.-> B1["Create brand profile"]
    C -.-> C1["Submit KYC docs"]
    D -.-> D1["Register phone numbers"]
    E -.-> E1["Verify brand display"]
    F -.-> F1["Switch to production"]
```

| Step | Task | Validation |
|:----:|:-----|:-----------|
| 1 | Obtain API credentials | Can authenticate to sandbox |
| 2 | Create brand profile | Brand ID returned |
| 3 | Submit vetting documents | Status: `vetting_in_progress` |
| 4 | Wait for vetting approval | Status: `active` |
| 5 | Register phone numbers | Numbers associated with brand |
| 6 | Make test branded call | Brand displays on device |
| 7 | Configure webhooks | Events received |
| 8 | Switch to production | Live calls branded |

---

## Rate Limits

| Endpoint | Limit | Window |
|:---------|------:|:-------|
| `POST /brands` | 10 | per hour |
| `POST /calls/intent` | 1,000 | per minute |
| `GET /calls/*` | 100 | per minute |
| `POST /documents` | 50 | per hour |
| Webhooks (outbound) | 10,000 | per minute |

---

## Webhook Security

### Signature Verification

```
X-NumHub-Signature: sha256=abc123...
X-NumHub-Timestamp: 1707076800
```

```python
import hmac
import hashlib

def verify_webhook(payload, signature, timestamp, secret):
    message = f"{timestamp}.{payload}"
    expected = hmac.new(
        secret.encode(),
        message.encode(),
        hashlib.sha256
    ).hexdigest()
    return hmac.compare_digest(f"sha256={expected}", signature)
```

---

*Document Version: 1.0*  
*Last Updated: February 4, 2026*
