<?php

declare(strict_types=1);

namespace Sashalenz\UklonDelivery\ApiModels\Order\RequestData;

use Sashalenz\UklonDelivery\ApiModels\Shared\RequestData\DoorRequest;
use Spatie\LaravelData\Data;

/**
 * The order sender (pickup contact).
 */
class PersonRequest extends Data
{
    public function __construct(
        public readonly string $name,
        public readonly string $phone,           // format: +380XXXXXXXXX
        public readonly ?DoorRequest $door = null,
    ) {}
}
