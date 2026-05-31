<?php

declare(strict_types=1);

namespace Sashalenz\UklonDelivery\ApiModels\Order\RequestData;

use Sashalenz\UklonDelivery\ApiModels\Fare\ResponseData\FareData;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

/**
 * Request body for POST /orders.
 *
 * `fare_id` comes from a prior POST /fares/estimate ({@see FareData::$id}).
 * `product` selects which estimated product to book (`car` / `courier`);
 * `agreed_cost` should fall within the estimated cost range for that product.
 */
class CreateOrderRequest extends Data
{
    /**
     * @param  DataCollection<int, ReceiverRequest>  $receivers
     */
    public function __construct(
        public readonly string $fare_id,
        public readonly PersonRequest $sender,
        #[DataCollectionOf(ReceiverRequest::class)]
        public readonly DataCollection $receivers,
        public readonly ?string $product = null,
        public readonly ?string $comment = null,
        public readonly ?float $agreed_cost = null,
    ) {}
}
