<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Sashalenz\UklonDelivery\ApiModels\Fare\RequestData\EstimateFareRequest;
use Sashalenz\UklonDelivery\ApiModels\Fare\ResponseData\FareData;
use Sashalenz\UklonDelivery\UklonDelivery;

function fareResponseBody(): array
{
    return [
        'id' => '01936d6b-2526-72e4-9b57-654f6545c818',
        'currency' => 'UAH',
        'estimated_products' => [
            'car' => [
                'estimation' => [
                    'cost' => [
                        'minimum' => 120,
                        'recommended' => 120,
                        'maximum' => 400,
                        'surge_multiplier' => 1,
                        'main_route' => 120,
                    ],
                    'route' => [
                        'distance' => ['cityMeters' => 3000, 'suburbanMeters' => 1000],
                        'overview_polyline' => 'eojyHoe~}D',
                        'drive_time_seconds' => 600,
                    ],
                ],
            ],
        ],
        'expires_at' => 1700000600,
    ];
}

it('estimates a fare and hydrates the nested product graph', function (): void {
    fakeUklon(['*/fares/estimate' => Http::response(fareResponseBody())]);

    $fare = UklonDelivery::fare()->estimate(EstimateFareRequest::from([
        'city' => 1,
        'pickup_point' => ['latitude' => 50.4501, 'longitude' => 30.5234, 'address' => 'вул. Хрещатик, 1'],
        'dropoff_points' => [
            ['latitude' => 50.4547, 'longitude' => 30.5238, 'address' => 'вул. Сумська, 2'],
        ],
        'products' => ['car' => []],
    ]));

    expect($fare)->toBeInstanceOf(FareData::class)
        ->and($fare->id)->toBe('01936d6b-2526-72e4-9b57-654f6545c818')
        ->and($fare->currency)->toBe('UAH')
        ->and($fare->expires_at)->toBe(1700000600)
        ->and($fare->estimated_products->car->isAvailable())->toBeTrue()
        ->and($fare->estimated_products->courier)->toBeNull()
        ->and($fare->estimated_products->car->estimation->cost->recommended)->toBe(120.0)
        ->and($fare->estimated_products->car->estimation->cost->maximum)->toBe(400.0)
        ->and($fare->estimated_products->car->estimation->route->drive_time_seconds)->toBe(600)
        ->and($fare->estimated_products->car->estimation->route->distance->getTotalMeters())->toBe(4000);
});

it('sends the estimate as an authenticated POST with the city in the body', function (): void {
    fakeUklon(['*/fares/estimate' => Http::response(fareResponseBody())]);

    UklonDelivery::fare()->estimate(EstimateFareRequest::from([
        'city' => 1,
        'pickup_point' => ['latitude' => 50.45, 'longitude' => 30.52],
        'dropoff_points' => [['latitude' => 50.46, 'longitude' => 30.53]],
    ]));

    Http::assertSent(fn (Request $req) => $req->method() === 'POST'
        && str_contains($req->url(), '/api/v1/fares/estimate')
        && $req['city'] === 1
        && $req->hasHeader('Authorization', 'Bearer test-access-token'));
});
