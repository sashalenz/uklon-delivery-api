<?php

declare(strict_types=1);

namespace Sashalenz\UklonDelivery\ApiModels\Fare\ResponseData;

use Sashalenz\UklonDelivery\ApiModels\Shared\ResponseData\DistanceData;
use Spatie\LaravelData\Data;

/**
 * Estimated route for a product: distance, encoded polyline and drive time.
 */
class EstimatedRouteData extends Data
{
    public function __construct(
        public readonly DistanceData $distance,
        public readonly string $overview_polyline,
        public readonly int $drive_time_seconds,
    ) {}
}
