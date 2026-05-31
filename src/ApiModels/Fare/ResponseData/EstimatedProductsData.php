<?php

declare(strict_types=1);

namespace Sashalenz\UklonDelivery\ApiModels\Fare\ResponseData;

use Spatie\LaravelData\Data;

/**
 * Estimations keyed by product. Each is present only if it was requested.
 */
class EstimatedProductsData extends Data
{
    public function __construct(
        public readonly ?EstimatedProductData $car = null,
        public readonly ?EstimatedProductData $courier = null,
    ) {}

    public function isEmpty(): bool
    {
        return $this->car === null && $this->courier === null;
    }
}
