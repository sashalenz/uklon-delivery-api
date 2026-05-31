<?php

declare(strict_types=1);

namespace Sashalenz\UklonDelivery\ApiModels\City\ResponseData;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

/**
 * Response of GET /cities — the cities available for delivery.
 */
class CitiesData extends Data
{
    /**
     * @param  DataCollection<int, CityData>  $cities
     */
    public function __construct(
        #[DataCollectionOf(CityData::class)]
        public readonly DataCollection $cities,
    ) {}
}
