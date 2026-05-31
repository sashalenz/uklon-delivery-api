<?php

declare(strict_types=1);

namespace Sashalenz\UklonDelivery\ApiModels\Fare\ResponseData;

use Spatie\LaravelData\Data;

/**
 * Per-product estimation result — exactly one of `estimation` or `error` is set.
 */
class EstimatedProductData extends Data
{
    public function __construct(
        public readonly ?ProductEstimationData $estimation = null,
        public readonly ?EstimationErrorData $error = null,
    ) {}

    public function isAvailable(): bool
    {
        return $this->estimation !== null;
    }
}
