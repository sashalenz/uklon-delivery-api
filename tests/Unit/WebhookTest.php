<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Sashalenz\UklonDelivery\ApiModels\Webhook\RequestData\SetWebhookRequest;
use Sashalenz\UklonDelivery\ApiModels\Webhook\ResponseData\WebhookData;
use Sashalenz\UklonDelivery\UklonDelivery;

it('registers an order webhook with a POST', function (): void {
    fakeUklon(['*/api/v1/webhooks/order' => Http::response([], 200)]);

    UklonDelivery::webhook()->setForOrder(new SetWebhookRequest(
        url: 'https://example.test/uklon/order',
        key: 'shared-secret',
    ));

    Http::assertSent(fn (Request $req) => $req->method() === 'POST'
        && str_ends_with($req->url(), '/api/v1/webhooks/order')
        && $req['url'] === 'https://example.test/uklon/order'
        && $req['key'] === 'shared-secret'
        && $req->hasHeader('Authorization', 'Bearer test-access-token'));
});

it('reads the current driver webhook', function (): void {
    fakeUklon(['*/api/v1/webhooks/driver' => Http::response([
        'url' => 'https://example.test/uklon/driver',
        'key' => 'driver-secret',
    ])]);

    $webhook = UklonDelivery::webhook()->getForDriver();

    expect($webhook)->toBeInstanceOf(WebhookData::class)
        ->and($webhook->url)->toBe('https://example.test/uklon/driver')
        ->and($webhook->key)->toBe('driver-secret');
});

it('deletes the order webhook with a DELETE', function (): void {
    fakeUklon(['*/api/v1/webhooks/order' => Http::response(null, 204)]);

    UklonDelivery::webhook()->deleteForOrder();

    Http::assertSent(fn (Request $req) => $req->method() === 'DELETE'
        && str_ends_with($req->url(), '/api/v1/webhooks/order'));
});
