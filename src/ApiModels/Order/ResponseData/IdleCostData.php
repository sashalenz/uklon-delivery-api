<?php

declare(strict_types=1);

namespace Sashalenz\UklonDelivery\ApiModels\Order\ResponseData;

use Spatie\LaravelData\Data;

/**
 * Idle (waiting) charge included in the order cost.
 */
class IdleCostData extends Data
{
    public function __construct(
        public readonly float $amount,
        public readonly int $minutes,
    ) {}
}
