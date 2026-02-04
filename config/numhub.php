<?php

/**
 * NumHub BrandControl API Configuration.
 *
 * Configuration for the NumHub BrandControl API client used for
 * BCID application management, entity registration, and compliance.
 *
 * @see https://brandidentity-api.numhub.com/docs/index.html
 */

return [
    /*
    |--------------------------------------------------------------------------
    | NumHub API URL
    |--------------------------------------------------------------------------
    |
    | The base URL for the NumHub BrandControl API.
    |
    */
    'api_url' => env('NUMHUB_API_URL', 'https://brandidentity-api.numhub.com'),

    /*
    |--------------------------------------------------------------------------
    | Authentication Credentials
    |--------------------------------------------------------------------------
    |
    | Email and password for NumHub API authentication.
    | Contact NumHub support to obtain credentials: BCIDsupport@numhub.com
    |
    */
    'email' => env('NUMHUB_EMAIL'),
    'password' => env('NUMHUB_PASSWORD'),

    /*
    |--------------------------------------------------------------------------
    | Client ID
    |--------------------------------------------------------------------------
    |
    | Optional override for the client ID. If not set, the first client
    | from the authentication response will be used.
    |
    */
    'client_id' => env('NUMHUB_CLIENT_ID'),

    /*
    |--------------------------------------------------------------------------
    | Authentication Scheme
    |--------------------------------------------------------------------------
    |
    | The X-Auth-Scheme header value required by NumHub.
    | Default is ATLAASROPG for standard API authentication.
    |
    */
    'auth_scheme' => env('NUMHUB_AUTH_SCHEME', 'ATLAASROPG'),

    /*
    |--------------------------------------------------------------------------
    | Token Cache TTL
    |--------------------------------------------------------------------------
    |
    | How long to cache the access token in seconds.
    | NumHub tokens expire after 24 hours (86400 seconds).
    | Default is 23.5 hours to ensure refresh before expiry.
    |
    */
    'token_cache_ttl' => (int) env('NUMHUB_TOKEN_CACHE_TTL', 84600),

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    |
    | NumHub API rate limits: 100 requests per minute.
    | These settings control local rate limiting to prevent 429 errors.
    |
    */
    'rate_limit' => [
        'max_requests' => (int) env('NUMHUB_RATE_LIMIT_MAX', 100),
        'window_seconds' => (int) env('NUMHUB_RATE_LIMIT_WINDOW', 60),
    ],

    /*
    |--------------------------------------------------------------------------
    | HTTP Client Settings
    |--------------------------------------------------------------------------
    |
    | Timeout and retry configuration for API requests.
    |
    */
    'timeout' => (int) env('NUMHUB_TIMEOUT', 30),

    'retry' => [
        'times' => (int) env('NUMHUB_RETRY_TIMES', 3),
        'sleep' => (int) env('NUMHUB_RETRY_SLEEP', 1000), // milliseconds
        'when' => [429, 500, 502, 503, 504], // HTTP status codes to retry
    ],

    /*
    |--------------------------------------------------------------------------
    | Mock Mode
    |--------------------------------------------------------------------------
    |
    | Enable mock mode for development/testing without real API calls.
    | When enabled, the client will return mock responses.
    |
    */
    'mock' => env('NUMHUB_MOCK', false),

    /*
    |--------------------------------------------------------------------------
    | Logging
    |--------------------------------------------------------------------------
    |
    | Enable detailed logging of API requests and responses.
    | Use with caution in production as it may log sensitive data.
    |
    */
    'logging' => [
        'enabled' => env('NUMHUB_LOGGING_ENABLED', false),
        'channel' => env('NUMHUB_LOGGING_CHANNEL', 'stack'),
    ],
];
