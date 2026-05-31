<?php

declare(strict_types=1);

namespace Sashalenz\UklonDelivery\ApiModels\Order\ResponseData;

use Sashalenz\UklonDelivery\ApiModels\Shared\ResponseData\AppraisedDetailsData;
use Sashalenz\UklonDelivery\ApiModels\Shared\ResponseData\BuyoutData;
use Sashalenz\UklonDelivery\ApiModels\Shared\ResponseData\DoorData;
use Sashalenz\UklonDelivery\ApiModels\Shared\ResponseData\ExtraParametersData;
use Sashalenz\UklonDelivery\ApiModels\Shared\ResponseData\PostpaymentData;
use Spatie\LaravelData\Data;

/**
 * A drop-off recipient as returned on an order.
 */
class ReceiverData extends Data
{
    public function __construct(
        public readonly string $name,
        public readonly string $phone,
        public readonly ?DoorData $door = null,
        public readonly ?bool $age_verification = null,
        public readonly ?BuyoutData $buyout = null,
        public readonly ?ExtraParametersData $extra_parameters = null,
        public readonly ?AppraisedDetailsData $appraised_details = null,
        public readonly ?PostpaymentData $postpayment = null,
    ) {}
}
