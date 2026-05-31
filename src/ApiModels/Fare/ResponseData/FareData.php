<?php

declare(strict_types=1);

namespace Sashalenz\UklonDelivery\ApiModels\Fare\ResponseData;

use Spatie\LaravelData\Data;

/**
 * Response of POST /fares/estimate.
 *
 * `id` is the fare identifier required to create an order; `expires_at` is a
 * Unix timestamp after which the estimate is no longer valid.
 */
class FareData extends Data
{
    public function __construct(
        public readonly string $id,
        public readonly string $currency,
        public readonly EstimatedProductsData $estimated_products,
        public readonly int $expires_at,
    ) {}
}
