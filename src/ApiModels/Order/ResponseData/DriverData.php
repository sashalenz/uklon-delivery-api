<?php

declare(strict_types=1);

namespace Sashalenz\UklonDelivery\ApiModels\Order\ResponseData;

use Sashalenz\UklonDelivery\Enums\DisabilityType;
use Spatie\LaravelData\Data;

/**
 * The assigned courier (present once the order reaches an assigned status).
 */
class DriverData extends Data
{
    public function __construct(
        public readonly string $name,
        public readonly string $phone,
        public readonly ?float $rating = null,
        public readonly ?int $marks_count = null,
        public readonly ?DisabilityType $disabilityType = null,
        public readonly ?string $image_url = null,
        public readonly ?int $registered_at = null,
        public readonly ?int $completed_orders = null,
    ) {}
}
