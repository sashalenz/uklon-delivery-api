<?php

declare(strict_types=1);

namespace Sashalenz\UklonDelivery\ApiModels\Order\ResponseData;

use Sashalenz\UklonDelivery\Enums\DropoffStatus;
use Spatie\LaravelData\Data;

/**
 * A drop-off point on the order route, with its current delivery status.
 */
class DropoffPointData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly float $latitude,
        public readonly float $longitude,
        public readonly DropoffStatus $status,
        public readonly ?string $address = null,
    ) {}
}
