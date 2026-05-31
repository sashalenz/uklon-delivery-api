<?php

declare(strict_types=1);

namespace Sashalenz\UklonDelivery\ApiModels\Fare\ResponseData;

use Spatie\LaravelData\Data;

/**
 * Successful estimation for a product: cost range and route.
 */
class ProductEstimationData extends Data
{
    public function __construct(
        public readonly EstimatedCostData $cost,
        public readonly EstimatedRouteData $route,
    ) {}
}
