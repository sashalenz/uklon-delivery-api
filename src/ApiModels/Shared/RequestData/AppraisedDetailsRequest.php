<?php

declare(strict_types=1);

namespace Sashalenz\UklonDelivery\ApiModels\Shared\RequestData;

use Spatie\LaravelData\Data;

/**
 * Declared value of the parcel for a receiver.
 */
class AppraisedDetailsRequest extends Data
{
    public function __construct(
        public readonly float $cost,
    ) {}
}
