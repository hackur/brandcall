# NumHub BrandControl API Integration - TODO

> Generated from: https://brandidentity-api.numhub.com/docs/index.html
> Date: 2026-02-04
> API Version: V1 (rel. 231011)

## API Overview

| Property | Value |
|----------|-------|
| Base URL | `https://brandidentity-api.numhub.com` |
| Auth | Bearer token (24h expiry) |
| Rate Limit | 100 requests/minute |
| Auth Scheme | `X-Auth-Scheme: ATLAASROPG` |
| Support | BCIDsupport@numhub.com / 844-468-6482 |

---

## Phase 1: Foundation (Week 1)

### 1.1 Authentication Service
- [ ] Create `NumHubClient` service class
- [ ] Implement `/api/v1/authorize/token` - token generation
  - Email + Password → Access Token
  - Store token with expiry tracking
  - Auto-refresh before expiry
- [ ] Implement `/api/v1/authorize/userInfo` - fetch user details
- [ ] Add `client-id` and `X-Auth-Scheme` headers to all requests
- [ ] Handle 401 (token expired) with auto-retry
- [ ] Handle 429 (rate limit) with backoff

### 1.2 Configuration
- [ ] Add to `.env`:
  ```
  NUMHUB_API_URL=https://brandidentity-api.numhub.com
  NUMHUB_EMAIL=
  NUMHUB_PASSWORD=
  NUMHUB_CLIENT_ID=
  ```
- [ ] Create config/numhub.php
- [ ] Store credentials securely (encrypted)

### 1.3 Database Schema
- [ ] `numhub_tokens` - cache access tokens
- [ ] `numhub_entities` - track NumhubEntityId mappings
- [ ] `numhub_identities` - track NumhubIdentityId mappings
- [ ] `numhub_sync_log` - API call audit trail

---

## Phase 2: Application Management (Week 2)

### 2.1 Submit BCID Application
- [ ] `POST /api/v1/application` - Submit new application
  - Status 1 = Saved (draft)
  - Status 2 = Submit (for processing)
- [ ] Map BrandCall `Business` model → NumHub `SaveBCApplicationModel`
- [ ] Store returned `NumhubEntityId` 

### 2.2 Update Application
- [ ] `PUT /api/v1/application/{NumhubEntityId}` - Update existing
- [ ] Sync changes from BrandCall → NumHub

### 2.3 Get Application Status
- [ ] `GET /api/v1/application/{NumhubEntityId}` - Fetch details
- [ ] `GET /api/v1/application/{NumhubEntityId}/view` - View mode
- [ ] `GET /api/v1/application/vetting-report/{NumhubEntityId}` - With vetting
- [ ] `GET /api/v1/application/{clientId}/completedEntities` - List completed

### 2.4 Application Documents
- [ ] `POST /api/v1/application/{NumhubEntityId}/documents` - Upload LOA, etc.
- [ ] `DELETE /api/v1/application/{NumhubEntityId}/documents/{documentId}`
- [ ] `GET /api/v1/application/{NumhubEntityId}/downloadtemplate` - LOA template

### 2.5 OTP Verification (Step 1)
- [ ] `POST /api/v1/application/{NumhubEntityId}/generateOtp` - Send OTP
- [ ] `POST /api/v1/application/{NumhubEntityId}/verifyOtp` - Verify OTP

---

## Phase 3: Display Identity Management (Week 3)

### 3.1 Caller ID Display
- [ ] `GET /api/v1/applications/newdisplayidentity` - List caller IDs
- [ ] `GET /api/v1/applications/newidentities/{NumhubIdentityId}` - Get by ID
- [ ] `PUT /api/v1/applications/updatedisplayidentity` - Update display data
- [ ] `PUT /api/v1/applications/deactivationrequest` - Deactivate identity

### 3.2 Phone Number Management
- [ ] `POST /api/v1/applications/{NumhubIdentityId}/uploadadditionaltns` - Bulk upload
- [ ] `DELETE /api/v1/applications/{NumhubIdentityId}/additionaltns` - Remove
- [ ] `GET /api/v1/applications/downloadphonenumbers` - Export XLSX

---

## Phase 4: Attestation (Week 4)

### 4.1 STIR/SHAKEN Attestation
- [ ] `GET /api/v1/application/attestation/{clientId}` - Get attestation entities
- [ ] `PUT /api/v1/application/attestation/{NumhubEntityId}` - Submit attestation
- [ ] Map attestation levels (A/B/C) to UI

---

## Phase 5: Reports & Notifications (Week 5)

### 5.1 Settlement Reports
- [ ] `GET /api/v1/confirmationReports/settlementReports` - BCID usage/billing
- [ ] Build settlement dashboard in Filament

### 5.2 Status Reports
- [ ] `GET /api/v1/reports/status` - Application status counts
- [ ] `GET /api/v1/reports/deals` - Deal reports by period

### 5.3 Notifications/Alerts
- [ ] `GET /api/v1/notifications` - Flagged application alerts
- [ ] `GET /api/v1/notifications/{NumhubEntityId}/flag` - Specific flags
- [ ] Display alerts in BrandCall admin

---

## Phase 6: OSP/Reseller Features (Week 6)

### 6.1 Deals Management (for reseller model)
- [ ] `GET /api/v1/deals` - List deals
- [ ] `POST /api/v1/deals` - Create deal (Enterprise or BPO)
- [ ] `PUT /api/v1/deals/{dealId}` - Update deal
- [ ] `POST /api/v1/deals/{id}/resendEmail` - Resend registration

### 6.2 Fee Management
- [ ] `GET /api/v1/deals/fees/{ospId}` - Get default fees
- [ ] `POST /api/v1/deals/fees/{ospId}` - Create fee structure
- [ ] `PUT /api/v1/deals/fees/{ospId}` - Update fees

### 6.3 Enterprise/EID Lists
- [ ] `GET /api/v1/application/{ospId}/eids` - List EIDs for OSP
- [ ] `GET /api/v1/application/{EID}/enterprise` - Get by EID

---

## API Request Headers (All Requests)

```php
[
    'Authorization' => 'Bearer ' . $accessToken,
    'client-id' => config('numhub.client_id'),
    'X-Auth-Scheme' => 'ATLAASROPG',
    'Content-Type' => 'application/json',
    'Accept' => 'application/json',
]
```

---

## Error Handling

| Code | Meaning | Action |
|------|---------|--------|
| 400 | Bad Request | Log error, show validation message |
| 401 | Unauthorized | Refresh token, retry |
| 403 | Forbidden | Check permissions, alert admin |
| 404 | Not Found | Entity doesn't exist |
| 429 | Rate Limited | Backoff, retry after delay |

---

## Data Models to Create

### Laravel Models
- [ ] `NumHubToken` - Token storage
- [ ] `NumHubEntity` - Entity mapping (business → NumhubEntityId)
- [ ] `NumHubIdentity` - Identity mapping (phone → NumhubIdentityId)
- [ ] `NumHubDocument` - Document tracking
- [ ] `NumHubSyncLog` - API audit log

### DTOs
- [ ] `SaveBCApplicationModel` - Create application
- [ ] `UpdateBCApplicationModel` - Update application
- [ ] `BrandControlDeals` - Deal management
- [ ] `OspDefaultFeeModel` - Fee structure

---

## Filament Admin Pages

- [ ] NumHub Settings (credentials, connection test)
- [ ] Application Status Dashboard
- [ ] Settlement Reports viewer
- [ ] Sync Log / API audit trail
- [ ] Alert notifications panel

---

## Testing

- [ ] Unit tests for NumHubClient
- [ ] Feature tests for application flow
- [ ] Mock API responses for CI
- [ ] Test with NumHub sandbox credentials

---

## Notes from Meeting (2026-02-04)

*Add meeting notes here after call*

---

## References

- API Docs: https://brandidentity-api.numhub.com/docs/index.html
- Swagger JSON: https://brandidentity-api.numhub.com/docs/swagger.json
- Support: BCIDsupport@numhub.com | 844-468-6482
