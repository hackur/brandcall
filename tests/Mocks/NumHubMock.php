<?php

/**
 * BrandCall - Branded Caller ID SaaS Platform.
 *
 * @author     BrandCall Development Team
 * @copyright  2024-2026 BrandCall
 * @license    Proprietary
 */

declare(strict_types=1);

namespace Tests\Mocks;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

/**
 * NumHub API Mock Helper.
 *
 * Provides utilities for mocking NumHub BrandControl API responses
 * in tests. Loads fixtures from tests/Fixtures/NumHub/ directory.
 *
 * Usage:
 * ```php
 * // Basic mocking
 * NumHubMock::fake([
 *     '/api/v1/authorize/token' => NumHubMock::token(),
 *     '/api/v1/application' => NumHubMock::applicationCreated(),
 * ]);
 *
 * // Simulate errors
 * NumHubMock::fake([
 *     '/api/v1/application' => NumHubMock::error400(),
 * ]);
 *
 * // Sequence of responses
 * NumHubMock::fakeSequence('/api/v1/authorize/token')
 *     ->push(NumHubMock::error401(), 401)
 *     ->push(NumHubMock::token(), 200);
 *
 * // Rate limit simulation
 * NumHubMock::fakeRateLimit('/api/v1/application', retryAfter: 60);
 * ```
 */
class NumHubMock
{
    /**
     * NumHub API base URL for URL pattern matching.
     */
    public const BASE_URL = 'brandidentity-api.numhub.com';

    /**
     * Path to fixtures directory.
     */
    private static string $fixturesPath = __DIR__.'/../Fixtures/NumHub';

    /**
     * Set up HTTP fakes for NumHub API endpoints.
     *
     * @param  array<string, array<string, mixed>>  $responses  Map of endpoint patterns to response data
     */
    public static function fake(array $responses = []): void
    {
        $formattedResponses = [];

        foreach ($responses as $endpoint => $response) {
            // Convert endpoint to full URL pattern
            $pattern = self::buildUrlPattern($endpoint);

            // Response is either fixture data or callable
            if (is_callable($response)) {
                $formattedResponses[$pattern] = $response;
            } else {
                $status = $response['_status'] ?? 200;
                $headers = $response['_headers'] ?? [];
                unset($response['_status'], $response['_headers']);

                $formattedResponses[$pattern] = Http::response($response, $status, $headers);
            }
        }

        Http::fake($formattedResponses);
    }

    /**
     * Create a sequence of responses for an endpoint.
     *
     * @return \Illuminate\Http\Client\ResponseSequence
     */
    public static function fakeSequence(string $endpoint): \Illuminate\Http\Client\ResponseSequence
    {
        $pattern = self::buildUrlPattern($endpoint);

        return Http::fakeSequence($pattern);
    }

    /**
     * Simulate rate limiting for an endpoint.
     *
     * First call returns 429, subsequent calls succeed.
     *
     * @param  int  $retryAfter  Seconds to wait before retry (included in response)
     */
    public static function fakeRateLimit(string $endpoint, int $retryAfter = 60): void
    {
        $pattern = self::buildUrlPattern($endpoint);

        Http::fakeSequence($pattern)
            ->push(
                self::error429($retryAfter),
                429,
                ['Retry-After' => (string) $retryAfter]
            )
            ->push(self::applicationCreated(), 200);
    }

    /**
     * Simulate token expiry and refresh flow.
     *
     * First request to protected endpoint returns 401,
     * token refresh succeeds, retry of original request succeeds.
     */
    public static function fakeTokenRefreshFlow(): void
    {
        // Token endpoint always succeeds
        Http::fake([
            self::buildUrlPattern('/api/v1/authorize/token') => Http::response(self::token(), 200),
        ]);

        // First call to any other endpoint returns 401, then succeeds
        Http::fakeSequence(self::buildUrlPattern('/api/v1/*'))
            ->push(self::error401(), 401)
            ->push(self::applicationCreated(), 200);
    }

    // =========================================================================
    // FIXTURE LOADERS
    // =========================================================================

    /**
     * Load a fixture file and return parsed JSON.
     *
     * @param  string  $name  Fixture name without .json extension
     * @return array<string, mixed>
     */
    public static function fixture(string $name): array
    {
        $path = self::$fixturesPath.'/'.$name.'.json';

        if (! file_exists($path)) {
            throw new \RuntimeException("NumHub fixture not found: {$path}");
        }

        $content = file_get_contents($path);
        $data = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException("Invalid JSON in fixture: {$name}");
        }

        return $data;
    }

    /**
     * Get successful token response fixture.
     *
     * @return array<string, mixed>
     */
    public static function token(): array
    {
        return self::fixture('token-response');
    }

    /**
     * Get application created response fixture.
     *
     * @return array<string, mixed>
     */
    public static function applicationCreated(): array
    {
        return self::fixture('application-created');
    }

    /**
     * Get application approved response fixture.
     *
     * @return array<string, mixed>
     */
    public static function applicationApproved(): array
    {
        return self::fixture('application-approved');
    }

    /**
     * Get application rejected response fixture.
     *
     * @return array<string, mixed>
     */
    public static function applicationRejected(): array
    {
        return self::fixture('application-rejected');
    }

    /**
     * Get identity list response fixture.
     *
     * @return array<string, mixed>
     */
    public static function identityList(): array
    {
        return self::fixture('identity-list');
    }

    /**
     * Get settlement report response fixture.
     *
     * @return array<string, mixed>
     */
    public static function settlementReport(): array
    {
        return self::fixture('settlement-report');
    }

    // =========================================================================
    // ERROR RESPONSES
    // =========================================================================

    /**
     * Get 400 Bad Request error fixture.
     *
     * @return array<string, mixed>
     */
    public static function error400(): array
    {
        return self::fixture('error-400');
    }

    /**
     * Get 401 Unauthorized error fixture.
     *
     * @return array<string, mixed>
     */
    public static function error401(): array
    {
        return self::fixture('error-401');
    }

    /**
     * Get 429 Rate Limit error fixture.
     *
     * @param  int|null  $retryAfter  Override the retryAfter value
     * @return array<string, mixed>
     */
    public static function error429(?int $retryAfter = null): array
    {
        $data = self::fixture('error-429');

        if ($retryAfter !== null) {
            $data['error']['rateLimit']['retryAfter'] = $retryAfter;
        }

        return $data;
    }

    // =========================================================================
    // CUSTOM RESPONSE BUILDERS
    // =========================================================================

    /**
     * Create a custom application response with overrides.
     *
     * @param  array<string, mixed>  $overrides  Fields to override
     * @return array<string, mixed>
     */
    public static function application(array $overrides = []): array
    {
        $base = self::applicationCreated();

        return array_merge($base, $overrides);
    }

    /**
     * Create a custom identity response.
     *
     * @param  array<string, mixed>  $overrides  Fields to override
     * @return array<string, mixed>
     */
    public static function identity(array $overrides = []): array
    {
        $base = [
            'NumhubIdentityId' => 'idt_'.bin2hex(random_bytes(8)),
            'NumhubEntityId' => 'ent_abc123def456',
            'displayName' => 'Test Company',
            'displayNumber' => '+15551234567',
            'callReason' => 'Customer Service',
            'status' => 'active',
            'attestationLevel' => 'A',
            'phoneNumbers' => [
                'total' => 10,
                'active' => 10,
                'pending' => 0,
            ],
            'createdAt' => now()->toIso8601String(),
            'updatedAt' => now()->toIso8601String(),
        ];

        return array_merge($base, $overrides);
    }

    /**
     * Create a validation error response with custom errors.
     *
     * @param  array<array{field: string, code: string, message: string}>  $errors  Validation errors
     * @return array<string, mixed>
     */
    public static function validationError(array $errors): array
    {
        return [
            'success' => false,
            'error' => [
                'code' => 'VALIDATION_ERROR',
                'message' => 'The request contains invalid data.',
                'details' => $errors,
            ],
            'requestId' => 'req_'.bin2hex(random_bytes(8)),
            'timestamp' => now()->toIso8601String(),
            '_status' => 400,
        ];
    }

    /**
     * Create a server error response.
     *
     * @return array<string, mixed>
     */
    public static function serverError(): array
    {
        return [
            'success' => false,
            'error' => [
                'code' => 'INTERNAL_SERVER_ERROR',
                'message' => 'An unexpected error occurred. Please try again later.',
            ],
            'requestId' => 'req_'.bin2hex(random_bytes(8)),
            'timestamp' => now()->toIso8601String(),
            '_status' => 500,
        ];
    }

    // =========================================================================
    // ASSERTIONS
    // =========================================================================

    /**
     * Assert that a request was sent to NumHub with specific criteria.
     *
     * @param  string  $method  HTTP method
     * @param  string  $endpoint  Endpoint path (without base URL)
     * @param  callable|null  $callback  Additional assertions on the request
     */
    public static function assertSent(string $method, string $endpoint, ?callable $callback = null): void
    {
        Http::assertSent(function ($request) use ($method, $endpoint, $callback) {
            if (strtoupper($request->method()) !== strtoupper($method)) {
                return false;
            }

            if (! str_contains($request->url(), self::BASE_URL.$endpoint)) {
                return false;
            }

            // Verify required headers
            if (! $request->hasHeader('client-id')) {
                return false;
            }

            if ($request->header('X-Auth-Scheme')[0] !== 'ATLAASROPG') {
                return false;
            }

            if ($callback) {
                return $callback($request);
            }

            return true;
        });
    }

    /**
     * Assert that no requests were sent to NumHub.
     */
    public static function assertNothingSent(): void
    {
        Http::assertNothingSent();
    }

    /**
     * Assert that a specific number of requests were sent.
     */
    public static function assertSentCount(int $count): void
    {
        Http::assertSentCount($count);
    }

    // =========================================================================
    // HELPERS
    // =========================================================================

    /**
     * Build a full URL pattern for HTTP fake matching.
     */
    private static function buildUrlPattern(string $endpoint): string
    {
        // If already contains the base URL, return as-is
        if (str_contains($endpoint, self::BASE_URL)) {
            return $endpoint;
        }

        // Ensure endpoint starts with /
        $endpoint = '/'.ltrim($endpoint, '/');

        return 'https://'.self::BASE_URL.$endpoint;
    }

    /**
     * Get a random NumHub entity ID.
     */
    public static function entityId(): string
    {
        return 'ent_'.bin2hex(random_bytes(8));
    }

    /**
     * Get a random NumHub identity ID.
     */
    public static function identityId(): string
    {
        return 'idt_'.bin2hex(random_bytes(8));
    }
}
