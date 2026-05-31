<?php

declare(strict_types=1);

namespace Sashalenz\UklonDelivery\ApiModels\Shared\RequestData;

use Spatie\LaravelData\Data;

/**
 * Cash-on-delivery amount collected from a receiver.
 */
class PostpaymentRequest extends Data
{
    public function __construct(
        public readonly float $cost,
    ) {}
}
