<?php

declare(strict_types=1);

namespace Sashalenz\UklonDelivery\ApiModels\Shared\RequestData;

use Spatie\LaravelData\Data;

/**
 * Extra per-receiver parameters: free-form comment and your external tracking
 * number (echoed back in driver-location webhooks as `external_tracking_numbers`).
 */
class ExtraParametersRequest extends Data
{
    public function __construct(
        public readonly ?string $comment = null,
        public readonly ?string $external_tracking_number = null,
    ) {}
}
