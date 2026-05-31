<?php

declare(strict_types=1);

namespace Sashalenz\UklonDelivery\ApiModels\Shared\RequestData;

use Spatie\LaravelData\Data;

/**
 * A geographic point (pickup or drop-off) used in fare estimates and routes.
 */
class PointRequest extends Data
{
    public function __construct(
        public readonly float $latitude,
        public readonly float $longitude,
        public readonly ?string $address = null,
    ) {}
}
