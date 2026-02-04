# NumHub BrandControl API - Testing Strategy

> Comprehensive testing approach for the NumHub API integration.
> Last Updated: 2026-02-04

---

## Overview

This document outlines the testing strategy for the NumHub BrandControl API integration in BrandCall. All tests use **Pest PHP** and follow Laravel testing conventions.

### Goals

1. **100% unit test coverage** for core services
2. **Complete isolation** from external APIs via mocking
3. **Fast CI/CD execution** (<30 seconds for unit tests)
4. **Realistic fixtures** matching actual API responses
5. **Clear failure diagnostics** for debugging

---

## Test Architecture

```
tests/
├── Unit/
│   └── Services/
│       └── NumHub/
│           ├── NumHubClientTest.php      # HTTP client, retries, headers
│           ├── TokenManagerTest.php       # Auth, caching, refresh
│           ├── ApplicationServiceTest.php # Business logic
│           └── DTOs/                      # Data transfer objects
├── Feature/
│   └── NumHub/
│       ├── ApplicationFlowTest.php       # Full application lifecycle
│       ├── IdentityManagementTest.php    # Display identity CRUD
│       ├── AttestationFlowTest.php       # STIR/SHAKEN flow
│       └── WebhookHandlerTest.php        # Incoming webhooks
├── Fixtures/
│   └── NumHub/                           # JSON response fixtures
└── Mocks/
    └── NumHubMock.php                    # Mock helper class
```

---

## Unit Test Coverage Plan

### 1. NumHubClient (HTTP Layer)

| Method | Test Cases |
|--------|------------|
| `request()` | ✓ Adds required headers (Authorization, client-id, X-Auth-Scheme) |
| `request()` | ✓ Handles successful 2xx responses |
| `request()` | ✓ Throws `NumHubAuthException` on 401 |
| `request()` | ✓ Throws `NumHubRateLimitException` on 429 |
| `request()` | ✓ Throws `NumHubValidationException` on 400 |
| `request()` | ✓ Throws `NumHubException` on 5xx errors |
| `request()` | ✓ Retries on timeout (configurable) |
| `request()` | ✓ Logs all requests to audit trail |
| `get()`/`post()`/`put()`/`delete()` | ✓ Delegates to request() correctly |

### 2. TokenManager (Authentication)

| Method | Test Cases |
|--------|------------|
| `getToken()` | ✓ Returns cached token if valid |
| `getToken()` | ✓ Fetches new token if cache empty |
| `getToken()` | ✓ Refreshes token if within expiry buffer (5 min) |
| `authenticate()` | ✓ Sends correct credentials to /authorize/token |
| `authenticate()` | ✓ Stores token with correct TTL |
| `authenticate()` | ✓ Throws on invalid credentials |
| `invalidate()` | ✓ Clears cached token |
| `getUserInfo()` | ✓ Fetches and caches user info |

### 3. ApplicationService (Business Logic)

| Method | Test Cases |
|--------|------------|
| `create()` | ✓ Creates application with correct payload |
| `create()` | ✓ Maps BrandCall model to NumHub DTO |
| `create()` | ✓ Stores NumhubEntityId mapping |
| `update()` | ✓ Updates existing application |
| `submit()` | ✓ Changes status from Saved to Submit |
| `getStatus()` | ✓ Returns application status |
| `uploadDocument()` | ✓ Handles multipart file upload |
| `generateOtp()` | ✓ Triggers OTP for verification |
| `verifyOtp()` | ✓ Completes OTP verification |

### 4. IdentityService

| Method | Test Cases |
|--------|------------|
| `list()` | ✓ Returns paginated identities |
| `get()` | ✓ Returns single identity by ID |
| `update()` | ✓ Updates display identity |
| `deactivate()` | ✓ Submits deactivation request |
| `uploadPhoneNumbers()` | ✓ Bulk uploads TNs |
| `deletePhoneNumbers()` | ✓ Removes TNs from identity |

### 5. ReportService

| Method | Test Cases |
|--------|------------|
| `getSettlementReports()` | ✓ Returns settlement data |
| `getStatusReport()` | ✓ Returns application status counts |
| `getDealsReport()` | ✓ Returns deal metrics |

---

## Feature Test Scenarios

### Application Flow (End-to-End)

```php
// tests/Feature/NumHub/ApplicationFlowTest.php

it('completes full application lifecycle', function () {
    // 1. Create application (draft)
    // 2. Upload required documents
    // 3. Generate and verify OTP
    // 4. Submit for review
    // 5. Check status changes
    // 6. Handle approval webhook
});

it('handles application rejection gracefully', function () {
    // Submit → Rejected → User notification
});

it('allows resubmission after rejection', function () {
    // Rejected → Fix issues → Resubmit
});
```

### Identity Management Flow

```php
// tests/Feature/NumHub/IdentityManagementTest.php

it('creates display identity from brand', function () {
    // Brand model → NumHub identity creation
});

it('bulk uploads phone numbers to identity', function () {
    // CSV upload → NumHub bulk TN add
});

it('handles deactivation request', function () {
    // User request → NumHub deactivation
});
```

### Error Handling Scenarios

```php
it('retries on rate limit with exponential backoff', function () {
    // First call: 429 → Wait → Retry → Success
});

it('refreshes token on 401 and retries', function () {
    // First call: 401 → Refresh token → Retry → Success
});

it('surfaces validation errors to user', function () {
    // 400 error → Parse errors → User-friendly message
});
```

---

## Mock API Response Strategy

### Fixture Files

All fixtures are stored in `tests/Fixtures/NumHub/` and loaded by `NumHubMock`:

| Fixture | Purpose |
|---------|---------|
| `token-response.json` | Successful authentication |
| `application-created.json` | New application response |
| `application-approved.json` | Approved application |
| `application-rejected.json` | Rejected with reasons |
| `identity-list.json` | Paginated identity list |
| `settlement-report.json` | Monthly settlement data |
| `error-400.json` | Validation error response |
| `error-401.json` | Unauthorized response |
| `error-429.json` | Rate limit response |

### NumHubMock Helper

```php
// Usage in tests
NumHubMock::fake([
    '/api/v1/authorize/token' => NumHubMock::token(),
    '/api/v1/application' => NumHubMock::applicationCreated(),
    '/api/v1/application/*' => NumHubMock::applicationApproved(),
]);

// Simulate failures
NumHubMock::fakeSequence('/api/v1/authorize/token')
    ->push(NumHubMock::error401())   // First call fails
    ->push(NumHubMock::token());      // Retry succeeds

// Simulate rate limiting
NumHubMock::fakeRateLimit('/api/v1/application', retryAfter: 60);
```

### HTTP Fake Pattern

```php
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    Http::fake([
        'brandidentity-api.numhub.com/*' => Http::response(
            NumHubMock::fixture('token-response'),
            200
        ),
    ]);
});

it('authenticates with NumHub', function () {
    $client = app(NumHubClient::class);
    $token = $client->authenticate();

    expect($token)->not->toBeNull();
    Http::assertSent(fn ($request) =>
        $request->hasHeader('X-Auth-Scheme', 'ATLAASROPG')
    );
});
```

---

## CI/CD Integration

### GitHub Actions Workflow

```yaml
# .github/workflows/test.yml

name: Tests

on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest

    steps:
      - uses: actions/checkout@v4

      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.4'
          coverage: xdebug

      - name: Install Dependencies
        run: composer install --no-interaction

      - name: Run Unit Tests
        run: php artisan test --testsuite=Unit --parallel

      - name: Run Feature Tests
        run: php artisan test --testsuite=Feature --parallel

      - name: Upload Coverage
        uses: codecov/codecov-action@v3
        with:
          files: coverage.xml
```

### Test Suites Configuration

```xml
<!-- phpunit.xml -->
<testsuites>
    <testsuite name="Unit">
        <directory>tests/Unit</directory>
    </testsuite>
    <testsuite name="Feature">
        <directory>tests/Feature</directory>
    </testsuite>
    <testsuite name="NumHub">
        <directory>tests/Unit/Services/NumHub</directory>
        <directory>tests/Feature/NumHub</directory>
    </testsuite>
</testsuites>
```

### Running Tests

```bash
# All tests
composer test

# NumHub integration tests only
php artisan test --testsuite=NumHub

# With coverage
php artisan test --coverage --min=80

# Specific test file
php artisan test tests/Unit/Services/NumHub/NumHubClientTest.php
```

---

## Test Data Factories

### NumHub Entity Factory

```php
// database/factories/NumHubEntityFactory.php

namespace Database\Factories;

use App\Models\NumHubEntity;
use Illuminate\Database\Eloquent\Factories\Factory;

class NumHubEntityFactory extends Factory
{
    protected $model = NumHubEntity::class;

    public function definition(): array
    {
        return [
            'numhub_entity_id' => $this->faker->uuid(),
            'business_id' => Business::factory(),
            'status' => 'pending',
            'submitted_at' => null,
            'approved_at' => null,
        ];
    }

    public function approved(): static
    {
        return $this->state(fn () => [
            'status' => 'approved',
            'submitted_at' => now()->subDays(7),
            'approved_at' => now()->subDay(),
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn () => [
            'status' => 'rejected',
            'submitted_at' => now()->subDays(7),
            'rejection_reason' => 'Invalid documentation',
        ]);
    }
}
```

---

## Coverage Requirements

| Component | Minimum Coverage |
|-----------|-----------------|
| `NumHubClient` | 90% |
| `TokenManager` | 95% |
| `ApplicationService` | 85% |
| `IdentityService` | 85% |
| `DTOs` | 100% |
| **Overall NumHub** | **85%** |

### Measuring Coverage

```bash
# Generate HTML coverage report
XDEBUG_MODE=coverage php artisan test --coverage-html=coverage-report

# Check minimum threshold
php artisan test --coverage --min=85
```

---

## Test Organization Best Practices

### 1. Use Descriptive Test Names

```php
// ✅ Good
it('refreshes expired token before making API request')
it('throws NumHubRateLimitException when rate limit exceeded')

// ❌ Bad
it('works')
it('test token')
```

### 2. Arrange-Act-Assert Pattern

```php
it('creates application from brand', function () {
    // Arrange
    $brand = Brand::factory()->create();
    NumHubMock::fake(['*' => NumHubMock::applicationCreated()]);

    // Act
    $result = app(ApplicationService::class)->create($brand);

    // Assert
    expect($result)->toHaveKey('NumhubEntityId');
    expect($brand->fresh()->numhub_entity_id)->not->toBeNull();
});
```

### 3. Isolate External Dependencies

```php
// Always mock HTTP in unit tests
beforeEach(function () {
    Http::fake();
    Cache::flush();
});

// Reset between tests
afterEach(function () {
    Http::assertNothingSent(); // Verify no unexpected calls
});
```

### 4. Test Edge Cases

```php
it('handles empty response body', function () { ... });
it('handles malformed JSON response', function () { ... });
it('handles network timeout', function () { ... });
it('handles concurrent token refresh', function () { ... });
```

---

## Exception Testing

### Custom Exceptions

```php
// app/Exceptions/NumHub/
NumHubException::class           // Base exception
NumHubAuthException::class       // 401 errors
NumHubRateLimitException::class  // 429 errors
NumHubValidationException::class // 400 errors
NumHubServerException::class     // 5xx errors
```

### Testing Exceptions

```php
it('throws NumHubAuthException on 401', function () {
    Http::fake(['*' => Http::response(null, 401)]);

    expect(fn () => $client->get('/api/v1/application'))
        ->toThrow(NumHubAuthException::class);
});

it('includes retry-after in rate limit exception', function () {
    Http::fake(['*' => Http::response(null, 429, ['Retry-After' => '60'])]);

    try {
        $client->get('/api/v1/application');
    } catch (NumHubRateLimitException $e) {
        expect($e->retryAfter)->toBe(60);
    }
});
```

---

## Contract/Interface Testing

When implementing the `VoiceProvider` interface for NumHub:

```php
// Ensure NumHubDriver implements all interface methods
it('implements VoiceProvider interface', function () {
    $driver = new NumHubDriver(config('voice.drivers.numhub'));

    expect($driver)->toBeInstanceOf(VoiceProvider::class);
});

// Test against interface contract
it('returns array from call method', function () {
    $driver = new NumHubDriver(config('voice.drivers.numhub'));
    $brand = Brand::factory()->create();

    $result = $driver->call($brand, '+15551234567', '+15559876543');

    expect($result)->toBeArray();
    expect($result)->toHaveKeys(['success', 'call_sid']);
});
```

---

## Quick Reference Commands

```bash
# Run all NumHub tests
php artisan test tests/Unit/Services/NumHub tests/Feature/NumHub

# Run with verbose output
php artisan test --filter=NumHub -v

# Run specific test method
php artisan test --filter="it creates application from brand"

# Generate coverage for NumHub only
XDEBUG_MODE=coverage php artisan test tests/Unit/Services/NumHub \
    --coverage-html=coverage-report

# Watch mode (requires phpunit-watcher)
./vendor/bin/phpunit-watcher watch --testsuite=NumHub
```

---

## Next Steps

1. [ ] Create fixture files in `tests/Fixtures/NumHub/`
2. [ ] Implement `NumHubMock` helper class
3. [ ] Write unit tests for `NumHubClient`
4. [ ] Write unit tests for `TokenManager`
5. [ ] Write feature tests for application flow
6. [ ] Add NumHub test suite to CI/CD pipeline
7. [ ] Set up coverage reporting

---

*This strategy should be updated as the integration evolves.*
