<?php

declare(strict_types=1);

namespace Sashalenz\UklonDelivery\ApiModels\Order\RequestData;

use Sashalenz\UklonDelivery\ApiModels\Shared\RequestData\AppraisedDetailsRequest;
use Sashalenz\UklonDelivery\ApiModels\Shared\RequestData\BuyoutRequest;
use Sashalenz\UklonDelivery\ApiModels\Shared\RequestData\DoorRequest;
use Sashalenz\UklonDelivery\ApiModels\Shared\RequestData\ExtraParametersRequest;
use Sashalenz\UklonDelivery\ApiModels\Shared\RequestData\PostpaymentRequest;
use Spatie\LaravelData\Data;

/**
 * A drop-off recipient. An order may have several receivers (multi-drop).
 */
class ReceiverRequest extends Data
{
    public function __construct(
        public readonly string $name,
        public readonly string $phone,                // format: +380XXXXXXXXX
        public readonly ?DoorRequest $door = null,
        public readonly ?bool $age_verification = null,
        public readonly ?BuyoutRequest $buyout = null,
        public readonly ?ExtraParametersRequest $extra_parameters = null,
        public readonly ?AppraisedDetailsRequest $appraised_details = null,
        public readonly ?PostpaymentRequest $postpayment = null,
    ) {}
}
