<?php

declare(strict_types=1);

namespace Sashalenz\UklonDelivery\ApiModels\Shared\ResponseData;

use Spatie\LaravelData\Data;

/**
 * Extra per-receiver parameters echoed back on the order.
 */
class ExtraParametersData extends Data
{
    public function __construct(
        public readonly ?string $comment = null,
        public readonly ?string $external_tracking_number = null,
    ) {}
}
