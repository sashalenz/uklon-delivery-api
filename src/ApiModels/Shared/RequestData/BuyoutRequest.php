<?php

declare(strict_types=1);

namespace Sashalenz\UklonDelivery\ApiModels\Shared\RequestData;

use Spatie\LaravelData\Data;

/**
 * Buyout (cash-on-purchase) amount the courier pays out at pickup.
 */
class BuyoutRequest extends Data
{
    public function __construct(
        public readonly float $cost,
    ) {}
}
