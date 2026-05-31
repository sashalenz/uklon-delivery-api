<?php

declare(strict_types=1);

namespace Sashalenz\UklonDelivery\ApiModels\Shared\RequestData;

use Spatie\LaravelData\Data;

/**
 * Door-to-door delivery details for a sender or receiver address.
 */
class DoorRequest extends Data
{
    public function __construct(
        public readonly ?string $entrance = null,
        public readonly ?string $floor = null,
        public readonly ?string $apartment = null,
    ) {}
}
