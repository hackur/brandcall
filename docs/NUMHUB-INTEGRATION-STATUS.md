# NumHub Integration Status

> Current state of NumHub BrandControl API integration for BrandCall.

**Last Updated:** 2026-02-05  
**Status:** 🟡 Ready for Testing

---

## Executive Summary

| Area | Status | Notes |
|------|--------|-------|
| **API Docs** | ✅ Complete | Swagger downloaded locally |
| **Test Portal** | ✅ Ready | Account created, Google OAuth login |
| **API Credentials** | 🟡 Pending | Test account uses Google OAuth |
| **Authentication** | 📋 Designed | `TokenManager` class created, not tested |
| **Application Management** | 📋 Designed | Endpoints documented |
| **Document Uploads** | 📋 Designed | Pass-through architecture planned |
| **Display Identity** | 📋 Planned | Not started |
| **Attestation** | 📋 Planned | Not started |
| **Webhooks** | 📋 Planned | Not started |

---

## Portal Access

### Test Portal

**URL:** `https://www.atlaas.app/signin`  
**Login:** Google OAuth (jcsarda@gmail.com)  
**Account Created:** 2026-02-05 at 7:24 AM by Daniela Villamar  

> **Important:** There is NO separate test/sandbox URL. The same portal serves both test and production. Test vs production is determined by **client assignment** on your account. Jeremy's account is tied to a test client with test data.

### What You'll See

Per Daniela: "Jeremy now has access to our test portal and OSP environment. He may see a lot of data and activity, but please note that everything there is test data only."

---

## API Documentation

### Downloaded Locally

| File | Location |
|------|----------|
| Swagger JSON | `/Volumes/JS-DEV/brandcall/docs/numhub-swagger.json` |

### Online Resources

- **API Docs UI:** https://brandidentity-api.numhub.com/docs/index.html
- **Swagger JSON:** https://brandidentity-api.numhub.com/docs/swagger.json
- **BCID E-Book:** (see Drew's email, Feb 3)

### API Base URL

```
https://brandidentity-api.numhub.com/api/v1/
```

---

## NumHub Contacts

| Name | Role | Email | Phone |
|------|------|-------|-------|
| Daniela Villamar | Product Manager | dvillamar@numhub.com | 541-395-6121 |
| Drew Andersen | Director of Sales | dandersen@numhub.com | 541-598-8300 |
| Sara Hutchinson | Director of Operations | shutchinson@numhub.com | 541-598-2329 |
| Support | | BCIDsupport@numhub.com | 844-468-6482 |

---

## Next Steps

1. **Jeremy:** Try logging into https://www.atlaas.app/signin with Google
2. If login fails, contact Daniela (account may need time to propagate)
3. Explore the OSP test environment and document the UI workflow
4. Get API credentials (may be visible in portal, or ask Daniela)
5. Test token acquisition with `TokenManager`

---

## Blocking Items

### 1. NumHub API Credentials (for programmatic access)

**What we need:**
- API Email/Username (may be jcsarda@gmail.com)
- API Password (may use OAuth token exchange)
- Client ID (`client-id` header)
- Auth Scheme confirmation (`ATLAASROPG`)

**Who provides:** SmashByte (NumHub reseller partnership)

**Status:** Test account created 2026-02-05. Need to explore portal to find API credentials or confirm OAuth-to-API flow.

---

## Implementation Status by Phase

### Phase 1: Authentication (Week 1) - 🟡 In Progress

| Task | Status | Notes |
|------|--------|-------|
| `NumHubClient` service class | ✅ Designed | `/app/Services/NumHub/NumHubClient.php` |
| `TokenManager` for auth | ✅ Designed | `/app/Services/NumHub/TokenManager.php` |
| Token endpoint integration | ⏸️ Blocked | Needs credentials to test |
| Config/environment setup | ✅ Ready | `.env` variables defined |
| Rate limiting (100 req/min) | 📋 Planned | |
| 401 auto-retry with refresh | 📋 Planned | |

**Files Created:**
- `app/Services/NumHub/TokenManager.php` - Token acquisition and caching
- `tests/Unit/Services/NumHub/TokenManagerTest.php` - Unit tests (mocked)

**Environment Variables (add to `.env`):**
```env
NUMHUB_API_URL=https://brandidentity-api.numhub.com
NUMHUB_EMAIL=
NUMHUB_PASSWORD=
NUMHUB_CLIENT_ID=
```

---

### Phase 2: Application Management (Week 2) - 📋 Planned

| Task | Status | Endpoint |
|------|--------|----------|
| Submit BCID Application | 📋 Planned | `POST /api/v1/application` |
| Update Application | 📋 Planned | `PUT /api/v1/application/{id}` |
| Get Application Status | 📋 Planned | `GET /api/v1/application/{id}` |
| Get Vetting Report | 📋 Planned | `GET /api/v1/application/vetting-report/{id}` |
| Upload Documents | 📋 Planned | `POST /api/v1/application/{id}/documents` |
| Delete Documents | 📋 Planned | `DELETE /api/v1/application/{id}/documents/{docId}` |
| Download LOA Template | 📋 Planned | `GET /api/v1/application/{id}/downloadtemplate` |
| OTP Generation | 📋 Planned | `POST /api/v1/application/{id}/generateOtp` |
| OTP Verification | 📋 Planned | `POST /api/v1/application/{id}/verifyOtp` |

---

### Phase 3: Display Identity Management (Week 3) - 📋 Planned

| Task | Status | Endpoint |
|------|--------|----------|
| List Caller IDs | 📋 Planned | `GET /api/v1/applications/newdisplayidentity` |
| Get Display Identity | 📋 Planned | `GET /api/v1/applications/newidentities/{id}` |
| Update Display Identity | 📋 Planned | `PUT /api/v1/applications/updatedisplayidentity` |
| Deactivate Identity | 📋 Planned | `PUT /api/v1/applications/deactivationrequest` |
| Upload Additional TNs | 📋 Planned | `POST /api/v1/applications/{id}/uploadadditionaltns` |
| Remove TNs | 📋 Planned | `DELETE /api/v1/applications/{id}/additionaltns` |
| Export Phone Numbers | 📋 Planned | `GET /api/v1/applications/downloadphonenumbers` |

---

### Phase 4: STIR/SHAKEN Attestation (Week 4) - 📋 Planned

| Task | Status | Endpoint |
|------|--------|----------|
| Get Attestation Entities | 📋 Planned | `GET /api/v1/application/attestation/{clientId}` |
| Submit Attestation | 📋 Planned | `PUT /api/v1/application/attestation/{id}` |

---

### Phase 5: Reports & Notifications (Week 5) - 📋 Planned

| Task | Status | Endpoint |
|------|--------|----------|
| Settlement Reports | 📋 Planned | `GET /api/v1/confirmationReports/settlementReports` |
| Status Reports | 📋 Planned | `GET /api/v1/reports/status` |
| Deal Reports | 📋 Planned | `GET /api/v1/reports/deals` |
| Get Notifications | 📋 Planned | `GET /api/v1/notifications` |
| Get Flagged Apps | 📋 Planned | `GET /api/v1/notifications/{id}/flag` |

---

### Phase 6: OSP/Reseller Features (Week 6) - 📋 Planned

| Task | Status | Endpoint |
|------|--------|----------|
| List Deals | 📋 Planned | `GET /api/v1/deals` |
| Create Deal | 📋 Planned | `POST /api/v1/deals` |
| Update Deal | 📋 Planned | `PUT /api/v1/deals/{id}` |
| Resend Registration Email | 📋 Planned | `POST /api/v1/deals/{id}/resendEmail` |
| Get OSP Fees | 📋 Planned | `GET /api/v1/deals/fees/{ospId}` |
| Set OSP Fees | 📋 Planned | `POST /api/v1/deals/fees/{ospId}` |
| Update OSP Fees | 📋 Planned | `PUT /api/v1/deals/fees/{ospId}` |

---

## Code Architecture

### Service Layer

```
app/Services/NumHub/
├── NumHubClient.php        # Main HTTP client (pending)
├── TokenManager.php        # Auth token management (created)
├── ApplicationService.php  # BCID application CRUD (pending)
├── DocumentService.php     # Document uploads (pending)
├── IdentityService.php     # Display identity management (pending)
├── ReportService.php       # Settlement & reports (pending)
└── WebhookHandler.php      # Incoming webhook processor (pending)
```

### Database Schema

```sql
-- Track NumHub entity mappings
CREATE TABLE numhub_entities (
    id BIGINT PRIMARY KEY,
    tenant_id BIGINT NOT NULL,
    numhub_entity_id UUID NOT NULL,
    entity_type VARCHAR(50) NOT NULL,
    local_entity_type VARCHAR(50) NOT NULL,
    local_entity_id BIGINT NOT NULL,
    status VARCHAR(50) DEFAULT 'pending',
    vetting_status VARCHAR(50),
    synced_at TIMESTAMP,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- Track NumHub documents (metadata only, not files)
CREATE TABLE numhub_documents (
    id BIGINT PRIMARY KEY,
    numhub_entity_id UUID NOT NULL,
    numhub_document_id UUID,
    document_type VARCHAR(50) NOT NULL,
    filename VARCHAR(255),
    status VARCHAR(50) DEFAULT 'pending',
    rejection_reason TEXT,
    uploaded_at TIMESTAMP,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- API call audit log
CREATE TABLE numhub_sync_log (
    id BIGINT PRIMARY KEY,
    endpoint VARCHAR(255) NOT NULL,
    method VARCHAR(10) NOT NULL,
    request_data JSON,
    response_code INT,
    response_data JSON,
    error_message TEXT,
    duration_ms INT,
    created_at TIMESTAMP
);
```

### Config File

```php
// config/numhub.php
return [
    'api_url' => env('NUMHUB_API_URL', 'https://brandidentity-api.numhub.com'),
    'email' => env('NUMHUB_EMAIL'),
    'password' => env('NUMHUB_PASSWORD'),
    'client_id' => env('NUMHUB_CLIENT_ID'),
    'auth_scheme' => env('NUMHUB_AUTH_SCHEME', 'ATLAASROPG'),
    'timeout' => env('NUMHUB_TIMEOUT', 30),
    'retry_attempts' => env('NUMHUB_RETRY_ATTEMPTS', 3),
];
```

---

## Document Upload Architecture

### Pass-Through Strategy (Recommended)

To avoid storing sensitive documents (driver's licenses, government IDs) on our servers:

```
┌────────────┐    ┌───────────────────┐    ┌─────────────┐
│   User     │───▶│  BrandCall API    │───▶│  NumHub API │
│  Browser   │    │  (pass-through)   │    │   Storage   │
└────────────┘    └───────────────────┘    └─────────────┘
                         │
                         ▼
                  Store metadata only:
                  - filename
                  - document_type
                  - numhub_document_id
                  - status
```

### Implementation Steps

1. **Upload Endpoint**
   ```php
   POST /api/v1/documents/upload
   Content-Type: multipart/form-data
   
   - file: binary
   - type: business_license|drivers_license|etc
   - entity_id: NumHub entity UUID
   ```

2. **Validation Layer**
   - Check file format (PDF for LOA, BMP for logo)
   - Check file size (max 10MB)
   - Validate document type
   - Check user authorization

3. **NumHub Proxy**
   - Forward file to NumHub API
   - Map document type to NumHub type (LOA/LOGO/DOCUMENTS)
   - Return NumHub document ID

4. **Metadata Storage**
   - Store in `numhub_documents` table
   - Do NOT store file content

5. **Status Sync**
   - Poll or receive webhook for document status
   - Update local status accordingly

### NumHub Document Types Mapping

| BrandCall Type | NumHub DocumentType |
|----------------|---------------------|
| `loa` | `LOA` |
| `logo` | `LOGO` |
| All others | `DOCUMENTS` |

### File Format Requirements

| NumHub Type | Accepted Formats |
|-------------|------------------|
| `LOA` | PDF only |
| `LOGO` | BMP only |
| `DOCUMENTS` | PDF, XLSX, CSV |

---

## Testing Strategy

### Unit Tests (Mocked)

All NumHub service classes have corresponding unit tests with mocked HTTP responses:

```bash
# Run NumHub-specific tests
php artisan test --filter=NumHub
```

### Integration Tests (Requires Credentials)

Once credentials are available:

```bash
# Run against NumHub sandbox
php artisan test --filter=NumHubIntegration --env=testing
```

### Sandbox Environment

**Confirmed:** NumHub does NOT have a separate sandbox URL. Test vs production is controlled by **client assignment**:
- Test account: associated with test client, sees test data only
- Production account: associated with live client(s)
- Same portal URL: `https://www.atlaas.app/signin`
- Same API URL: `https://brandidentity-api.numhub.com`

---

## Webhook Integration

### Expected Webhook Events

| Event | Trigger | Action |
|-------|---------|--------|
| `application.submitted` | Application created | Update local status |
| `application.approved` | Vetting passed | Enable branded calling |
| `application.rejected` | Vetting failed | Notify user, show rejection reason |
| `document.approved` | Document accepted | Update document status |
| `document.rejected` | Document rejected | Notify user, show reason |
| `identity.activated` | Display identity live | Enable calling with this identity |
| `identity.suspended` | Identity suspended | Disable calling, notify |

### Webhook Handler

```php
// routes/api.php
Route::post('/webhooks/numhub', [NumHubWebhookController::class, 'handle'])
    ->middleware('verify.numhub.signature');
```

### Signature Verification

NumHub likely signs webhooks. Implementation TBD based on their documentation.

---

## Error Handling

### HTTP Status Codes

| Code | Meaning | Action |
|------|---------|--------|
| 400 | Bad Request | Log, show validation error |
| 401 | Unauthorized | Refresh token, retry |
| 403 | Forbidden | Check permissions, alert admin |
| 404 | Not Found | Entity doesn't exist |
| 429 | Rate Limited | Backoff, retry after delay |
| 500 | Server Error | Log, retry with backoff |

### Retry Strategy

```php
// Exponential backoff: 1s, 2s, 4s
$attempts = 3;
$delay = 1000; // ms

for ($i = 0; $i < $attempts; $i++) {
    try {
        return $this->client->request(...);
    } catch (RateLimitException $e) {
        sleep($delay / 1000);
        $delay *= 2;
    }
}
```

---

## Filament Admin Integration

### Planned Admin Pages

- **NumHub Settings** - API credentials, connection test
- **Application Dashboard** - View all BCID applications
- **Document Review** - See document statuses (links to NumHub)
- **Settlement Reports** - Usage and billing data
- **Sync Log** - API call history for debugging

---

## Next Steps

### Immediate (Once Credentials Received)

1. Add credentials to `.env`
2. Test token acquisition
3. Verify API connectivity
4. Test document upload flow

### Week 1

1. Complete TokenManager integration
2. Build ApplicationService
3. Create document upload endpoint
4. Test with sandbox credentials

### Week 2

1. Build Filament admin pages
2. Implement webhook handler
3. Test end-to-end onboarding flow

---

## Questions for NumHub/SmashByte

1. ✅ **Sandbox environment** - ANSWERED: No separate URL. Same portal/API, test vs prod controlled by client assignment.
2. ✅ **Test credentials** - ANSWERED: Account created for jcsarda@gmail.com with Google OAuth login.
3. **Webhook documentation** - What events are available? How are they signed?
4. **Direct upload** - Any plans for client-side signed URL uploads?
5. **Rate limits** - Is 100 req/min confirmed? Any burst allowance?
6. **SLA** - What's the API availability target?
7. **API credentials** - Does Google OAuth login work for API calls, or do we need separate API credentials?

---

## References

- [NumHub API Docs](https://brandidentity-api.numhub.com/docs/index.html)
- [NumHub Swagger](https://brandidentity-api.numhub.com/docs/swagger.json)
- [TODO-NUMHUB-INTEGRATION.md](./TODO-NUMHUB-INTEGRATION.md) - Detailed endpoint checklist
- [NUMHUB.md](./NUMHUB.md) - Platform overview and FAQs
- [NUMHUB-INTEGRATION.md](./NUMHUB-INTEGRATION.md) - API patterns guide

---

*Contact: BCIDsupport@numhub.com | 844-468-6482*
