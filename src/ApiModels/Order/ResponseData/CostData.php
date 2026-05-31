<?php

declare(strict_types=1);

namespace Sashalenz\UklonDelivery\ApiModels\Order\ResponseData;

use Sashalenz\UklonDelivery\ApiModels\Shared\ResponseData\BuyoutData;
use Spatie\LaravelData\Data;

/**
 * The order's cost breakdown.
 */
class CostData extends Data
{
    public function __construct(
        public readonly string $currency,
        public readonly float $total,
        public readonly float $route,
        public readonly float $minimum = 0,
        public readonly float $maximum = 0,
        public readonly ?float $return = null,
        public readonly ?BuyoutData $buyout = null,
        public readonly ?IdleCostData $idle = null,
    ) {}
}
