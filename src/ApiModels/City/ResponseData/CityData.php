<?php

declare(strict_types=1);

namespace Sashalenz\UklonDelivery\ApiModels\City\ResponseData;

use Spatie\LaravelData\Data;

/**
 * A city available for delivery. Its `id` is the `city` value for fare estimates.
 */
class CityData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
    ) {}
}
