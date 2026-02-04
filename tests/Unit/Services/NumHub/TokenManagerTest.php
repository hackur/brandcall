<?php

/**
 * BrandCall - Branded Caller ID SaaS Platform.
 *
 * @author     BrandCall Development Team
 * @copyright  2024-2026 BrandCall
 * @license    Proprietary
 */

declare(strict_types=1);

use App\Services\NumHub\TokenManager;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\Mocks\NumHubMock;

/**
 * Unit tests for TokenManager - handles NumHub API authentication.
 *
 * Tests cover:
 * - Token caching and retrieval
 * - Token refresh logic
 * - Authentication flow
 * - Error handling for auth failures
 *
 * @see \App\Services\NumHub\TokenManager
 */
beforeEach(function () {
    Http::fake();
    Cache::flush();

    // Set up config
    config([
        'numhub.api_url' => 'https://brandidentity-api.numhub.com',
        'numhub.email' => 'test@brandcall.io',
        'numhub.password' => 'test_password',
        'numhub.client_id' => 'test_client_id',
    ]);
});

// =============================================================================
// TOKEN RETRIEVAL
// =============================================================================

describe('getToken', function () {
    it('returns cached token if valid', function () {
        // Pre-cache a valid token
        $cachedToken = 'cached_access_token_12345';
        Cache::put('numhub_access_token', $cachedToken, now()->addHours(12));

        $manager = app(TokenManager::class);
        $token = $manager->getToken();

        expect($token)->toBe($cachedToken);

        // Should not make any HTTP requests
        Http::assertNothingSent();
    })->skip('Implement TokenManager first');

    it('fetches new token if cache is empty', function () {
        NumHubMock::fake([
            '/api/v1/authorize/token' => NumHubMock::token(),
        ]);

        $manager = app(TokenManager::class);
        $token = $manager->getToken();

        expect($token)->not->toBeNull();
        expect($token)->toBeString();

        Http::assertSent(fn ($request) => str_contains($request->url(), '/authorize/token')
        );
    })->skip('Implement TokenManager first');

    it('refreshes token if within expiry buffer', function () {
        // Cache a token that expires in 4 minutes (within 5 min buffer)
        Cache::put('numhub_access_token', 'expiring_token', now()->addMinutes(4));

        NumHubMock::fake([
            '/api/v1/authorize/token' => NumHubMock::token(),
        ]);

        $manager = app(TokenManager::class);
        $token = $manager->getToken();

        // Should have fetched a new token
        expect($token)->not->toBe('expiring_token');

        Http::assertSent(fn ($request) => str_contains($request->url(), '/authorize/token')
        );
    })->skip('Implement TokenManager first');

    it('does not refresh token if outside expiry buffer', function () {
        // Cache a token that expires in 10 minutes (outside 5 min buffer)
        $validToken = 'valid_token_abc';
        Cache::put('numhub_access_token', $validToken, now()->addMinutes(10));

        $manager = app(TokenManager::class);
        $token = $manager->getToken();

        expect($token)->toBe($validToken);
        Http::assertNothingSent();
    })->skip('Implement TokenManager first');
});

// =============================================================================
// AUTHENTICATION
// =============================================================================

describe('authenticate', function () {
    it('sends correct credentials to token endpoint', function () {
        NumHubMock::fake([
            '/api/v1/authorize/token' => NumHubMock::token(),
        ]);

        $manager = app(TokenManager::class);
        $manager->authenticate();

        Http::assertSent(function ($request) {
            return str_contains($request->url(), '/api/v1/authorize/token')
                && $request['email'] === 'test@brandcall.io'
                && $request['password'] === 'test_password';
        });
    })->skip('Implement TokenManager first');

    it('includes required headers in auth request', function () {
        NumHubMock::fake([
            '/api/v1/authorize/token' => NumHubMock::token(),
        ]);

        $manager = app(TokenManager::class);
        $manager->authenticate();

        Http::assertSent(function ($request) {
            return $request->hasHeader('client-id')
                && $request->header('X-Auth-Scheme')[0] === 'ATLAASROPG';
        });
    })->skip('Implement TokenManager first');

    it('stores token with correct TTL', function () {
        $tokenResponse = NumHubMock::token();
        $expiresIn = $tokenResponse['expires_in']; // 86400 seconds (24 hours)

        NumHubMock::fake([
            '/api/v1/authorize/token' => $tokenResponse,
        ]);

        $manager = app(TokenManager::class);
        $manager->authenticate();

        // Token should be cached
        expect(Cache::has('numhub_access_token'))->toBeTrue();

        // TTL should be based on expires_in from response
        $ttl = Cache::getStore()->get('numhub_access_token_ttl');
        expect($ttl)->toBeLessThanOrEqual($expiresIn);
    })->skip('Implement TokenManager first');

    it('returns the access token', function () {
        $tokenResponse = NumHubMock::token();

        NumHubMock::fake([
            '/api/v1/authorize/token' => $tokenResponse,
        ]);

        $manager = app(TokenManager::class);
        $token = $manager->authenticate();

        expect($token)->toBe($tokenResponse['access_token']);
    })->skip('Implement TokenManager first');

    it('throws exception on invalid credentials', function () {
        Http::fake([
            '*' => Http::response([
                'error' => 'Invalid credentials',
                'message' => 'The provided email or password is incorrect.',
            ], 401),
        ]);

        $manager = app(TokenManager::class);
        $manager->authenticate();
    })->throws(\App\Exceptions\NumHub\NumHubAuthException::class)
        ->skip('Implement TokenManager and exceptions first');

    it('throws exception with meaningful message on auth failure', function () {
        Http::fake([
            '*' => Http::response([
                'error' => 'Invalid credentials',
            ], 401),
        ]);

        $manager = app(TokenManager::class);

        try {
            $manager->authenticate();
        } catch (\App\Exceptions\NumHub\NumHubAuthException $e) {
            expect($e->getMessage())->toContain('credentials');
        }
    })->skip('Implement TokenManager and exceptions first');
});

// =============================================================================
// TOKEN INVALIDATION
// =============================================================================

describe('invalidate', function () {
    it('clears cached token', function () {
        Cache::put('numhub_access_token', 'some_token', now()->addHours(12));

        $manager = app(TokenManager::class);
        $manager->invalidate();

        expect(Cache::has('numhub_access_token'))->toBeFalse();
    })->skip('Implement TokenManager first');

    it('clears related cache entries', function () {
        Cache::put('numhub_access_token', 'some_token', now()->addHours(12));
        Cache::put('numhub_user_info', ['id' => 'usr_123'], now()->addHours(12));

        $manager = app(TokenManager::class);
        $manager->invalidate();

        expect(Cache::has('numhub_access_token'))->toBeFalse();
        expect(Cache::has('numhub_user_info'))->toBeFalse();
    })->skip('Implement TokenManager first');
});

// =============================================================================
// USER INFO
// =============================================================================

describe('getUserInfo', function () {
    it('fetches user info from API', function () {
        NumHubMock::fake([
            '/api/v1/authorize/token' => NumHubMock::token(),
            '/api/v1/authorize/userInfo' => [
                'id' => 'usr_test_12345',
                'email' => 'api@brandcall.io',
                'name' => 'BrandCall API User',
                'role' => 'osp_admin',
            ],
        ]);

        $manager = app(TokenManager::class);
        $userInfo = $manager->getUserInfo();

        expect($userInfo)->toBeArray();
        expect($userInfo['id'])->toBe('usr_test_12345');
        expect($userInfo['email'])->toBe('api@brandcall.io');
    })->skip('Implement TokenManager first');

    it('caches user info', function () {
        NumHubMock::fake([
            '/api/v1/authorize/token' => NumHubMock::token(),
            '/api/v1/authorize/userInfo' => [
                'id' => 'usr_test_12345',
            ],
        ]);

        $manager = app(TokenManager::class);

        // First call
        $manager->getUserInfo();

        // Second call should use cache
        $manager->getUserInfo();

        // Only one request to userInfo endpoint
        Http::assertSentCount(2); // token + userInfo
    })->skip('Implement TokenManager first');

    it('returns cached user info', function () {
        $cachedInfo = [
            'id' => 'cached_user',
            'email' => 'cached@example.com',
        ];
        Cache::put('numhub_user_info', $cachedInfo, now()->addHours(12));
        Cache::put('numhub_access_token', 'valid_token', now()->addHours(12));

        $manager = app(TokenManager::class);
        $userInfo = $manager->getUserInfo();

        expect($userInfo)->toBe($cachedInfo);
        Http::assertNothingSent();
    })->skip('Implement TokenManager first');
});

// =============================================================================
// TOKEN VALIDATION
// =============================================================================

describe('isTokenValid', function () {
    it('returns true when token is cached and not expiring', function () {
        Cache::put('numhub_access_token', 'valid_token', now()->addHours(12));

        $manager = app(TokenManager::class);

        expect($manager->isTokenValid())->toBeTrue();
    })->skip('Implement TokenManager first');

    it('returns false when token is not cached', function () {
        $manager = app(TokenManager::class);

        expect($manager->isTokenValid())->toBeFalse();
    })->skip('Implement TokenManager first');

    it('returns false when token is expiring soon', function () {
        Cache::put('numhub_access_token', 'expiring_token', now()->addMinutes(4));

        $manager = app(TokenManager::class);

        expect($manager->isTokenValid())->toBeFalse();
    })->skip('Implement TokenManager first');
});

// =============================================================================
// CONCURRENCY
// =============================================================================

describe('concurrent token refresh', function () {
    it('prevents multiple simultaneous token refreshes', function () {
        $refreshCount = 0;

        Http::fake(function ($request) use (&$refreshCount) {
            if (str_contains($request->url(), '/authorize/token')) {
                $refreshCount++;
                // Simulate slow response
                usleep(100000); // 100ms

                return Http::response(NumHubMock::token(), 200);
            }

            return Http::response([], 200);
        });

        $manager = app(TokenManager::class);

        // Simulate concurrent requests
        $promises = [];
        for ($i = 0; $i < 5; $i++) {
            $promises[] = fn () => $manager->getToken();
        }

        // Execute all and wait
        foreach ($promises as $promise) {
            $promise();
        }

        // Should only refresh once due to locking
        expect($refreshCount)->toBe(1);
    })->skip('Implement TokenManager with locking first');
});

// =============================================================================
// ERROR RECOVERY
// =============================================================================

describe('error recovery', function () {
    it('retries authentication on temporary failure', function () {
        $attempts = 0;

        Http::fake(function () use (&$attempts) {
            $attempts++;
            if ($attempts < 2) {
                return Http::response(null, 503);
            }

            return Http::response(NumHubMock::token(), 200);
        });

        $manager = app(TokenManager::class);
        $token = $manager->authenticate();

        expect($attempts)->toBe(2);
        expect($token)->not->toBeNull();
    })->skip('Implement TokenManager with retry first');

    it('clears invalid cached token on auth failure', function () {
        Cache::put('numhub_access_token', 'invalid_token', now()->addHours(12));

        Http::fake([
            '*' => Http::response(NumHubMock::error401(), 401),
        ]);

        $manager = app(TokenManager::class);

        try {
            $manager->authenticate();
        } catch (\Exception $e) {
            // Expected
        }

        expect(Cache::has('numhub_access_token'))->toBeFalse();
    })->skip('Implement TokenManager first');
});
