<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Sashalenz\UklonDelivery\Credentials;
use Sashalenz\UklonDelivery\UklonDelivery;

it('authenticates with explicit per-instance credentials instead of config', function () {
    fakeUklon([
        '*/api/v1/cities' => Http::response(['cities' => []]),
    ]);

    $credentials = new Credentials(
        appUid: 'sender-app-uid',
        clientId: 'sender-client-id',
        clientSecret: 'sender-client-secret',
    );

    UklonDelivery::city($credentials)->all();

    Http::assertSent(function (Request $request): bool {
        if (! str_ends_with($request->url(), '/auth')) {
            return false;
        }

        return $request['app_uid'] === 'sender-app-uid'
            && $request['client_id'] === 'sender-client-id'
            && $request['client_secret'] === 'sender-client-secret';
    });
});

it('falls back to config credentials when none are passed', function () {
    fakeUklon([
        '*/api/v1/cities' => Http::response(['cities' => []]),
    ]);

    UklonDelivery::city()->all();

    Http::assertSent(function (Request $request): bool {
        if (! str_ends_with($request->url(), '/auth')) {
            return false;
        }

        return $request['client_id'] === 'test-client-id';
    });
});

it('routes a non-staging account to the production gateway', function () {
    // The test environment defaults to staging; an explicit non-staging
    // account must override it and hit the production base URL.
    fakeUklon([
        '*/api/v1/cities' => Http::response(['cities' => []]),
    ]);

    $credentials = new Credentials(
        appUid: 'a',
        clientId: 'b',
        clientSecret: 'c',
        staging: false,
    );

    UklonDelivery::city($credentials)->all();

    Http::assertSent(
        fn (Request $request): bool => str_starts_with($request->url(), 'https://deliverygateway.uklon.com.ua/api/v1'),
    );
});

it('caches tokens separately per client id', function () {
    fakeUklon([
        '*/api/v1/cities' => Http::response(['cities' => []]),
    ]);

    $a = new Credentials(appUid: 'a', clientId: 'client-a', clientSecret: 's');
    $b = new Credentials(appUid: 'b', clientId: 'client-b', clientSecret: 's');

    UklonDelivery::city($a)->all();
    UklonDelivery::city($a)->all(); // reuses cached token — no second /auth
    UklonDelivery::city($b)->all(); // distinct client_id — fresh /auth

    Http::assertSentCount(5); // 2 auth (a, b) + 3 cities
});
