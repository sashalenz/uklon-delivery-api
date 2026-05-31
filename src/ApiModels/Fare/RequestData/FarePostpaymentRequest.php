<?php

declare(strict_types=1);

namespace Sashalenz\UklonDelivery\ApiModels\Fare\RequestData;

use Spatie\LaravelData\Data;

/**
 * Post-payment merchant configuration for a fare product.
 */
class FarePostpaymentRequest extends Data
{
    public function __construct(
        public readonly string $merchant_id,
    ) {}
}
