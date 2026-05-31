<?php

declare(strict_types=1);

namespace Sashalenz\UklonDelivery\ApiModels\Fare\RequestData;

use Sashalenz\UklonDelivery\ApiModels\Order\RequestData\CreateOrderRequest;
use Sashalenz\UklonDelivery\ApiModels\Shared\RequestData\PointRequest;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

/**
 * Request body for POST /fares/estimate.
 *
 * The returned fare `id` must be passed to {@see CreateOrderRequest}
 * when placing the order (the estimate expires, see `expires_at`).
 */
class EstimateFareRequest extends Data
{
    /**
     * @param  DataCollection<int, PointRequest>  $dropoff_points
     */
    public function __construct(
        public readonly int $city,
        public readonly PointRequest $pickup_point,
        #[DataCollectionOf(PointRequest::class)]
        public readonly DataCollection $dropoff_points,
        public readonly ?ProductsRequest $products = null,
        public readonly ?ConditionsRequest $conditions = null,
        public readonly ?string $strategy_id = null,
    ) {}
}
