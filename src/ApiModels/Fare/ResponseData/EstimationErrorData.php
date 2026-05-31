<?php

declare(strict_types=1);

namespace Sashalenz\UklonDelivery\ApiModels\Fare\ResponseData;

use Spatie\LaravelData\Data;

/**
 * Why a product could not be estimated (present instead of `estimation`).
 */
class EstimationErrorData extends Data
{
    public function __construct(
        public readonly string $sub_code,
    ) {}
}
