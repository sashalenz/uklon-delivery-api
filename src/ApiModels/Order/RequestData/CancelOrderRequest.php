<?php

declare(strict_types=1);

namespace Sashalenz\UklonDelivery\ApiModels\Order\RequestData;

use Sashalenz\UklonDelivery\Enums\CancelReason;
use Spatie\LaravelData\Data;

/**
 * Request body for PUT /orders/{id}/cancel.
 */
class CancelOrderRequest extends Data
{
    public function __construct(
        public readonly CancelReason $reason,
    ) {}
}
