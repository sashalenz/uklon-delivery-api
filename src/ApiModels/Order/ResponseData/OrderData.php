<?php

declare(strict_types=1);

namespace Sashalenz\UklonDelivery\ApiModels\Order\ResponseData;

use Sashalenz\UklonDelivery\ApiModels\Shared\ResponseData\PersonData;
use Sashalenz\UklonDelivery\Enums\OrderStatus;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

/**
 * Full order object returned by GET /orders/{id}, the order lists, and order
 * webhooks. Only `id` and `status` are guaranteed; everything else is populated
 * as the order progresses.
 */
class OrderData extends Data
{
    /**
     * @param  DataCollection<int, ReceiverData>  $receivers
     * @param  array<int, mixed>  $extra_services
     */
    public function __construct(
        public readonly string $id,
        public readonly OrderStatus $status,
        #[DataCollectionOf(ReceiverData::class)]
        public readonly DataCollection $receivers,
        public readonly ?string $product = null,
        public readonly ?TimesData $times = null,
        public readonly ?PersonData $sender = null,
        public readonly ?ReturnReceiverData $return_receiver = null,
        public readonly ?CreatorData $creator = null,
        public readonly ?DriverData $driver = null,
        public readonly ?RouteData $route = null,
        public readonly ?CostData $cost = null,
        public readonly ?CancellationData $cancellation = null,
        public readonly ?string $comment = null,
        public readonly bool $suspended = false,
        public readonly bool $deferred = false,
        public readonly array $extra_services = [],
        public readonly ?IdleData $idle = null,
    ) {}
}
