<?php

declare(strict_types=1);

namespace Sashalenz\UklonDelivery\ApiModels\Order\ResponseData;

use Spatie\LaravelData\Data;

/**
 * Order lifecycle timestamps (Unix seconds).
 */
class TimesData extends Data
{
    public function __construct(
        public readonly int $creation,
        public readonly ?int $acceptance = null,
        public readonly ?int $arrival = null,
        public readonly ?int $estimated_arrival = null,
    ) {}
}
