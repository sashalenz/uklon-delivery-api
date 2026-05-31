<?php

declare(strict_types=1);

namespace Sashalenz\UklonDelivery\ApiModels\Order\ResponseData;

use Sashalenz\UklonDelivery\ApiModels\Shared\ResponseData\DistanceData;
use Spatie\LaravelData\Data;

/**
 * The order route: city id, total distance and the ordered points.
 */
class RouteData extends Data
{
    public function __construct(
        public readonly int $city,
        public readonly DistanceData $distance,
        public readonly PointsData $points,
    ) {}
}
