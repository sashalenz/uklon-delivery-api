<?php

declare(strict_types=1);

namespace Sashalenz\UklonDelivery\ApiModels\Shared\ResponseData;

use Spatie\LaravelData\Data;

/**
 * A person (order sender) with optional door details.
 */
class PersonData extends Data
{
    public function __construct(
        public readonly string $name,
        public readonly string $phone,
        public readonly ?DoorData $door = null,
    ) {}
}
