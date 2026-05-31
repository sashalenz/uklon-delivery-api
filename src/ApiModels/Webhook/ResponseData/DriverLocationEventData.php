<?php

declare(strict_types=1);

namespace Sashalenz\UklonDelivery\ApiModels\Webhook\ResponseData;

use Spatie\LaravelData\Data;

/**
 * Inbound payload delivered to your driver-webhook URL. Hydrate it in your
 * controller with `DriverLocationEventData::from($request->all())`.
 */
class DriverLocationEventData extends Data
{
    public function __construct(
        public readonly DriverLocationData $location,
        public readonly OrderContextData $order_context,
        public readonly string $event_id,
        public readonly int $occurred_at,
    ) {}
}
