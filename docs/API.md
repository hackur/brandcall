# BrandCall API Reference

Complete REST API documentation for integrating with BrandCall's branded caller ID platform.

**Base URL:** `https://api.brandcall.com/v1`

**API Version:** v1

---

## Table of Contents

1. [Authentication](#authentication)
2. [Brands](#brands)
3. [Calls](#calls)
4. [Analytics](#analytics)
5. [Webhooks](#webhooks)
6. [Error Handling](#error-handling)
7. [Rate Limits](#rate-limits)
8. [SDKs & Examples](#sdks--examples)

---

## Authentication

BrandCall uses **Bearer token authentication** with per-brand API keys.

### API Key Format

```
bci_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
```

All API keys are prefixed with `bci_` followed by 64 hexadecimal characters.

### Authenticating Requests

Include your API key in the `Authorization` header:

```http
Authorization: Bearer bci_your_api_key_here
```

### Security Best Practices

- **Never expose API keys in client-side code** — use server-side calls only
- **Rotate keys periodically** — regenerate via dashboard or API
- **Use environment variables** — never commit keys to version control
- **One key per brand** — each brand has its own isolated API key

### Example Request

```bash
curl -X GET "https://api.brandcall.com/v1/brands" \
  -H "Authorization: Bearer bci_a1b2c3d4e5f6..." \
  -H "Content-Type: application/json" \
  -H "Accept: application/json"
```

### Authentication Errors

| Code | Error | Description |
|------|-------|-------------|
| 401 | `unauthorized` | Missing or invalid API key |
| 403 | `forbidden` | API key valid but brand is suspended/inactive |

---

## Brands

Manage branded caller ID profiles.

### Brand Object

```json
{
  "id": 1,
  "slug": "acme-corp",
  "name": "ACME Corporation",
  "display_name": "ACME Corp",
  "call_reason": "Appointment Reminder",
  "logo_url": "https://api.brandcall.com/storage/logos/acme-corp.png",
  "status": "active",
  "phone_numbers_count": 5,
  "created_at": "2026-01-15T10:30:00Z",
  "updated_at": "2026-01-20T14:22:00Z"
}
```

#### Brand Status Values

| Status | Description |
|--------|-------------|
| `draft` | Brand created but not submitted for vetting |
| `pending_vetting` | Awaiting approval by BrandCall team |
| `active` | Approved and can make branded calls |
| `suspended` | Temporarily disabled (compliance/billing issue) |

---

### List Brands

Retrieve all brands associated with your API key's tenant.

```http
GET /api/v1/brands
```

#### Query Parameters

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `status` | string | — | Filter by status: `draft`, `pending_vetting`, `active`, `suspended` |
| `search` | string | — | Search by name or display_name |
| `per_page` | integer | 15 | Results per page (max: 100) |
| `page` | integer | 1 | Page number |

#### Response

```json
{
  "data": [
    {
      "id": 1,
      "slug": "acme-corp",
      "name": "ACME Corporation",
      "display_name": "ACME Corp",
      "call_reason": "Appointment Reminder",
      "logo_url": "https://api.brandcall.com/storage/logos/acme-corp.png",
      "status": "active",
      "phone_numbers_count": 5,
      "created_at": "2026-01-15T10:30:00Z",
      "updated_at": "2026-01-20T14:22:00Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "per_page": 15,
    "total": 3,
    "last_page": 1
  },
  "links": {
    "first": "https://api.brandcall.com/v1/brands?page=1",
    "last": "https://api.brandcall.com/v1/brands?page=1",
    "prev": null,
    "next": null
  }
}
```

---

### Get Brand Details

Retrieve a single brand by slug.

```http
GET /api/v1/brands/{slug}
```

#### Path Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `slug` | string | Brand's URL-safe identifier |

#### Response

```json
{
  "data": {
    "id": 1,
    "slug": "acme-corp",
    "name": "ACME Corporation",
    "display_name": "ACME Corp",
    "call_reason": "Appointment Reminder",
    "logo_url": "https://api.brandcall.com/storage/logos/acme-corp.png",
    "status": "active",
    "phone_numbers": [
      {
        "id": 1,
        "phone_number": "+14155551234",
        "country_code": "US",
        "cnam_display_name": "ACME Corp",
        "cnam_registered": true,
        "status": "active"
      }
    ],
    "numhub_brand_id": "nbrand_abc123",
    "created_at": "2026-01-15T10:30:00Z",
    "updated_at": "2026-01-20T14:22:00Z"
  }
}
```

---

## Calls

Initiate and manage branded calls.

### Call Object

```json
{
  "id": "call_8f7e6d5c4b3a2901",
  "brand_slug": "acme-corp",
  "from_number": "+14155551234",
  "to_number": "+14155559876",
  "call_reason": "Appointment Reminder",
  "status": "completed",
  "direction": "outbound",
  "attestation_level": "A",
  "stir_shaken_verified": true,
  "branded_call": true,
  "call_initiated_at": "2026-01-20T14:30:00Z",
  "call_answered_at": "2026-01-20T14:30:12Z",
  "call_ended_at": "2026-01-20T14:32:45Z",
  "ring_duration_seconds": 12,
  "talk_duration_seconds": 153,
  "total_duration_seconds": 165,
  "cost": "0.0250",
  "created_at": "2026-01-20T14:30:00Z"
}
```

#### Call Status Values

| Status | Description |
|--------|-------------|
| `initiated` | Call request received, connecting |
| `ringing` | Phone is ringing at destination |
| `answered` | Call was answered |
| `completed` | Call ended normally |
| `failed` | Call failed to connect |
| `busy` | Destination was busy |
| `no_answer` | Call was not answered |
| `canceled` | Call was canceled before connection |

#### Attestation Levels

| Level | Description |
|-------|-------------|
| `A` | Full attestation — carrier verified caller identity and authorization |
| `B` | Partial attestation — carrier verified origin but not full caller chain |
| `C` | Gateway attestation — call originated from a gateway with limited verification |

---

### Initiate Branded Call

Start a new branded outbound call.

```http
POST /api/v1/brands/{slug}/calls
```

#### Path Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `slug` | string | Brand's URL-safe identifier |

#### Request Body

```json
{
  "from": "+14155551234",
  "to": "+14155559876",
  "call_reason": "Appointment Reminder",
  "callback_url": "https://yourapp.com/webhooks/brandcall",
  "metadata": {
    "appointment_id": "appt_123",
    "patient_name": "John Doe"
  }
}
```

#### Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `from` | string | ✅ | Caller phone number (E.164 format: `+14155551234`) |
| `to` | string | ✅ | Destination phone number (E.164 format) |
| `call_reason` | string | — | Override default call reason (max 100 chars) |
| `callback_url` | string | — | Webhook URL for call status updates |
| `metadata` | object | — | Custom key-value pairs (max 10 keys, 500 chars each) |

#### Response

```json
{
  "data": {
    "id": "call_8f7e6d5c4b3a2901",
    "brand_slug": "acme-corp",
    "from_number": "+14155551234",
    "to_number": "+14155559876",
    "call_reason": "Appointment Reminder",
    "status": "initiated",
    "direction": "outbound",
    "attestation_level": "A",
    "stir_shaken_verified": true,
    "branded_call": true,
    "call_initiated_at": "2026-01-20T14:30:00Z",
    "metadata": {
      "appointment_id": "appt_123",
      "patient_name": "John Doe"
    },
    "created_at": "2026-01-20T14:30:00Z"
  }
}
```

#### Errors

| Code | Error | Description |
|------|-------|-------------|
| 400 | `invalid_phone_number` | Phone number not in E.164 format |
| 400 | `unregistered_from_number` | From number not registered with this brand |
| 403 | `brand_not_active` | Brand is not in `active` status |
| 429 | `rate_limit_exceeded` | Too many calls per second |

---

### List Call History

Retrieve paginated call history for a brand.

```http
GET /api/v1/brands/{slug}/calls
```

#### Query Parameters

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `status` | string | — | Filter: `initiated`, `answered`, `completed`, `failed` |
| `from` | string | — | Filter by caller number (E.164) |
| `to` | string | — | Filter by destination number (E.164) |
| `date_from` | string | — | Start date (ISO 8601: `2026-01-01`) |
| `date_to` | string | — | End date (ISO 8601) |
| `per_page` | integer | 25 | Results per page (max: 100) |
| `page` | integer | 1 | Page number |

#### Response

```json
{
  "data": [
    {
      "id": "call_8f7e6d5c4b3a2901",
      "from_number": "+14155551234",
      "to_number": "+14155559876",
      "call_reason": "Appointment Reminder",
      "status": "completed",
      "attestation_level": "A",
      "talk_duration_seconds": 153,
      "cost": "0.0250",
      "call_initiated_at": "2026-01-20T14:30:00Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "per_page": 25,
    "total": 1532,
    "last_page": 62
  },
  "links": {
    "first": "https://api.brandcall.com/v1/brands/acme-corp/calls?page=1",
    "last": "https://api.brandcall.com/v1/brands/acme-corp/calls?page=62",
    "prev": null,
    "next": "https://api.brandcall.com/v1/brands/acme-corp/calls?page=2"
  }
}
```

---

### Get Call Details

Retrieve complete details for a specific call.

```http
GET /api/v1/brands/{slug}/calls/{id}
```

#### Path Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `slug` | string | Brand's URL-safe identifier |
| `id` | string | Call ID (format: `call_xxxxx`) |

#### Response

```json
{
  "data": {
    "id": "call_8f7e6d5c4b3a2901",
    "brand_slug": "acme-corp",
    "from_number": "+14155551234",
    "to_number": "+14155559876",
    "call_reason": "Appointment Reminder",
    "status": "completed",
    "direction": "outbound",
    "attestation_level": "A",
    "stir_shaken_verified": true,
    "branded_call": true,
    "rcd_payload": {
      "display_name": "ACME Corp",
      "logo_url": "https://cdn.brandcall.com/logos/acme.png",
      "call_reason": "Appointment Reminder"
    },
    "call_initiated_at": "2026-01-20T14:30:00Z",
    "call_answered_at": "2026-01-20T14:30:12Z",
    "call_ended_at": "2026-01-20T14:32:45Z",
    "ring_duration_seconds": 12,
    "talk_duration_seconds": 153,
    "total_duration_seconds": 165,
    "cost": "0.0250",
    "billable": true,
    "spam_scores": {
      "hiya": 0,
      "nomorobo": 0
    },
    "flagged_as_spam": false,
    "metadata": {
      "appointment_id": "appt_123"
    },
    "carrier_metadata": {
      "carrier": "Verizon",
      "line_type": "mobile"
    },
    "created_at": "2026-01-20T14:30:00Z"
  }
}
```

---

## Analytics

Access call analytics and performance metrics.

### Get Brand Analytics

Retrieve aggregated analytics for a brand.

```http
GET /api/v1/brands/{slug}/analytics
```

#### Query Parameters

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `period` | string | `30d` | Time period: `7d`, `30d`, `90d`, `365d`, `all` |
| `date_from` | string | — | Custom start date (overrides period) |
| `date_to` | string | — | Custom end date |
| `group_by` | string | `day` | Aggregation: `hour`, `day`, `week`, `month` |

#### Response

```json
{
  "data": {
    "summary": {
      "total_calls": 15420,
      "answered_calls": 12336,
      "answer_rate": 0.80,
      "average_duration_seconds": 127,
      "total_talk_time_seconds": 1567872,
      "total_cost": "385.50",
      "spam_flags": 0
    },
    "by_status": {
      "completed": 12336,
      "no_answer": 2467,
      "busy": 412,
      "failed": 205
    },
    "by_attestation": {
      "A": 15210,
      "B": 180,
      "C": 30
    },
    "time_series": [
      {
        "date": "2026-01-20",
        "total_calls": 523,
        "answered_calls": 418,
        "answer_rate": 0.80,
        "average_duration_seconds": 132
      },
      {
        "date": "2026-01-19",
        "total_calls": 498,
        "answered_calls": 403,
        "answer_rate": 0.81,
        "average_duration_seconds": 125
      }
    ],
    "top_destinations": [
      {
        "area_code": "415",
        "state": "CA",
        "call_count": 2341,
        "answer_rate": 0.82
      },
      {
        "area_code": "212",
        "state": "NY",
        "call_count": 1876,
        "answer_rate": 0.78
      }
    ],
    "period": {
      "from": "2025-12-21T00:00:00Z",
      "to": "2026-01-20T23:59:59Z"
    }
  }
}
```

### Key Metrics Explained

| Metric | Description |
|--------|-------------|
| `answer_rate` | Percentage of calls answered (VPU - Voice Pick Up) |
| `attestation_level` | STIR/SHAKEN verification level |
| `spam_flags` | Calls flagged as spam by carriers/blockers |

---

## Webhooks

Receive real-time notifications for call events and brand status changes.

### Configuring Webhooks

Configure webhook endpoints in your BrandCall dashboard or via the API when initiating calls (`callback_url` parameter).

### Webhook Payload Format

All webhooks are sent as HTTP POST requests with JSON body:

```json
{
  "id": "evt_a1b2c3d4e5f6g7h8",
  "type": "call.completed",
  "created_at": "2026-01-20T14:32:45Z",
  "data": {
    // Event-specific payload
  }
}
```

### Webhook Signature Verification

All webhooks include a signature header for verification:

```http
X-BrandCall-Signature: sha256=a1b2c3d4e5f6...
```

Verify using HMAC-SHA256:

```php
$payload = file_get_contents('php://input');
$signature = hash_hmac('sha256', $payload, $webhookSecret);
$isValid = hash_equals($signature, $receivedSignature);
```

---

### Call Events

#### `call.initiated`

Sent when a call request is received and processing begins.

```json
{
  "id": "evt_a1b2c3d4e5f6g7h8",
  "type": "call.initiated",
  "created_at": "2026-01-20T14:30:00Z",
  "data": {
    "call_id": "call_8f7e6d5c4b3a2901",
    "brand_slug": "acme-corp",
    "from_number": "+14155551234",
    "to_number": "+14155559876",
    "call_reason": "Appointment Reminder",
    "attestation_level": "A",
    "metadata": {
      "appointment_id": "appt_123"
    }
  }
}
```

#### `call.answered`

Sent when the call is answered by the recipient.

```json
{
  "id": "evt_b2c3d4e5f6g7h8i9",
  "type": "call.answered",
  "created_at": "2026-01-20T14:30:12Z",
  "data": {
    "call_id": "call_8f7e6d5c4b3a2901",
    "brand_slug": "acme-corp",
    "from_number": "+14155551234",
    "to_number": "+14155559876",
    "ring_duration_seconds": 12,
    "answered_at": "2026-01-20T14:30:12Z"
  }
}
```

#### `call.completed`

Sent when a call ends successfully.

```json
{
  "id": "evt_c3d4e5f6g7h8i9j0",
  "type": "call.completed",
  "created_at": "2026-01-20T14:32:45Z",
  "data": {
    "call_id": "call_8f7e6d5c4b3a2901",
    "brand_slug": "acme-corp",
    "from_number": "+14155551234",
    "to_number": "+14155559876",
    "status": "completed",
    "ring_duration_seconds": 12,
    "talk_duration_seconds": 153,
    "total_duration_seconds": 165,
    "cost": "0.0250",
    "answered_at": "2026-01-20T14:30:12Z",
    "ended_at": "2026-01-20T14:32:45Z"
  }
}
```

#### `call.failed`

Sent when a call fails to connect.

```json
{
  "id": "evt_d4e5f6g7h8i9j0k1",
  "type": "call.failed",
  "created_at": "2026-01-20T14:30:30Z",
  "data": {
    "call_id": "call_8f7e6d5c4b3a2901",
    "brand_slug": "acme-corp",
    "from_number": "+14155551234",
    "to_number": "+14155559876",
    "status": "failed",
    "failure_reason": "invalid_destination",
    "failure_code": "CALL_001",
    "failure_message": "The destination number is not valid or cannot be reached"
  }
}
```

##### Failure Reason Codes

| Code | Reason | Description |
|------|--------|-------------|
| `CALL_001` | `invalid_destination` | Destination number is invalid or unreachable |
| `CALL_002` | `carrier_rejected` | Carrier rejected the call |
| `CALL_003` | `network_error` | Network connectivity issue |
| `CALL_004` | `busy` | Destination was busy |
| `CALL_005` | `no_answer` | No answer after ring timeout |
| `CALL_006` | `blocked` | Call blocked by carrier or recipient |
| `CALL_007` | `insufficient_funds` | Account has insufficient balance |

---

### Brand Events

#### `brand.status_changed`

Sent when a brand's status changes.

```json
{
  "id": "evt_e5f6g7h8i9j0k1l2",
  "type": "brand.status_changed",
  "created_at": "2026-01-20T10:00:00Z",
  "data": {
    "brand_id": 1,
    "brand_slug": "acme-corp",
    "previous_status": "pending_vetting",
    "new_status": "active",
    "reason": "Brand approved after vetting process",
    "changed_at": "2026-01-20T10:00:00Z"
  }
}
```

---

### Webhook Best Practices

1. **Respond quickly** — Return 2xx within 5 seconds; process asynchronously
2. **Handle duplicates** — Use `id` field for idempotency
3. **Verify signatures** — Always validate `X-BrandCall-Signature`
4. **Retry handling** — We retry failed webhooks 3 times with exponential backoff
5. **Use HTTPS** — Webhook URLs must be HTTPS in production

---

## Error Handling

All API errors follow a consistent format.

### Error Response Format

```json
{
  "error": {
    "code": "validation_error",
    "message": "The given data was invalid.",
    "details": {
      "from": ["The from field must be in E.164 format."],
      "to": ["The to field is required."]
    }
  },
  "request_id": "req_a1b2c3d4e5f6"
}
```

### HTTP Status Codes

| Code | Description |
|------|-------------|
| `200` | Success |
| `201` | Created |
| `400` | Bad Request — Invalid parameters |
| `401` | Unauthorized — Invalid or missing API key |
| `403` | Forbidden — Valid key but insufficient permissions |
| `404` | Not Found — Resource doesn't exist |
| `422` | Unprocessable Entity — Validation failed |
| `429` | Too Many Requests — Rate limit exceeded |
| `500` | Internal Server Error — Contact support |
| `503` | Service Unavailable — Temporary outage |

### Error Codes Reference

#### Authentication Errors

| Code | Description |
|------|-------------|
| `unauthorized` | Missing or invalid API key |
| `forbidden` | API key valid but action not permitted |
| `brand_suspended` | Brand is suspended |
| `brand_not_active` | Brand must be active to perform this action |

#### Validation Errors

| Code | Description |
|------|-------------|
| `validation_error` | One or more fields failed validation |
| `invalid_phone_number` | Phone number not in E.164 format |
| `unregistered_from_number` | From number not registered with brand |
| `invalid_date_range` | Date range is invalid |

#### Resource Errors

| Code | Description |
|------|-------------|
| `not_found` | Requested resource doesn't exist |
| `brand_not_found` | Brand with given slug not found |
| `call_not_found` | Call with given ID not found |

#### Rate Limit Errors

| Code | Description |
|------|-------------|
| `rate_limit_exceeded` | Too many requests |
| `call_rate_exceeded` | Too many calls per second |
| `daily_limit_exceeded` | Daily call limit reached |

---

## Rate Limits

### Default Limits

| Tier | API Requests | Calls per Second | Daily Calls |
|------|--------------|------------------|-------------|
| **Starter** | 100/min | 1 | 1,000 |
| **Growth** | 500/min | 5 | 10,000 |
| **Enterprise** | 2,000/min | 25 | Unlimited |

### Rate Limit Headers

All responses include rate limit information:

```http
X-RateLimit-Limit: 500
X-RateLimit-Remaining: 487
X-RateLimit-Reset: 1706032800
```

| Header | Description |
|--------|-------------|
| `X-RateLimit-Limit` | Maximum requests per window |
| `X-RateLimit-Remaining` | Requests remaining in current window |
| `X-RateLimit-Reset` | Unix timestamp when limit resets |

### Handling Rate Limits

When rate limited, you'll receive:

```http
HTTP/1.1 429 Too Many Requests
Retry-After: 30

{
  "error": {
    "code": "rate_limit_exceeded",
    "message": "Rate limit exceeded. Retry after 30 seconds.",
    "retry_after": 30
  }
}
```

**Best practices:**
- Implement exponential backoff
- Cache responses when possible
- Batch operations where supported
- Contact support for limit increases

---

## SDKs & Examples

### cURL

#### List Brands

```bash
curl -X GET "https://api.brandcall.com/v1/brands" \
  -H "Authorization: Bearer bci_your_api_key" \
  -H "Accept: application/json"
```

#### Initiate Call

```bash
curl -X POST "https://api.brandcall.com/v1/brands/acme-corp/calls" \
  -H "Authorization: Bearer bci_your_api_key" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "from": "+14155551234",
    "to": "+14155559876",
    "call_reason": "Appointment Reminder"
  }'
```

#### Get Call Details

```bash
curl -X GET "https://api.brandcall.com/v1/brands/acme-corp/calls/call_8f7e6d5c4b3a2901" \
  -H "Authorization: Bearer bci_your_api_key" \
  -H "Accept: application/json"
```

---

### JavaScript / Node.js

#### Installation

```bash
npm install brandcall
# or
yarn add brandcall
```

#### Usage

```javascript
import BrandCall from 'brandcall';

const client = new BrandCall({
  apiKey: process.env.BRANDCALL_API_KEY,
});

// List brands
const brands = await client.brands.list();
console.log(brands.data);

// Get brand details
const brand = await client.brands.get('acme-corp');
console.log(brand.data);

// Initiate a branded call
const call = await client.calls.create('acme-corp', {
  from: '+14155551234',
  to: '+14155559876',
  callReason: 'Appointment Reminder',
  metadata: {
    appointmentId: 'appt_123',
  },
});
console.log('Call initiated:', call.data.id);

// Get call history
const calls = await client.calls.list('acme-corp', {
  status: 'completed',
  dateFrom: '2026-01-01',
  perPage: 50,
});

// Get analytics
const analytics = await client.analytics.get('acme-corp', {
  period: '30d',
  groupBy: 'day',
});
console.log('Answer rate:', analytics.data.summary.answer_rate);
```

#### Webhook Handling (Express.js)

```javascript
import express from 'express';
import { verifyWebhookSignature } from 'brandcall';

const app = express();

app.post('/webhooks/brandcall', express.raw({ type: 'application/json' }), (req, res) => {
  const signature = req.headers['x-brandcall-signature'];
  const webhookSecret = process.env.BRANDCALL_WEBHOOK_SECRET;
  
  if (!verifyWebhookSignature(req.body, signature, webhookSecret)) {
    return res.status(401).send('Invalid signature');
  }
  
  const event = JSON.parse(req.body);
  
  switch (event.type) {
    case 'call.completed':
      console.log(`Call ${event.data.call_id} completed`);
      console.log(`Duration: ${event.data.talk_duration_seconds}s`);
      break;
      
    case 'call.failed':
      console.error(`Call ${event.data.call_id} failed: ${event.data.failure_reason}`);
      break;
      
    case 'brand.status_changed':
      console.log(`Brand ${event.data.brand_slug} status: ${event.data.new_status}`);
      break;
  }
  
  res.status(200).send('OK');
});

app.listen(3000);
```

---

### PHP / Laravel

#### Installation

```bash
composer require brandcall/brandcall-php
```

#### Usage

```php
<?php

use BrandCall\BrandCall;
use BrandCall\Exceptions\ApiException;
use BrandCall\Exceptions\RateLimitException;

$client = new BrandCall(env('BRANDCALL_API_KEY'));

// List brands
$brands = $client->brands->list();
foreach ($brands->data as $brand) {
    echo $brand->name . ' - ' . $brand->status . "\n";
}

// Get brand details
$brand = $client->brands->get('acme-corp');
echo "Display Name: " . $brand->data->display_name . "\n";

// Initiate a branded call
try {
    $call = $client->calls->create('acme-corp', [
        'from' => '+14155551234',
        'to' => '+14155559876',
        'call_reason' => 'Appointment Reminder',
        'metadata' => [
            'appointment_id' => 'appt_123',
            'patient_name' => 'John Doe',
        ],
    ]);
    
    echo "Call initiated: " . $call->data->id . "\n";
    
} catch (RateLimitException $e) {
    echo "Rate limited. Retry after: " . $e->retryAfter . " seconds\n";
    
} catch (ApiException $e) {
    echo "API Error: " . $e->getMessage() . "\n";
    echo "Error Code: " . $e->code . "\n";
}

// Get call history with filters
$calls = $client->calls->list('acme-corp', [
    'status' => 'completed',
    'date_from' => '2026-01-01',
    'per_page' => 50,
]);

echo "Total calls: " . $calls->meta->total . "\n";

// Get analytics
$analytics = $client->analytics->get('acme-corp', [
    'period' => '30d',
    'group_by' => 'day',
]);

echo "Answer Rate: " . ($analytics->data->summary->answer_rate * 100) . "%\n";
echo "Total Calls: " . $analytics->data->summary->total_calls . "\n";
```

#### Laravel Service Provider

```php
// config/services.php
return [
    'brandcall' => [
        'api_key' => env('BRANDCALL_API_KEY'),
        'webhook_secret' => env('BRANDCALL_WEBHOOK_SECRET'),
    ],
];

// In a service provider
$this->app->singleton(BrandCall::class, function ($app) {
    return new BrandCall(config('services.brandcall.api_key'));
});
```

#### Webhook Handling (Laravel)

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BrandCallWebhookController extends Controller
{
    public function handle(Request $request)
    {
        // Verify signature
        $signature = $request->header('X-BrandCall-Signature');
        $expectedSignature = 'sha256=' . hash_hmac(
            'sha256',
            $request->getContent(),
            config('services.brandcall.webhook_secret')
        );
        
        if (!hash_equals($expectedSignature, $signature)) {
            abort(401, 'Invalid signature');
        }
        
        $event = $request->all();
        
        match ($event['type']) {
            'call.completed' => $this->handleCallCompleted($event['data']),
            'call.failed' => $this->handleCallFailed($event['data']),
            'brand.status_changed' => $this->handleBrandStatusChanged($event['data']),
            default => Log::info('Unhandled webhook type: ' . $event['type']),
        };
        
        return response('OK', 200);
    }
    
    private function handleCallCompleted(array $data): void
    {
        Log::info('Call completed', [
            'call_id' => $data['call_id'],
            'duration' => $data['talk_duration_seconds'],
            'cost' => $data['cost'],
        ]);
        
        // Update your database, send notifications, etc.
    }
    
    private function handleCallFailed(array $data): void
    {
        Log::warning('Call failed', [
            'call_id' => $data['call_id'],
            'reason' => $data['failure_reason'],
        ]);
    }
    
    private function handleBrandStatusChanged(array $data): void
    {
        Log::info('Brand status changed', [
            'brand' => $data['brand_slug'],
            'from' => $data['previous_status'],
            'to' => $data['new_status'],
        ]);
    }
}

// routes/api.php
Route::post('/webhooks/brandcall', [BrandCallWebhookController::class, 'handle'])
    ->withoutMiddleware(['csrf']);
```

---

### Python

```python
import os
import hmac
import hashlib
import requests
from typing import Optional

class BrandCallClient:
    def __init__(self, api_key: str, base_url: str = "https://api.brandcall.com/v1"):
        self.api_key = api_key
        self.base_url = base_url
        self.session = requests.Session()
        self.session.headers.update({
            "Authorization": f"Bearer {api_key}",
            "Content-Type": "application/json",
            "Accept": "application/json",
        })
    
    def list_brands(self, status: Optional[str] = None, per_page: int = 15):
        params = {"per_page": per_page}
        if status:
            params["status"] = status
        response = self.session.get(f"{self.base_url}/brands", params=params)
        response.raise_for_status()
        return response.json()
    
    def get_brand(self, slug: str):
        response = self.session.get(f"{self.base_url}/brands/{slug}")
        response.raise_for_status()
        return response.json()
    
    def create_call(self, brand_slug: str, from_number: str, to_number: str, 
                    call_reason: Optional[str] = None, metadata: Optional[dict] = None):
        payload = {
            "from": from_number,
            "to": to_number,
        }
        if call_reason:
            payload["call_reason"] = call_reason
        if metadata:
            payload["metadata"] = metadata
            
        response = self.session.post(
            f"{self.base_url}/brands/{brand_slug}/calls",
            json=payload
        )
        response.raise_for_status()
        return response.json()
    
    def list_calls(self, brand_slug: str, status: Optional[str] = None, 
                   per_page: int = 25):
        params = {"per_page": per_page}
        if status:
            params["status"] = status
        response = self.session.get(
            f"{self.base_url}/brands/{brand_slug}/calls",
            params=params
        )
        response.raise_for_status()
        return response.json()
    
    def get_analytics(self, brand_slug: str, period: str = "30d"):
        response = self.session.get(
            f"{self.base_url}/brands/{brand_slug}/analytics",
            params={"period": period}
        )
        response.raise_for_status()
        return response.json()


# Usage
client = BrandCallClient(os.environ["BRANDCALL_API_KEY"])

# List brands
brands = client.list_brands(status="active")
for brand in brands["data"]:
    print(f"{brand['name']} - {brand['status']}")

# Initiate call
call = client.create_call(
    brand_slug="acme-corp",
    from_number="+14155551234",
    to_number="+14155559876",
    call_reason="Appointment Reminder",
    metadata={"appointment_id": "appt_123"}
)
print(f"Call initiated: {call['data']['id']}")

# Get analytics
analytics = client.get_analytics("acme-corp", period="30d")
print(f"Answer rate: {analytics['data']['summary']['answer_rate'] * 100}%")


# Webhook verification
def verify_webhook(payload: bytes, signature: str, secret: str) -> bool:
    expected = "sha256=" + hmac.new(
        secret.encode(),
        payload,
        hashlib.sha256
    ).hexdigest()
    return hmac.compare_digest(expected, signature)
```

---

## Support

- **Documentation:** https://docs.brandcall.com
- **API Status:** https://status.brandcall.com
- **Support Email:** api-support@brandcall.com
- **Developer Slack:** https://brandcall-developers.slack.com

### Request ID

Every API response includes a `X-Request-Id` header. Include this when contacting support:

```http
X-Request-Id: req_a1b2c3d4e5f6g7h8
```

---

*API Version 1.0 | Last Updated: 2026-01-31*
