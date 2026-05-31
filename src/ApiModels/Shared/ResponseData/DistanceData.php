<?php

declare(strict_types=1);

namespace Sashalenz\UklonDelivery\ApiModels\Shared\ResponseData;

use Spatie\LaravelData\Data;

/**
 * Route distance split into city and suburban segments.
 *
 * Note: the API uses camelCase keys here (`cityMeters`, `suburbanMeters`),
 * unlike the snake_case used elsewhere — property names match verbatim.
 */
class DistanceData extends Data
{
    public function __construct(
        public readonly int $cityMeters,
        public readonly int $suburbanMeters,
    ) {}

    public function getTotalMeters(): int
    {
        return $this->cityMeters + $this->suburbanMeters;
    }
}
