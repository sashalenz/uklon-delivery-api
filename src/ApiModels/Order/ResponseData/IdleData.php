<?php

declare(strict_types=1);

namespace Sashalenz\UklonDelivery\ApiModels\Order\ResponseData;

use Sashalenz\UklonDelivery\Enums\IdleState;
use Spatie\LaravelData\Data;

/**
 * Idle (waiting) state of the order and its free/paid windows.
 */
class IdleData extends Data
{
    public function __construct(
        public readonly IdleState $state,
        public readonly ?FreeData $free = null,
        public readonly ?PaidData $paid = null,
    ) {}
}
