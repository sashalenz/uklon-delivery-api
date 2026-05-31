<?php

declare(strict_types=1);

namespace Sashalenz\UklonDelivery\ApiModels\Fare\RequestData;

use Spatie\LaravelData\Data;

/**
 * Which products to estimate, with their per-product options. Provide at least
 * one of `car` or `courier`.
 */
class ProductsRequest extends Data
{
    public function __construct(
        public readonly ?CarProductRequest $car = null,
        public readonly ?CourierProductRequest $courier = null,
    ) {}
}
