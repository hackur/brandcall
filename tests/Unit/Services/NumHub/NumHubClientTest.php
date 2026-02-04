<?php

/**
 * BrandCall - Branded Caller ID SaaS Platform.
 *
 * @author     BrandCall Development Team
 * @copyright  2024-2026 BrandCall
 * @license    Proprietary
 */

declare(strict_types=1);

use App\Services\NumHub\NumHubClient;
use Illuminate\Support\Facades\Http;
use Tests\Mocks\NumHubMock;

/**
 * Unit tests for NumHubClient - the HTTP layer for NumHub API communication.
 *
 * Tests cover:
 * - Request header configuration
 * - HTTP method delegation
 * - Error handling and exceptions
 * - Retry logic
 * - Request/response logging
 *
 * @see \App\Services\NumHub\NumHubClient
 */
beforeEach(function () {
    // Reset HTTP fakes before each test
    Http::fake();

    // Clear any cached tokens
    cache()->forget('numhub_access_token');
});

// =============================================================================
// HEADER CONFIGURATION
// =============================================================================

describe('request headers', function () {
    it('includes Authorization header with bearer token', function () {
        NumHubMock::fake([
            '/api/v1/application' => NumHubMock::applicationCreated(),
        ]);

        $client = app(NumHubClient::class);
        $client->get('/api/v1/application');

        Http::assertSent(fn ($request) => str_starts_with(
            $request->header('Authorization')[0] ?? '',
            'Bearer '
        ));
    })->skip('Implement NumHubClient first');

    it('includes client-id header', function () {
        NumHubMock::fake([
            '/api/v1/application' => NumHubMock::applicationCreated(),
        ]);

        $client = app(NumHubClient::class);
        $client->get('/api/v1/application');

        Http::assertSent(fn ($request) => $request->hasHeader('client-id'));
    })->skip('Implement NumHubClient first');

    it('includes X-Auth-Scheme header with ATLAASROPG value', function () {
        NumHubMock::fake([
            '/api/v1/application' => NumHubMock::applicationCreated(),
        ]);

        $client = app(NumHubClient::class);
        $client->get('/api/v1/application');

        Http::assertSent(fn ($request) => $request->header('X-Auth-Scheme')[0] === 'ATLAASROPG'
        );
    })->skip('Implement NumHubClient first');

    it('sets Content-Type to application/json', function () {
        NumHubMock::fake([
            '/api/v1/application' => NumHubMock::applicationCreated(),
        ]);

        $client = app(NumHubClient::class);
        $client->post('/api/v1/application', ['data' => 'test']);

        Http::assertSent(fn ($request) => str_contains(
            $request->header('Content-Type')[0] ?? '',
            'application/json'
        ));
    })->skip('Implement NumHubClient first');

    it('sets Accept to application/json', function () {
        NumHubMock::fake([
            '/api/v1/application' => NumHubMock::applicationCreated(),
        ]);

        $client = app(NumHubClient::class);
        $client->get('/api/v1/application');

        Http::assertSent(fn ($request) => $request->header('Accept')[0] === 'application/json'
        );
    })->skip('Implement NumHubClient first');
});

// =============================================================================
// HTTP METHODS
// =============================================================================

describe('HTTP methods', function () {
    it('sends GET requests correctly', function () {
        NumHubMock::fake([
            '/api/v1/application/ent_123' => NumHubMock::applicationApproved(),
        ]);

        $client = app(NumHubClient::class);
        $response = $client->get('/api/v1/application/ent_123');

        Http::assertSent(fn ($request) => $request->method() === 'GET'
        );

        expect($response)->toBeArray();
        expect($response)->toHaveKey('NumhubEntityId');
    })->skip('Implement NumHubClient first');

    it('sends POST requests with body', function () {
        NumHubMock::fake([
            '/api/v1/application' => NumHubMock::applicationCreated(),
        ]);

        $client = app(NumHubClient::class);
        $data = ['companyInfo' => ['legalName' => 'Test Company']];
        $response = $client->post('/api/v1/application', $data);

        Http::assertSent(fn ($request) => $request->method() === 'POST' &&
            $request['companyInfo']['legalName'] === 'Test Company'
        );
    })->skip('Implement NumHubClient first');

    it('sends PUT requests for updates', function () {
        NumHubMock::fake([
            '/api/v1/application/ent_123' => NumHubMock::applicationApproved(),
        ]);

        $client = app(NumHubClient::class);
        $client->put('/api/v1/application/ent_123', ['status' => 2]);

        Http::assertSent(fn ($request) => $request->method() === 'PUT'
        );
    })->skip('Implement NumHubClient first');

    it('sends DELETE requests', function () {
        NumHubMock::fake([
            '/api/v1/application/ent_123/documents/doc_1' => [],
        ]);

        $client = app(NumHubClient::class);
        $client->delete('/api/v1/application/ent_123/documents/doc_1');

        Http::assertSent(fn ($request) => $request->method() === 'DELETE'
        );
    })->skip('Implement NumHubClient first');
});

// =============================================================================
// SUCCESS RESPONSES
// =============================================================================

describe('successful responses', function () {
    it('returns decoded JSON for 200 responses', function () {
        NumHubMock::fake([
            '/api/v1/application/ent_123' => NumHubMock::applicationApproved(),
        ]);

        $client = app(NumHubClient::class);
        $response = $client->get('/api/v1/application/ent_123');

        expect($response)->toBeArray();
        expect($response['NumhubEntityId'])->toBe('ent_abc123def456');
        expect($response['status'])->toBe(5);
    })->skip('Implement NumHubClient first');

    it('returns decoded JSON for 201 responses', function () {
        Http::fake([
            '*' => Http::response(NumHubMock::applicationCreated(), 201),
        ]);

        $client = app(NumHubClient::class);
        $response = $client->post('/api/v1/application', []);

        expect($response)->toBeArray();
        expect($response['NumhubEntityId'])->not->toBeNull();
    })->skip('Implement NumHubClient first');

    it('handles empty 204 responses', function () {
        Http::fake([
            '*' => Http::response(null, 204),
        ]);

        $client = app(NumHubClient::class);
        $response = $client->delete('/api/v1/application/ent_123/documents/doc_1');

        expect($response)->toBeNull();
    })->skip('Implement NumHubClient first');
});

// =============================================================================
// ERROR HANDLING
// =============================================================================

describe('error handling', function () {
    it('throws NumHubAuthException on 401 response', function () {
        Http::fake([
            '*' => Http::response(NumHubMock::error401(), 401),
        ]);

        $client = app(NumHubClient::class);
        $client->get('/api/v1/application');
    })->throws(\App\Exceptions\NumHub\NumHubAuthException::class)
        ->skip('Implement NumHubClient and exceptions first');

    it('throws NumHubRateLimitException on 429 response', function () {
        Http::fake([
            '*' => Http::response(NumHubMock::error429(), 429, ['Retry-After' => '60']),
        ]);

        $client = app(NumHubClient::class);
        $client->get('/api/v1/application');
    })->throws(\App\Exceptions\NumHub\NumHubRateLimitException::class)
        ->skip('Implement NumHubClient and exceptions first');

    it('includes retry-after in rate limit exception', function () {
        Http::fake([
            '*' => Http::response(NumHubMock::error429(45), 429, ['Retry-After' => '45']),
        ]);

        $client = app(NumHubClient::class);

        try {
            $client->get('/api/v1/application');
        } catch (\App\Exceptions\NumHub\NumHubRateLimitException $e) {
            expect($e->retryAfter)->toBe(45);
        }
    })->skip('Implement NumHubClient and exceptions first');

    it('throws NumHubValidationException on 400 response', function () {
        Http::fake([
            '*' => Http::response(NumHubMock::error400(), 400),
        ]);

        $client = app(NumHubClient::class);
        $client->post('/api/v1/application', []);
    })->throws(\App\Exceptions\NumHub\NumHubValidationException::class)
        ->skip('Implement NumHubClient and exceptions first');

    it('includes validation errors in exception', function () {
        Http::fake([
            '*' => Http::response(NumHubMock::error400(), 400),
        ]);

        $client = app(NumHubClient::class);

        try {
            $client->post('/api/v1/application', []);
        } catch (\App\Exceptions\NumHub\NumHubValidationException $e) {
            expect($e->errors)->toBeArray();
            expect($e->errors)->not->toBeEmpty();
        }
    })->skip('Implement NumHubClient and exceptions first');

    it('throws NumHubServerException on 500 response', function () {
        Http::fake([
            '*' => Http::response(NumHubMock::serverError(), 500),
        ]);

        $client = app(NumHubClient::class);
        $client->get('/api/v1/application');
    })->throws(\App\Exceptions\NumHub\NumHubServerException::class)
        ->skip('Implement NumHubClient and exceptions first');

    it('throws NumHubException on other 4xx errors', function () {
        Http::fake([
            '*' => Http::response(['error' => 'Forbidden'], 403),
        ]);

        $client = app(NumHubClient::class);
        $client->get('/api/v1/application');
    })->throws(\App\Exceptions\NumHub\NumHubException::class)
        ->skip('Implement NumHubClient and exceptions first');
});

// =============================================================================
// RETRY LOGIC
// =============================================================================

describe('retry logic', function () {
    it('retries on connection timeout', function () {
        $attempts = 0;

        Http::fake(function () use (&$attempts) {
            $attempts++;
            if ($attempts < 3) {
                throw new \Illuminate\Http\Client\ConnectionException('Connection timed out');
            }

            return Http::response(NumHubMock::applicationCreated(), 200);
        });

        $client = app(NumHubClient::class);
        $response = $client->get('/api/v1/application');

        expect($attempts)->toBe(3);
        expect($response)->toHaveKey('NumhubEntityId');
    })->skip('Implement NumHubClient with retry logic first');

    it('gives up after max retries', function () {
        Http::fake(function () {
            throw new \Illuminate\Http\Client\ConnectionException('Connection timed out');
        });

        $client = app(NumHubClient::class);
        $client->get('/api/v1/application');
    })->throws(\App\Exceptions\NumHub\NumHubConnectionException::class)
        ->skip('Implement NumHubClient with retry logic first');

    it('does not retry on validation errors', function () {
        $attempts = 0;

        Http::fake(function () use (&$attempts) {
            $attempts++;

            return Http::response(NumHubMock::error400(), 400);
        });

        $client = app(NumHubClient::class);

        try {
            $client->post('/api/v1/application', []);
        } catch (\Exception $e) {
            // Expected
        }

        expect($attempts)->toBe(1);
    })->skip('Implement NumHubClient first');
});

// =============================================================================
// AUDIT LOGGING
// =============================================================================

describe('audit logging', function () {
    it('logs successful requests', function () {
        NumHubMock::fake([
            '/api/v1/application' => NumHubMock::applicationCreated(),
        ]);

        $client = app(NumHubClient::class);
        $client->post('/api/v1/application', ['test' => 'data']);

        // Assert log was created in numhub_sync_log table
        $this->assertDatabaseHas('numhub_sync_log', [
            'endpoint' => '/api/v1/application',
            'method' => 'POST',
            'status_code' => 200,
        ]);
    })->skip('Implement NumHubClient and sync log first');

    it('logs failed requests', function () {
        Http::fake([
            '*' => Http::response(NumHubMock::error400(), 400),
        ]);

        $client = app(NumHubClient::class);

        try {
            $client->post('/api/v1/application', []);
        } catch (\Exception $e) {
            // Expected
        }

        $this->assertDatabaseHas('numhub_sync_log', [
            'endpoint' => '/api/v1/application',
            'method' => 'POST',
            'status_code' => 400,
        ]);
    })->skip('Implement NumHubClient and sync log first');

    it('does not log sensitive data in request body', function () {
        NumHubMock::fake([
            '/api/v1/authorize/token' => NumHubMock::token(),
        ]);

        $client = app(NumHubClient::class);
        $client->post('/api/v1/authorize/token', [
            'email' => 'test@example.com',
            'password' => 'secret123',
        ]);

        // Password should be masked in logs
        $log = \App\Models\NumHubSyncLog::latest()->first();
        expect($log->request_body)->not->toContain('secret123');
    })->skip('Implement NumHubClient and sync log first');
});

// =============================================================================
// BASE URL CONFIGURATION
// =============================================================================

describe('configuration', function () {
    it('uses base URL from config', function () {
        config(['numhub.api_url' => 'https://test-api.numhub.com']);

        NumHubMock::fake([
            '/api/v1/application' => NumHubMock::applicationCreated(),
        ]);

        $client = app(NumHubClient::class);
        $client->get('/api/v1/application');

        Http::assertSent(fn ($request) => str_contains($request->url(), 'test-api.numhub.com')
        );
    })->skip('Implement NumHubClient first');

    it('uses client ID from config', function () {
        config(['numhub.client_id' => 'test_client_123']);

        NumHubMock::fake([
            '/api/v1/application' => NumHubMock::applicationCreated(),
        ]);

        $client = app(NumHubClient::class);
        $client->get('/api/v1/application');

        Http::assertSent(fn ($request) => $request->header('client-id')[0] === 'test_client_123'
        );
    })->skip('Implement NumHubClient first');
});
