<?php

declare(strict_types=1);

namespace Sashalenz\UklonDelivery\ApiModels\Fare\RequestData;

use Spatie\LaravelData\Data;

/**
 * Scheduled (deferred) order — `arrival` is a Unix timestamp for pickup.
 */
class DeferredRequest extends Data
{
    public function __construct(
        public readonly int $arrival,
    ) {}
}
