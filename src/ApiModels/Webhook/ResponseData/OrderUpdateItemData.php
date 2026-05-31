<?php

declare(strict_types=1);

namespace Sashalenz\UklonDelivery\ApiModels\Webhook\ResponseData;

use Sashalenz\UklonDelivery\ApiModels\Order\ResponseData\OrderData;
use Spatie\LaravelData\Data;

/**
 * A single order-update event inside an {@see OrderUpdateEventData} payload.
 */
class OrderUpdateItemData extends Data
{
    public function __construct(
        public readonly OrderData $order,
        public readonly string $event_id,
        public readonly int $occurred_at,
    ) {}
}
