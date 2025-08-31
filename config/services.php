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

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'stripe' => [
        'secret' => env('STRIPE_SECRET'),
        'key' => env('STRIPE_KEY'),
        'webhook' => [
            'secret' => env('STRIPE_WEBHOOK_SECRET'),
            'tolerance' => env('STRIPE_WEBHOOK_TOLERANCE', 300),
        ],
    ],

    'paypal' => [
        'client_id' => env('PAYPAL_CLIENT_ID'),
        'client_secret' => env('PAYPAL_CLIENT_SECRET'),
        'mode' => env('PAYPAL_MODE', 'sandbox'), // sandbox or live
    ],

    'ai' => [
        'flask_url' => env('AI_FLASK_URL', 'http://127.0.0.1:5006'),
        'timeout' => env('AI_TIMEOUT', 10),
        'cache_ttl' => env('AI_CACHE_TTL', 3600), // 1 hour in seconds
        'enabled' => env('AI_ENABLED', true),
        'fallback_enabled' => env('AI_FALLBACK_ENABLED', true),
        'booking_enabled' => env('AI_BOOKING_ENABLED', true),
        'proxy_enabled' => env('AI_PROXY_ENABLED', true),
        'wallet_integration' => env('AI_WALLET_INTEGRATION', true),
    ],

];
