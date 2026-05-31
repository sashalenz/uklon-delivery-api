<?php

declare(strict_types=1);

namespace Sashalenz\UklonDelivery\ApiModels\Fare\ResponseData;

use Spatie\LaravelData\Data;

/**
 * Estimated cost range for a product. Pass `recommended` as `agreed_cost`
 * when creating the order, or any value within [minimum, maximum].
 */
class EstimatedCostData extends Data
{
    public function __construct(
        public readonly float $minimum,
        public readonly float $recommended,
        public readonly float $maximum,
        public readonly float $surge_multiplier,
        public readonly float $main_route,
        public readonly ?float $return = null,
    ) {}
}
