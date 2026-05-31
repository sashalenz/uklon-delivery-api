<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Sashalenz\UklonDelivery\UklonDelivery;

it('authenticates once and reuses the cached token across calls', function (): void {
    fakeUklon([
        '*/api/v1/cities' => Http::response(['cities' => []]),
    ]);

    UklonDelivery::city()->all();
    UklonDelivery::city()->all();
    UklonDelivery::city()->all();

    expect(Http::recorded(fn (Request $req) => str_ends_with($req->url(), '/api/v1/auth')))
        ->toHaveCount(1);
});

it('exchanges the configured credentials at /auth', function (): void {
    fakeUklon(['*/api/v1/cities' => Http::response(['cities' => []])]);

    UklonDelivery::city()->all();

    Http::assertSent(fn (Request $req) => str_ends_with($req->url(), '/api/v1/auth')
        && $req->method() === 'POST'
        && $req['app_uid'] === 'test-app-uid'
        && $req['client_id'] === 'test-client-id'
        && $req['client_secret'] === 'test-client-secret');
});

it('targets the staging gateway when staging is enabled', function (): void {
    fakeUklon(['*/api/v1/cities' => Http::response(['cities' => []])]);

    UklonDelivery::city()->all();

    Http::assertSent(fn (Request $req) => str_starts_with(
        $req->url(),
        'https://deliverygateway.staging.uklon.com.ua/api/v1',
    ));
});
