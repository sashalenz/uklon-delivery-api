<?php

declare(strict_types=1);

namespace Sashalenz\UklonDelivery\ApiModels\Order\ResponseData;

use Sashalenz\UklonDelivery\ApiModels\Shared\ResponseData\PointData;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

/**
 * Pickup, drop-off and optional return points of an order route.
 */
class PointsData extends Data
{
    /**
     * @param  DataCollection<int, DropoffPointData>  $dropoffs
     */
    public function __construct(
        public readonly PointData $pickup,
        #[DataCollectionOf(DropoffPointData::class)]
        public readonly DataCollection $dropoffs,
        public readonly ?PointData $return = null,
    ) {}
}
