<?php

declare(strict_types=1);

namespace Sashalenz\UklonDelivery\ApiModels\Webhook\ResponseData;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

/**
 * Inbound payload delivered to your order-webhook URL. Hydrate it in your
 * controller with `OrderUpdateEventData::from($request->all())`.
 */
class OrderUpdateEventData extends Data
{
    /**
     * @param  DataCollection<int, OrderUpdateItemData>  $items
     */
    public function __construct(
        #[DataCollectionOf(OrderUpdateItemData::class)]
        public readonly DataCollection $items,
    ) {}
}
