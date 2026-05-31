<?php

declare(strict_types=1);

namespace Sashalenz\UklonDelivery\ApiModels\Order\ResponseData;

use Spatie\LaravelData\Data;

/**
 * Live courier position for an order (GET /orders/{id}/driver/location).
 *
 * `bearing` is the heading in degrees; `next_point_eta` is seconds to the next
 * route point.
 */
class CourierLocationData extends Data
{
    public function __construct(
        public readonly float $latitude,
        public readonly float $longitude,
        public readonly ?int $bearing = null,
        public readonly ?int $next_point_eta = null,
    ) {}
}
