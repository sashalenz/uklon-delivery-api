<?php

declare(strict_types=1);

namespace Sashalenz\UklonDelivery\ApiModels\Shared\ResponseData;

use Spatie\LaravelData\Data;

/**
 * Buyout amount the courier pays out at pickup.
 */
class BuyoutData extends Data
{
    public function __construct(
        public readonly float $cost,
    ) {}
}
