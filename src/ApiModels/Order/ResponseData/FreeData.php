<?php

declare(strict_types=1);

namespace Sashalenz\UklonDelivery\ApiModels\Order\ResponseData;

use Spatie\LaravelData\Data;

/**
 * Free idle-waiting window for the order (Unix seconds / elapsed seconds).
 */
class FreeData extends Data
{
    public function __construct(
        public readonly int $completed_seconds,
        public readonly ?int $ends_at = null,
    ) {}
}
