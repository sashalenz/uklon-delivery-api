<?php

declare(strict_types=1);

namespace Sashalenz\UklonDelivery\ApiModels\Shared\ResponseData;

use Spatie\LaravelData\Data;

/**
 * A geographic point returned within an order route.
 */
class PointData extends Data
{
    public function __construct(
        public readonly float $latitude,
        public readonly float $longitude,
        public readonly ?string $address = null,
    ) {}
}
