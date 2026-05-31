<?php

declare(strict_types=1);

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Sashalenz\UklonDelivery\ApiModels\Order\RequestData\CancelOrderRequest;
use Sashalenz\UklonDelivery\ApiModels\Order\RequestData\CreateOrderRequest;
use Sashalenz\UklonDelivery\ApiModels\Order\ResponseData\CourierLocationData;
use Sashalenz\UklonDelivery\ApiModels\Order\ResponseData\CreatedOrderData;
use Sashalenz\UklonDelivery\ApiModels\Order\ResponseData\OrderData;
use Sashalenz\UklonDelivery\ApiModels\Order\ResponseData\OrderListData;
use Sashalenz\UklonDelivery\Enums\CancelReason;
use Sashalenz\UklonDelivery\Enums\DropoffStatus;
use Sashalenz\UklonDelivery\Enums\OrderStatus;
use Sashalenz\UklonDelivery\Exceptions\UklonDeliveryApiUnavailableException;
use Sashalenz\UklonDelivery\Exceptions\UklonDeliveryException;
use Sashalenz\UklonDelivery\UklonDelivery;

function orderResponseBody(array $overrides = []): array
{
    return array_merge([
        'id' => '01936d6b-2533-7244-9f2b-4c63b64163a4',
        'status' => 'processing',
        'product' => 'car',
        'times' => ['creation' => 1700000000],
        'sender' => ['name' => 'Viktor', 'phone' => '+380666666666'],
        'receivers' => [['name' => 'Bob', 'phone' => '+380666666665']],
        'creator' => [
            'id' => '01936d6b-2533-7244-9f2b-4c63b64163bb',
            'name' => 'Nick',
            'phone' => '+380666666667',
        ],
        'route' => [
            'city' => 1,
            'distance' => ['cityMeters' => 1000, 'suburbanMeters' => 500],
            'points' => [
                'pickup' => ['latitude' => 50.4501, 'longitude' => 30.5234, 'address' => 'Address'],
                'dropoffs' => [[
                    'id' => 7,
                    'latitude' => 51.4501,
                    'longitude' => 31.5234,
                    'address' => 'Address 2',
                    'status' => 'delivered',
                ]],
            ],
        ],
        'cost' => ['currency' => 'UAH', 'total' => 100, 'minimum' => 100, 'maximum' => 100, 'route' => 100],
        'suspended' => false,
        'deferred' => false,
    ], $overrides);
}

function createOrderRequest(): CreateOrderRequest
{
    return CreateOrderRequest::from([
        'fare_id' => '01936d6b-2526-72e4-9b57-654f6545c818',
        'product' => 'car',
        'sender' => ['name' => 'Viktor', 'phone' => '+380666666666'],
        'receivers' => [['name' => 'Bob', 'phone' => '+380666666665']],
        'agreed_cost' => 120.0,
    ]);
}

it('creates an order and returns its id', function (): void {
    fakeUklon(['*/api/v1/orders' => Http::response(['id' => 'new-order-id-123'])]);

    $order = UklonDelivery::order()->create(createOrderRequest());

    expect($order)->toBeInstanceOf(CreatedOrderData::class)
        ->and($order->id)->toBe('new-order-id-123');

    Http::assertSent(fn (Request $req) => $req->method() === 'POST'
        && str_ends_with($req->url(), '/api/v1/orders')
        && $req['fare_id'] === '01936d6b-2526-72e4-9b57-654f6545c818'
        && $req['agreed_cost'] === 120.0
        && $req->hasHeader('Authorization', 'Bearer test-access-token'));
});

it('gets a full order and hydrates the route, receivers and cost', function (): void {
    fakeUklon(['*/api/v1/orders/*' => Http::response(orderResponseBody())]);

    $order = UklonDelivery::order()->get('01936d6b-2533-7244-9f2b-4c63b64163a4');

    expect($order)->toBeInstanceOf(OrderData::class)
        ->and($order->status)->toBe(OrderStatus::Processing)
        ->and($order->status->isCourierAssigned())->toBeFalse()
        ->and($order->receivers)->toHaveCount(1)
        ->and($order->receivers[0]->name)->toBe('Bob')
        ->and($order->route->distance->getTotalMeters())->toBe(1500)
        ->and($order->route->points->dropoffs[0]->status)->toBe(DropoffStatus::Delivered)
        ->and($order->cost->total)->toBe(100.0)
        ->and($order->cost->currency)->toBe('UAH');
});

it('lists active orders with a cursor', function (): void {
    fakeUklon(['*/api/v1/orders/active' => Http::response([
        'items' => [orderResponseBody(), orderResponseBody(['id' => 'second', 'status' => 'accepted'])],
        'next_cursor' => 'cursor-abc',
    ])]);

    $list = UklonDelivery::order()->getActive();

    expect($list)->toBeInstanceOf(OrderListData::class)
        ->and($list->items)->toHaveCount(2)
        ->and($list->next_cursor)->toBe('cursor-abc')
        ->and($list->items[1]->status)->toBe(OrderStatus::Accepted);
});

it('lists archived orders and sends the limit/cursor as query params', function (): void {
    fakeUklon(['*/api/v1/orders/archived*' => Http::response([
        'items' => [orderResponseBody(['status' => 'completed'])],
        'next_cursor' => null,
    ])]);

    $list = UklonDelivery::order()->getArchived(50, 'prev-cursor');

    expect($list->items)->toHaveCount(1)
        ->and($list->items[0]->status)->toBe(OrderStatus::Completed)
        ->and($list->next_cursor)->toBeNull();

    Http::assertSent(fn (Request $req) => $req->method() === 'GET'
        && str_contains($req->url(), '/api/v1/orders/archived')
        && str_contains($req->url(), 'limit=50')
        && str_contains($req->url(), 'cursor=prev-cursor'));
});

it('gets the courier location', function (): void {
    fakeUklon(['*/driver/location' => Http::response([
        'latitude' => 50.45,
        'longitude' => 30.52,
        'bearing' => 90,
        'next_point_eta' => 120,
    ])]);

    $location = UklonDelivery::order()->getCourierLocation('order-id');

    expect($location)->toBeInstanceOf(CourierLocationData::class)
        ->and($location->latitude)->toBe(50.45)
        ->and($location->bearing)->toBe(90)
        ->and($location->next_point_eta)->toBe(120);
});

it('cancels an order with a PUT and the reason in the body', function (): void {
    fakeUklon(['*/cancel' => Http::response(null, 204)]);

    UklonDelivery::order()->cancel('order-id', new CancelOrderRequest(CancelReason::PlansChanged));

    Http::assertSent(fn (Request $req) => $req->method() === 'PUT'
        && str_ends_with($req->url(), '/api/v1/orders/order-id/cancel')
        && $req['reason'] === 'plans_changed');
});

it('throws UklonDeliveryException on a 4xx error', function (): void {
    fakeUklon(['*/api/v1/orders' => Http::response([
        'subcode' => 'invalid_fare',
        'message' => 'Fare expired',
    ], 422)]);

    UklonDelivery::order()->create(createOrderRequest());
})->throws(UklonDeliveryException::class, 'Fare expired');

it('throws UklonDeliveryApiUnavailableException on a connection error', function (): void {
    fakeUklon(['*/api/v1/orders/*' => fn () => throw new ConnectionException('Connection refused')]);

    UklonDelivery::order()->get('order-id');
})->throws(UklonDeliveryApiUnavailableException::class);

it('throws UklonDeliveryApiUnavailableException on a 5xx error', function (): void {
    fakeUklon(['*/api/v1/orders/*' => Http::response('boom', 503)]);

    UklonDelivery::order()->get('order-id');
})->throws(UklonDeliveryApiUnavailableException::class);
