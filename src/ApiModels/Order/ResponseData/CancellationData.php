<?php

declare(strict_types=1);

namespace Sashalenz\UklonDelivery\ApiModels\Order\ResponseData;

use Spatie\LaravelData\Data;

/**
 * Cancellation details for a canceled order.
 */
class CancellationData extends Data
{
    public function __construct(
        public readonly ?string $reason = null,
    ) {}
}
