<?php

declare(strict_types=1);

namespace Sashalenz\UklonDelivery\ApiModels\Shared\ResponseData;

use Spatie\LaravelData\Data;

/**
 * Declared value of the parcel for a receiver.
 */
class AppraisedDetailsData extends Data
{
    public function __construct(
        public readonly float $cost,
    ) {}
}
