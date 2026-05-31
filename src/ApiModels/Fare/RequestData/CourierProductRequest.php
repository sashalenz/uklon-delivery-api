<?php

declare(strict_types=1);

namespace Sashalenz\UklonDelivery\ApiModels\Fare\RequestData;

use Spatie\LaravelData\Data;

/**
 * Options requested for the `courier` product when estimating a fare.
 */
class CourierProductRequest extends Data
{
    public function __construct(
        public readonly ?bool $door = null,
        public readonly bool $confirmation_code = false,
        public readonly bool $buyout = false,
        public readonly bool $age_verification = false,
        public readonly ?DeferredRequest $deferred = null,
        public readonly ?FarePostpaymentRequest $postpayment = null,
    ) {}
}
