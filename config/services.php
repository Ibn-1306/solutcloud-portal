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
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'cors' => [
        'allowed_origins' => env('CORS_ALLOWED_ORIGINS', 'https://www.solutcloud.com,https://solutcloud.com'),
    ],

    'solutcloud' => [
        'contact_recipient' => env('SOLUTCLOUD_CONTACT_RECIPIENT', 'sales@i-solutions.ci'),
        'portal_url' => env('SOLUTCLOUD_PORTAL_URL', 'https://login.solutcloud.com'),
    ],

    'moneroo' => [
        'secret' => env('MONEROO_SECRET_KEY'),
        'webhook_secret' => env('MONEROO_WEBHOOK_SECRET'),
        'base_url' => env('MONEROO_BASE_URL', 'https://api.moneroo.io'),
        'currency' => strtoupper(env('MONEROO_CURRENCY', 'XOF')),
        'timeout' => (int) env('MONEROO_TIMEOUT', 10),
        'checkout_ttl_minutes' => (int) env('MONEROO_CHECKOUT_TTL_MINUTES', 1440),
        'sandbox_monthly_amounts' => [
            'start' => (int) env('MONEROO_SANDBOX_START_MONTHLY', 10),
            'business' => (int) env('MONEROO_SANDBOX_BUSINESS_MONTHLY', 20),
            'premium' => (int) env('MONEROO_SANDBOX_PREMIUM_MONTHLY', 30),
        ],
    ],

];
