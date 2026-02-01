<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Voice Driver
    |--------------------------------------------------------------------------
    |
    | This option defines the default voice driver that will be used when
    | making branded calls. You may set this to any of the drivers defined
    | below. Telnyx is recommended for quick start due to easy signup and
    | low pricing.
    |
    | Supported: "telnyx", "numhub", "twilio", "null"
    |
    */

    'default' => env('VOICE_DRIVER', 'telnyx'),

    /*
    |--------------------------------------------------------------------------
    | Voice Drivers
    |--------------------------------------------------------------------------
    |
    | Here you may configure the voice drivers for your application. Each
    | driver implements the VoiceProvider contract and can be swapped out
    | without changing application code.
    |
    */

    'drivers' => [

        /*
        |----------------------------------------------------------------------
        | Telnyx Driver
        |----------------------------------------------------------------------
        |
        | Developer-friendly, cheap ($0.002/min), easy signup.
        | Supports STIR/SHAKEN and CNAM caller ID.
        | Sign up: https://telnyx.com
        |
        */

        'telnyx' => [
            'api_key' => env('TELNYX_API_KEY'),
            'connection_id' => env('TELNYX_CONNECTION_ID'),
            'public_key' => env('TELNYX_PUBLIC_KEY'),
            'mock' => env('TELNYX_MOCK', true),
        ],

        /*
        |----------------------------------------------------------------------
        | NumHub Driver
        |----------------------------------------------------------------------
        |
        | Enterprise-grade BCID platform with full rich call data support
        | (logo, call reason). Requires sales contact for API credentials.
        | Sign up: https://numhub.com
        |
        */

        'numhub' => [
            'base_url' => env('NUMHUB_API_URL', 'https://api.numhub.com/v1'),
            'api_key' => env('NUMHUB_API_KEY'),
            'api_secret' => env('NUMHUB_API_SECRET'),
            'webhook_secret' => env('NUMHUB_WEBHOOK_SECRET'),
            'mock' => env('NUMHUB_MOCK', true),
        ],

        /*
        |----------------------------------------------------------------------
        | Twilio Driver
        |----------------------------------------------------------------------
        |
        | Most popular CPaaS. Has STIR/SHAKEN but limited branded calling.
        | Good if you already use Twilio for other services.
        | Sign up: https://twilio.com
        |
        */

        'twilio' => [
            'account_sid' => env('TWILIO_ACCOUNT_SID'),
            'auth_token' => env('TWILIO_AUTH_TOKEN'),
            'mock' => env('TWILIO_MOCK', true),
        ],

        /*
        |----------------------------------------------------------------------
        | Null Driver
        |----------------------------------------------------------------------
        |
        | No-op driver for testing. Always succeeds, does nothing.
        |
        */

        'null' => [],

    ],

    /*
    |--------------------------------------------------------------------------
    | Feature Comparison
    |--------------------------------------------------------------------------
    |
    | Quick reference for what each driver supports:
    |
    | Driver   | STIR/SHAKEN | CNAM | Logo Display | Number Purchase | Pricing
    | ---------|-------------|------|--------------|-----------------|--------
    | telnyx   | ✓           | ✓    | ✗            | ✓               | $0.002/min
    | numhub   | ✓           | ✓    | ✓            | ✗               | Enterprise
    | twilio   | ✓           | ✓    | ✗            | ✓               | $0.014/min
    | null     | ✗           | ✗    | ✗            | ✗               | Free
    |
    */

];
