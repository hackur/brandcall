# NumHub BrandControl API Client Design

> Service class architecture for NumHub BrandControl API integration
> Version: 1.0 | Date: 2026-02-04

---

## Overview

The `NumHubClient` is a dedicated service class for interacting with the NumHub BrandControl API. This is **separate** from the `NumHubDriver` voice provider - the BrandControl API handles BCID application management, entity registration, and compliance workflows.

### API Reference
- **Base URL**: `https://brandidentity-api.numhub.com`
- **Auth**: Bearer token (24h expiry)
- **Rate Limit**: 100 requests/minute
- **Auth Scheme**: `X-Auth-Scheme: ATLAASROPG`
- **Support**: BCIDsupport@numhub.com | 844-468-6482

---

## Architecture

### Directory Structure

```
app/Services/NumHub/
├── NumHubClient.php           # Main API client
├── NumHubTokenManager.php     # Token caching/refresh
├── Contracts/
│   └── NumHubClientInterface.php
├── DTOs/
│   ├── AccessToken.php
│   ├── Application.php
│   ├── ApplicationStatus.php
│   ├── ApplicantInformation.php
│   ├── BusinessEntityDetails.php
│   ├── BusinessIdentityDetails.php
│   ├── ConsumerProtectionContacts.php
│   ├── DisplayIdentity.php
│   ├── Deal.php
│   ├── Document.php
│   ├── SettlementReport.php
│   ├── AttestationEntity.php
│   └── PaginatedResult.php
├── Enums/
│   ├── ApplicationStatus.php
│   ├── DocumentType.php
│   ├── RoleType.php
│   └── OrgType.php
└── Exceptions/
    ├── NumHubException.php
    ├── AuthenticationException.php
    ├── RateLimitException.php
    ├── ValidationException.php
    └── EntityNotFoundException.php
```

### Class Diagram

```
┌─────────────────────────────────────────────────────────────────┐
│                        NumHubClient                              │
├─────────────────────────────────────────────────────────────────┤
│ - tokenManager: NumHubTokenManager                               │
│ - config: array                                                  │
│ - httpClient: PendingRequest                                     │
├─────────────────────────────────────────────────────────────────┤
│ + authenticate(): AccessToken                                    │
│ + getUserInfo(): UserInfo                                        │
│ + createApplication(dto): CreateEntityResponse                   │
│ + updateApplication(entityId, dto): CreateEntityResponse         │
│ + getApplication(entityId): Application                          │
│ + listApplications(filters): PaginatedResult                     │
│ + uploadDocument(entityId, file, type): UploadDocumentResponse   │
│ + generateOtp(entityId): bool                                    │
│ + verifyOtp(entityId, otp): bool                                 │
│ + listDisplayIdentities(filters): array<DisplayIdentity>         │
│ + updateDisplayIdentity(dto): bool                               │
│ + deactivateIdentity(identityId): bool                           │
│ + getSettlementReports(filters): PaginatedResult                 │
│ + listDeals(filters): PaginatedResult                            │
│ + createDeal(dto): int                                           │
│ + getAttestationEntities(clientId): PaginatedResult              │
│ + submitAttestation(entityId, dto): bool                         │
│ + getNotifications(): array<Alert>                               │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                     NumHubTokenManager                           │
├─────────────────────────────────────────────────────────────────┤
│ - cache: CacheRepository                                         │
│ - config: array                                                  │
├─────────────────────────────────────────────────────────────────┤
│ + getToken(): string                                             │
│ + refreshToken(): AccessToken                                    │
│ + invalidateToken(): void                                        │
│ + isTokenExpired(): bool                                         │
└─────────────────────────────────────────────────────────────────┘
```

---

## Authentication Flow

### Token Lifecycle

```
┌──────────────┐     ┌─────────────────┐     ┌──────────────┐
│ API Request  │────▶│ Token Manager   │────▶│ Cache Check  │
└──────────────┘     └─────────────────┘     └──────────────┘
                              │                      │
                              │            ┌─────────┴─────────┐
                              │            │                   │
                              ▼            ▼                   ▼
                     ┌────────────────┐  Cache Hit        Cache Miss
                     │ Validate Token │     │                  │
                     │ (not expired?) │     │                  │
                     └────────────────┘     │                  │
                              │             │                  ▼
                              ▼             │     ┌────────────────────┐
                     ┌────────────────┐     │     │ POST /authorize/   │
                     │ Return Token   │◀────┴─────│ token              │
                     └────────────────┘           └────────────────────┘
                                                          │
                                                          ▼
                                                  ┌────────────────────┐
                                                  │ Cache Token        │
                                                  │ (TTL: 23h 30m)     │
                                                  └────────────────────┘
```

### Token Caching Strategy

```php
// Cache key format
$cacheKey = 'numhub:token:' . md5(config('numhub.email'));

// TTL: 23.5 hours (30 min buffer before 24h expiry)
$ttl = 23.5 * 60 * 60; // 84,600 seconds

// Token response structure
[
    'access_token' => 'eyJ...',
    'token_type' => 'Bearer',
    'expires_in' => 86400,
    'client_id' => 123,
    'user' => [...],
    'user_roles' => [...],
    'user_clients' => [...],
]
```

### Required Headers (All Requests)

```php
[
    'Authorization' => 'Bearer ' . $accessToken,
    'client-id' => $clientId,               // From token response
    'X-Auth-Scheme' => 'ATLAASROPG',        // Required auth scheme
    'Content-Type' => 'application/json',
    'Accept' => 'application/json',
]
```

---

## API Endpoints by Domain

### 1. Authorization

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/v1/authorize/token` | Generate access token (email/password) |
| GET | `/api/v1/authorize/userInfo` | Get user info, clients, roles |

### 2. Application Management

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/v1/application` | Create new BCID application |
| PUT | `/api/v1/application/{entityId}` | Update existing application |
| GET | `/api/v1/application/{entityId}` | Get application details |
| GET | `/api/v1/application/{entityId}/view` | Get application (view mode) |
| GET | `/api/v1/application/vetting-report/{entityId}` | Get with vetting report |
| GET | `/api/v1/application/{clientId}/completedEntities` | List completed apps |
| GET | `/api/v1/application/{EID}/enterprise` | Get by Enterprise ID |
| GET | `/api/v1/application/{ospId}/eids` | List EIDs for OSP |

### 3. Application Documents

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/v1/application/{entityId}/documents` | Upload document (LOA, LOGO, DOCUMENTS) |
| DELETE | `/api/v1/application/{entityId}/documents/{docId}` | Delete document |
| GET | `/api/v1/application/{entityId}/downloadtemplate` | Download LOA template |

### 4. OTP Verification

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/v1/application/{entityId}/generateOtp` | Send OTP to applicant |
| POST | `/api/v1/application/{entityId}/verifyOtp` | Verify OTP code |

### 5. Application Authorization (Admin)

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/v1/application/{entityId}/authorize` | Approve/reject application |
| GET | `/api/v1/application/{entityId}/notes/{noteId}` | Get note/comment |

### 6. Display Identity Management

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/v1/applications/newdisplayidentity` | List caller identities |
| GET | `/api/v1/applications/newidentities/{identityId}` | Get identity by ID |
| PUT | `/api/v1/applications/updatedisplayidentity` | Update display identity |
| PUT | `/api/v1/applications/deactivationrequest` | Deactivate identity |
| POST | `/api/v1/applications/{identityId}/uploadadditionaltns` | Bulk upload TNs |
| DELETE | `/api/v1/applications/{identityId}/additionaltns` | Remove TNs |
| GET | `/api/v1/applications/downloadphonenumbers` | Export phone numbers |

### 7. Attestation (STIR/SHAKEN)

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/v1/application/attestation/{clientId}` | List attestation entities |
| PUT | `/api/v1/application/attestation/{entityId}` | Submit attestation |

### 8. Deals Management (OSP/Reseller)

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/v1/deals` | List deals (paginated, filterable) |
| POST | `/api/v1/deals` | Create new deal (Enterprise/BPO) |
| GET | `/api/v1/deals/{dealId}` | Get deal by ID |
| PUT | `/api/v1/deals/{dealId}` | Update deal |
| POST | `/api/v1/deals/{id}/resendEmail` | Resend registration email |

### 9. Fee Management

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/v1/deals/fees/{ospId}` | Get default fees |
| POST | `/api/v1/deals/fees/{ospId}` | Create fee structure |
| PUT | `/api/v1/deals/fees/{ospId}` | Update fees |

### 10. Reports

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/v1/confirmationReports/settlementReports` | BCID settlement reports |
| GET | `/api/v1/reports/status` | Application status counts |
| GET | `/api/v1/reports/deals` | Deal reports by period |

### 11. Notifications

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/v1/notifications` | Flagged application alerts |
| GET | `/api/v1/notifications/{entityId}/flag` | Get specific flags |

---

## Data Transfer Objects (DTOs)

### SaveBCApplicationModel (Create Application)

```php
readonly class SaveBCApplicationModel
{
    public function __construct(
        public int $status,  // 1=Saved, 2=Submit
        public ApplicantInformation $applicantInformation,
        public BusinessEntityDetails $businessEntityDetails,
        public BusinessIdentityDetails $businessIdentityDetails,
        public ConsumerProtectionContacts $consumerProtectionContacts,
        public ?BusinessAgentOfRecord $businessAgentOfRecord = null,
        public ?References $references = null,
        public ?DisplayIdentity $displayIdentityInformation = null,
        public ?RegisterCertificate $registerCertificate = null,
    ) {}
}
```

### ApplicantInformation

```php
readonly class ApplicantInformation
{
    public function __construct(
        public string $firstName,
        public string $lastName,
        public string $emailAddress,
        public string $phoneNumber,
        public ?array $ospIds = null,
        public bool $isVerified = false,
    ) {}
}
```

### BusinessEntityDetails

```php
readonly class BusinessEntityDetails
{
    public function __construct(
        public string $legalBusinessName,
        public string $businessWebsite,
        public string $primaryBusinessAddress,
        public string $city,
        public string $state,
        public string $zipCode,
        public string $phone,
        public ?string $originatingServiceProvider = null,
    ) {}
}
```

### BusinessIdentityDetails

```php
readonly class BusinessIdentityDetails
{
    public function __construct(
        public ?string $federalEmployerIdNumber = null,
        public ?string $dunAndBradstreetNumber = null,
        public ?string $stateCorporateRegistrationNumber = null,
        public ?string $stateProfessionalLicenseNumber = null,
        public ?string $primaryBusinessDomainSicCode = null,
    ) {}
}
```

### DisplayIdentity

```php
readonly class DisplayIdentity
{
    public function __construct(
        public string $callerName,
        public ?string $logoUrl = null,
        public ?string $callReason = null,
        public array $phoneNumbers = [],
        public ?string $applicationId = null,
    ) {}
}
```

### CreateEntityResponse

```php
readonly class CreateEntityResponse
{
    public function __construct(
        public string $numhubEntityId,    // UUID
        public ?string $applicationId = null,
        public ?string $eid = null,        // Enterprise ID
    ) {}
}
```

### AccessToken

```php
readonly class AccessToken
{
    public function __construct(
        public string $accessToken,
        public string $tokenType,
        public int $expiresIn,
        public int $clientId,
        public array $user,
        public array $userRoles,
        public array $userClients,
        public Carbon $expiresAt,
    ) {}

    public function isExpired(): bool
    {
        return $this->expiresAt->isPast();
    }
}
```

### BrandControlDeals (Create/Update Deal)

```php
readonly class BrandControlDeals
{
    public function __construct(
        public string $dealName,
        public string $customerEmailAddress,
        public string $customerPrimaryContactFirstName,
        public string $customerPrimaryContactLastName,
        public RoleType $roleType,    // Enterprise, BPO
        public OrgType $orgType,      // CommercialEnterprise, GovtPublicService, CharityNonProfit
        public ?string $operationsEmail = null,
        public ?string $customerPhoneNumber = null,
        public ?string $customerAddress = null,
        public ?string $customerCity = null,
        public ?string $customerState = null,
        public ?string $customerZipCode = null,
        public ?float $vettingFee = null,
        public ?float $retailFee = null,
        public ?float $platformFee = null,
        public ?float $callVolume = null,
    ) {}
}
```

---

## Enums

### ApplicationStatus

```php
enum ApplicationStatus: string
{
    case PENDING_REVIEW = 'PendingReview';
    case COMPLETE = 'Complete';
    case SAVED = 'Saved';
    case REJECTED = 'Rejected';
    case RESUBMITTED = 'Resubmitted';
    case IN_PROGRESS = 'InProgress';
    case SUBMITTED = 'Submitted';
}
```

### DocumentType

```php
enum DocumentType: string
{
    case LOA = 'LOA';           // PDF only
    case LOGO = 'LOGO';         // BMP only
    case DOCUMENTS = 'DOCUMENTS'; // PDF, XLSX, CSV
}
```

### RoleType

```php
enum RoleType: string
{
    case NONE = 'None';
    case ENTERPRISE = 'Enterprise';
    case BPO = 'BPO';
}
```

### OrgType

```php
enum OrgType: string
{
    case NONE = 'None';
    case COMMERCIAL_ENTERPRISE = 'CommercialEnterprise';
    case GOVT_PUBLIC_SERVICE = 'GovtPublicService';
    case CHARITY_NON_PROFIT = 'CharityNonProfit';
}
```

---

## Error Handling Strategy

### Exception Hierarchy

```php
NumHubException (base)
├── AuthenticationException (401)
├── AuthorizationException (403)
├── EntityNotFoundException (404)
├── ValidationException (400)
├── RateLimitException (429)
└── ApiException (500, network errors)
```

### Error Response Structure

```php
readonly class NumHubErrorResponse
{
    public function __construct(
        public bool $success,
        public string $message,
        public ?array $errors = null,
    ) {}
}
```

### Retry Strategy

```php
// Automatic retry configuration
[
    'retries' => 3,
    'retry_delay' => 1000, // ms
    'retry_on' => [429, 500, 502, 503, 504],
    'backoff' => 'exponential', // 1s, 2s, 4s
]
```

### Error Handling Implementation

```php
protected function handleResponse(Response $response): array
{
    $status = $response->status();
    $body = $response->json();

    return match (true) {
        $status === 401 => throw new AuthenticationException(
            $body['message'] ?? 'Authentication failed'
        ),
        $status === 403 => throw new AuthorizationException(
            $body['message'] ?? 'Access denied'
        ),
        $status === 404 => throw new EntityNotFoundException(
            $body['message'] ?? 'Entity not found'
        ),
        $status === 429 => throw new RateLimitException(
            'Rate limit exceeded. Try again later.',
            $response->header('Retry-After')
        ),
        $status >= 400 && $status < 500 => throw new ValidationException(
            $body['message'] ?? 'Validation failed',
            $body['errors'] ?? []
        ),
        $status >= 500 => throw new ApiException(
            $body['message'] ?? 'NumHub API error'
        ),
        default => $body,
    };
}
```

---

## Rate Limiting Approach

### Limits
- **100 requests per minute** across all endpoints
- Response includes `requests-remaining` header

### Implementation

```php
class RateLimiter
{
    private const CACHE_KEY = 'numhub:rate_limit';
    private const MAX_REQUESTS = 100;
    private const WINDOW_SECONDS = 60;

    public function attempt(): bool
    {
        $current = Cache::get(self::CACHE_KEY, 0);

        if ($current >= self::MAX_REQUESTS) {
            return false;
        }

        Cache::put(
            self::CACHE_KEY,
            $current + 1,
            now()->addSeconds(self::WINDOW_SECONDS)
        );

        return true;
    }

    public function remaining(): int
    {
        return max(0, self::MAX_REQUESTS - Cache::get(self::CACHE_KEY, 0));
    }

    public function waitUntilAvailable(): void
    {
        while (!$this->attempt()) {
            sleep(1);
        }
    }
}
```

### Rate Limit Handling

```php
protected function makeRequest(string $method, string $endpoint, array $data = []): array
{
    if (!$this->rateLimiter->attempt()) {
        throw new RateLimitException(
            'Rate limit reached. ' . $this->rateLimiter->remaining() . ' requests remaining.'
        );
    }

    $response = $this->client()->{$method}($endpoint, $data);

    // Update remaining from response header
    if ($remaining = $response->header('requests-remaining')) {
        $this->rateLimiter->updateRemaining((int) $remaining);
    }

    return $this->handleResponse($response);
}
```

---

## Configuration

### Environment Variables

```env
NUMHUB_API_URL=https://brandidentity-api.numhub.com
NUMHUB_EMAIL=api@brandcall.io
NUMHUB_PASSWORD=your-secure-password
NUMHUB_CLIENT_ID=
# If using mock mode for development
NUMHUB_MOCK=true
```

### Config File (config/numhub.php)

```php
<?php

return [
    /*
    |--------------------------------------------------------------------------
    | NumHub BrandControl API Configuration
    |--------------------------------------------------------------------------
    */

    'api_url' => env('NUMHUB_API_URL', 'https://brandidentity-api.numhub.com'),
    'email' => env('NUMHUB_EMAIL'),
    'password' => env('NUMHUB_PASSWORD'),
    'client_id' => env('NUMHUB_CLIENT_ID'),

    /*
    |--------------------------------------------------------------------------
    | Authentication
    |--------------------------------------------------------------------------
    */
    'auth_scheme' => 'ATLAASROPG',
    'token_cache_ttl' => 23.5 * 60 * 60, // 23.5 hours (buffer before 24h expiry)

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    */
    'rate_limit' => [
        'max_requests' => 100,
        'window_seconds' => 60,
    ],

    /*
    |--------------------------------------------------------------------------
    | HTTP Client
    |--------------------------------------------------------------------------
    */
    'timeout' => 30,
    'retry' => [
        'times' => 3,
        'sleep' => 1000,
        'when' => [429, 500, 502, 503, 504],
    ],

    /*
    |--------------------------------------------------------------------------
    | Mock Mode
    |--------------------------------------------------------------------------
    */
    'mock' => env('NUMHUB_MOCK', false),
];
```

---

## Service Provider Registration

```php
// app/Providers/NumHubServiceProvider.php

namespace App\Providers;

use App\Services\NumHub\Contracts\NumHubClientInterface;
use App\Services\NumHub\NumHubClient;
use App\Services\NumHub\NumHubTokenManager;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class NumHubServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public function register(): void
    {
        $this->app->singleton(NumHubTokenManager::class, function ($app) {
            return new NumHubTokenManager(
                $app['cache']->store(),
                $app['config']['numhub']
            );
        });

        $this->app->singleton(NumHubClientInterface::class, function ($app) {
            return new NumHubClient(
                $app->make(NumHubTokenManager::class),
                $app['config']['numhub']
            );
        });

        $this->app->alias(NumHubClientInterface::class, 'numhub');
    }

    public function provides(): array
    {
        return [
            NumHubClientInterface::class,
            NumHubTokenManager::class,
            'numhub',
        ];
    }
}
```

---

## Usage Examples

### Basic Authentication

```php
$client = app(NumHubClient::class);

// Get token (auto-cached)
$token = $client->authenticate();

// Get user info
$userInfo = $client->getUserInfo();
```

### Create BCID Application

```php
use App\Services\NumHub\DTOs\{
    SaveBCApplicationModel,
    ApplicantInformation,
    BusinessEntityDetails,
    BusinessIdentityDetails,
    ConsumerProtectionContacts
};

$application = new SaveBCApplicationModel(
    status: 2, // Submit immediately
    applicantInformation: new ApplicantInformation(
        firstName: 'John',
        lastName: 'Smith',
        emailAddress: 'john@company.com',
        phoneNumber: '+14155551234',
    ),
    businessEntityDetails: new BusinessEntityDetails(
        legalBusinessName: 'Acme Corp',
        businessWebsite: 'https://acme.com',
        primaryBusinessAddress: '123 Main St',
        city: 'San Francisco',
        state: 'CA',
        zipCode: '94105',
        phone: '+14155551234',
    ),
    businessIdentityDetails: new BusinessIdentityDetails(
        federalEmployerIdNumber: '12-3456789',
    ),
    consumerProtectionContacts: new ConsumerProtectionContacts(
        consumerComplaintEmail: 'complaints@acme.com',
        consumerComplaintPhoneNumber: '+14155551234',
    ),
);

$response = $client->createApplication($application);
// Returns: CreateEntityResponse with numhubEntityId, applicationId, eid
```

### Upload Document

```php
$response = $client->uploadDocument(
    entityId: $numhubEntityId,
    file: $request->file('loa'),
    type: DocumentType::LOA,
    description: 'Letter of Authorization'
);
```

### List Applications with Filters

```php
$result = $client->listApplications([
    'pageNumber' => 1,
    'pageSize' => 25,
    'status' => ApplicationStatus::PENDING_REVIEW,
    'startDate' => now()->subMonth(),
    'endDate' => now(),
]);

foreach ($result->items as $application) {
    // Process each application
}
```

### Manage Display Identities

```php
// List caller IDs
$identities = $client->listDisplayIdentities([
    'searchKey' => 'Acme',
    'pageNumber' => 1,
    'pageSize' => 50,
]);

// Update display identity
$client->updateDisplayIdentity(
    identityId: $identityId,
    callerName: 'Acme Support',
    callReason: 'Customer Service',
    logoUrl: 'https://cdn.acme.com/logo.bmp',
);

// Deactivate identity
$client->deactivateIdentity($identityId);
```

### Settlement Reports

```php
$reports = $client->getSettlementReports([
    'startDate' => now()->startOfMonth(),
    'endDate' => now(),
    'clientIds' => [123, 456],
]);

foreach ($reports->items as $report) {
    echo "EID: {$report->eId}, Claims: {$report->claimsPassed}";
}
```

---

## Testing Strategy

### Unit Tests

```php
class NumHubClientTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock HTTP responses
        Http::fake([
            '*/authorize/token' => Http::response([
                'accessToken' => 'test-token',
                'tokenType' => 'Bearer',
                'expiresIn' => 86400,
                'user' => ['userId' => 1],
                'userRoles' => [],
                'userClients' => [['clientId' => 123]],
            ]),
            '*/application' => Http::response([
                'success' => true,
                'result' => [
                    'numhubEntityId' => 'uuid-here',
                    'applicationId' => 'APP-001',
                ],
            ]),
        ]);
    }

    public function test_can_create_application(): void
    {
        $client = app(NumHubClient::class);

        $response = $client->createApplication($this->getTestApplicationDto());

        $this->assertNotNull($response->numhubEntityId);
        $this->assertEquals('APP-001', $response->applicationId);
    }

    public function test_handles_rate_limit(): void
    {
        Http::fake([
            '*' => Http::response(['message' => 'Rate limited'], 429),
        ]);

        $this->expectException(RateLimitException::class);

        app(NumHubClient::class)->authenticate();
    }
}
```

### Feature Tests

```php
class NumHubIntegrationTest extends TestCase
{
    /** @group integration */
    public function test_full_application_flow(): void
    {
        $this->markTestSkipped('Requires NumHub sandbox credentials');

        $client = app(NumHubClient::class);

        // 1. Create application (saved)
        $createResponse = $client->createApplication($dto);

        // 2. Upload documents
        $client->uploadDocument($createResponse->numhubEntityId, $file, DocumentType::LOA);

        // 3. Generate OTP
        $client->generateOtp($createResponse->numhubEntityId);

        // 4. Verify OTP (would need actual OTP)
        // $client->verifyOtp($createResponse->numhubEntityId, $otp);

        // 5. Submit application
        $dto->status = 2;
        $client->updateApplication($createResponse->numhubEntityId, $dto);
    }
}
```

---

## Migration Schema

```php
// database/migrations/xxxx_create_numhub_tables.php

Schema::create('numhub_entities', function (Blueprint $table) {
    $table->id();
    $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
    $table->uuid('numhub_entity_id')->unique();
    $table->string('application_id')->nullable();
    $table->string('eid')->nullable()->index(); // Enterprise ID
    $table->string('status')->default('Saved');
    $table->json('raw_response')->nullable();
    $table->timestamps();
});

Schema::create('numhub_identities', function (Blueprint $table) {
    $table->id();
    $table->foreignId('numhub_entity_id')->constrained('numhub_entities')->cascadeOnDelete();
    $table->uuid('numhub_identity_id')->unique();
    $table->string('dir_id')->nullable()->index();
    $table->string('caller_name');
    $table->string('call_reason')->nullable();
    $table->string('logo_url')->nullable();
    $table->string('status')->default('Active');
    $table->timestamps();
});

Schema::create('numhub_sync_logs', function (Blueprint $table) {
    $table->id();
    $table->string('entity_type'); // application, identity, document
    $table->string('entity_id');
    $table->string('action'); // create, update, delete, sync
    $table->string('status'); // success, failed
    $table->json('request')->nullable();
    $table->json('response')->nullable();
    $table->text('error_message')->nullable();
    $table->timestamp('created_at');
    $table->index(['entity_type', 'entity_id', 'created_at']);
});
```

---

## Changelog

| Version | Date | Changes |
|---------|------|---------|
| 1.0 | 2026-02-04 | Initial design document |
