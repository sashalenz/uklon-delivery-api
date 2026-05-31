<?php

declare(strict_types=1);

namespace Sashalenz\UklonDelivery\ApiModels\Order\ResponseData;

use Sashalenz\UklonDelivery\ApiModels\Order\Order;
use Spatie\LaravelData\Data;

/**
 * Response of POST /orders — only the new order `id` is returned. Call
 * {@see Order::get()} for the full order.
 */
class CreatedOrderData extends Data
{
    public function __construct(
        public readonly string $id,
    ) {}
}
