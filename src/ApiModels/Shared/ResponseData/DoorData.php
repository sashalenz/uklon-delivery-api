<?php

declare(strict_types=1);

namespace Sashalenz\UklonDelivery\ApiModels\Shared\ResponseData;

use Spatie\LaravelData\Data;

/**
 * Door-to-door details for a sender or receiver address.
 */
class DoorData extends Data
{
    public function __construct(
        public readonly ?string $entrance = null,
        public readonly ?string $floor = null,
        public readonly ?string $apartment = null,
    ) {}
}
