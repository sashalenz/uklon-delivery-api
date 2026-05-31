<?php

declare(strict_types=1);

namespace Sashalenz\UklonDelivery\ApiModels\Order\ResponseData;

use Spatie\LaravelData\Data;

/**
 * The account that created the order.
 */
class CreatorData extends Data
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $phone,
    ) {}
}
