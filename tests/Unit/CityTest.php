<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use Sashalenz\UklonDelivery\ApiModels\City\ResponseData\CitiesData;
use Sashalenz\UklonDelivery\UklonDelivery;

it('lists available delivery cities', function (): void {
    fakeUklon(['*/api/v1/cities' => Http::response([
        'cities' => [
            ['id' => 1, 'name' => 'Київ'],
            ['id' => 2, 'name' => 'Харків'],
        ],
    ])]);

    $cities = UklonDelivery::city()->all();

    expect($cities)->toBeInstanceOf(CitiesData::class)
        ->and($cities->cities)->toHaveCount(2)
        ->and($cities->cities[0]->id)->toBe(1)
        ->and($cities->cities[0]->name)->toBe('Київ')
        ->and($cities->cities[1]->name)->toBe('Харків');
});

it('caches the city list across calls', function (): void {
    fakeUklon(['*/api/v1/cities' => Http::response(['cities' => [['id' => 1, 'name' => 'Київ']]])]);

    UklonDelivery::city()->cache(3600)->all();
    UklonDelivery::city()->cache(3600)->all();

    // One auth + one cities call; the second all() is served from cache.
    expect(Http::recorded(fn ($req) => str_contains($req->url(), '/api/v1/cities')))->toHaveCount(1);
});
