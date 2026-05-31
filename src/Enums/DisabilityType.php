<?php

declare(strict_types=1);

namespace Sashalenz\UklonDelivery\Enums;

/**
 * Courier accessibility / disability type (affects how to communicate).
 *
 * Docs: https://deliverygateway.uklon.com.ua/docs
 */
enum DisabilityType: string
{
    case None = 'none';
    case Deaf = 'deaf';
    case HardHearing = 'hard_hearing';
}
