<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use Sashalenz\UklonDelivery\Tests\TestCase;

uses(TestCase::class)->in('Unit');

/**
 * Fake the Uklon API, always stubbing a successful OAuth token exchange.
 * Endpoint stubs are merged after, so callers only declare what they test.
 *
 * @param  array<string, mixed>  $stubs
 */
function fakeUklon(array $stubs = []): void
{
    Http::fake(array_merge([
        '*/api/v1/auth' => Http::response([
            'access_token' => 'test-access-token',
            'expires_in' => 1199,
        ]),
    ], $stubs));
}
