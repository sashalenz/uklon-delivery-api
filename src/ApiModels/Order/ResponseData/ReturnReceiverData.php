<?php

declare(strict_types=1);

namespace Sashalenz\UklonDelivery\ApiModels\Order\ResponseData;

use Sashalenz\UklonDelivery\ApiModels\Shared\ResponseData\BuyoutData;
use Sashalenz\UklonDelivery\ApiModels\Shared\ResponseData\DoorData;
use Spatie\LaravelData\Data;

/**
 * Contact that undelivered parcels are returned to.
 */
class ReturnReceiverData extends Data
{
    public function __construct(
        public readonly string $name,
        public readonly string $phone,
        public readonly ?DoorData $door = null,
        public readonly ?BuyoutData $buyout = null,
        public readonly ?string $comment = null,
    ) {}
}
