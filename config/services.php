<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | NumHub BrandControl
    |--------------------------------------------------------------------------
    |
    | NumHub provides STIR/SHAKEN compliant branded calling services.
    | Sign up at https://numhub.com to get API credentials.
    |
    */

    'numhub' => [
        'api_url' => env('NUMHUB_API_URL', 'https://api.numhub.com'),
        'api_key' => env('NUMHUB_API_KEY'),
        'api_secret' => env('NUMHUB_API_SECRET'),
        'webhook_secret' => env('NUMHUB_WEBHOOK_SECRET'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Call Analytics Providers
    |--------------------------------------------------------------------------
    |
    | Optional integrations with call analytics providers for spam detection
    | and number reputation monitoring.
    |
    */

    'analytics' => [
        'first_orion_enabled' => env('FIRST_ORION_ENABLED', false),
        'first_orion_api_key' => env('FIRST_ORION_API_KEY'),
        'first_orion_api_url' => env('FIRST_ORION_API_URL', 'https://api.firstorion.com'),

        'hiya_enabled' => env('HIYA_ENABLED', false),
        'hiya_api_key' => env('HIYA_API_KEY'),
        'hiya_api_url' => env('HIYA_API_URL', 'https://api.hiya.com'),

        'tns_enabled' => env('TNS_ENABLED', false),
        'tns_api_key' => env('TNS_API_KEY'),
        'tns_api_url' => env('TNS_API_URL', 'https://api.tnsi.com'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Stripe
    |--------------------------------------------------------------------------
    |
    | Usage-based metered billing via Stripe.
    |
    */

    'stripe' => [
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
        'metered_price_id' => env('STRIPE_METERED_PRICE_ID'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
    ],

];
