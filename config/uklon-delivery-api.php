<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Environment
    |--------------------------------------------------------------------------
    |
    | When `staging` is true, requests target the Uklon staging gateway
    | (`staging_url`). Otherwise the production `url` is used. Uklon issues
    | separate credentials for each environment.
    |
    | Docs: https://deliverygateway.uklon.com.ua/docs
    */
    'staging' => (bool) env('UKLON_DELIVERY_STAGING', false),

    'url' => env('UKLON_DELIVERY_API_URL', 'https://deliverygateway.uklon.com.ua/api/v1'),

    'staging_url' => env('UKLON_DELIVERY_API_STAGING_URL', 'https://deliverygateway.staging.uklon.com.ua/api/v1'),

    /*
    |--------------------------------------------------------------------------
    | OAuth credentials
    |--------------------------------------------------------------------------
    |
    | Issued per company by Uklon. The SDK exchanges them at POST /auth for a
    | short-lived bearer token, which is cached for its `expires_in` lifetime.
    | Keep them out of source control — set via .env.
    */
    'app_uid' => env('UKLON_DELIVERY_APP_UID'),

    'client_id' => env('UKLON_DELIVERY_CLIENT_ID'),

    'client_secret' => env('UKLON_DELIVERY_CLIENT_SECRET'),

    /*
    |--------------------------------------------------------------------------
    | Webhook signing secret
    |--------------------------------------------------------------------------
    |
    | The `key` you registered with your webhook subscription. Uklon signs every
    | webhook delivery with it (HMAC-SHA256, `X-Signature` header). Used by the
    | `uklon-webhook` middleware to verify inbound requests.
    */
    'webhook_secret' => env('UKLON_DELIVERY_WEBHOOK_SECRET'),

    /*
    |--------------------------------------------------------------------------
    | HTTP client settings
    |--------------------------------------------------------------------------
    */
    'timeout' => (int) env('UKLON_DELIVERY_TIMEOUT', 10),

    'retry_times' => 3,

    'retry_sleep' => 100,
];
