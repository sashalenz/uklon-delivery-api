<?php

declare(strict_types=1);

namespace Sashalenz\UklonDelivery\ApiModels\Fare\RequestData;

use Spatie\LaravelData\Data;

/**
 * Optional fare conditions, e.g. the maximum parcel weight.
 */
class ConditionsRequest extends Data
{
    public function __construct(
        public readonly ?int $max_weight_grams = null,
    ) {}
}
