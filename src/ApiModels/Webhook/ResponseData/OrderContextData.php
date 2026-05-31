<?php

declare(strict_types=1);

namespace Sashalenz\UklonDelivery\ApiModels\Webhook\ResponseData;

use Spatie\LaravelData\Data;

/**
 * Which order a driver-location event belongs to, plus your external tracking
 * numbers (from each receiver's `extra_parameters.external_tracking_number`).
 */
class OrderContextData extends Data
{
    /**
     * @param  array<int, string>  $external_tracking_numbers
     */
    public function __construct(
        public readonly string $id,
        public readonly array $external_tracking_numbers = [],
    ) {}
}
