<?php

declare(strict_types=1);

namespace Sashalenz\UklonDelivery\ApiModels\Order\ResponseData;

use Sashalenz\UklonDelivery\ApiModels\Order\Order;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

/**
 * A cursor-paginated page of orders (active or archived). Pass `next_cursor`
 * back to {@see Order::getArchived()}
 * to fetch the next page.
 */
class OrderListData extends Data
{
    /**
     * @param  DataCollection<int, OrderData>  $items
     */
    public function __construct(
        #[DataCollectionOf(OrderData::class)]
        public readonly DataCollection $items,
        public readonly ?string $next_cursor = null,
    ) {}
}
