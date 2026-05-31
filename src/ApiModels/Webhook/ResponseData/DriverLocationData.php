<?php

declare(strict_types=1);

namespace Sashalenz\UklonDelivery\ApiModels\Webhook\ResponseData;

use Spatie\LaravelData\Data;

/**
 * Courier position inside a driver-location webhook payload.
 */
class DriverLocationData extends Data
{
    public function __construct(
        public readonly float $latitude,
        public readonly float $longitude,
        public readonly int $eta,
    ) {}
}
