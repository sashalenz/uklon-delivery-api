<?php

declare(strict_types=1);

namespace Sashalenz\UklonDelivery\ApiModels\Order\ResponseData;

use Spatie\LaravelData\Data;

/**
 * Paid idle-waiting window for the order (Unix seconds / elapsed seconds).
 */
class PaidData extends Data
{
    public function __construct(
        public readonly int $completed_seconds,
        public readonly ?int $started_at = null,
    ) {}
}
