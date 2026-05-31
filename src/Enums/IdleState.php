<?php

declare(strict_types=1);

namespace Sashalenz\UklonDelivery\Enums;

/**
 * Idle (waiting) state of an order — whether the courier is waiting for free
 * or paid, or not waiting at all.
 *
 * Docs: https://deliverygateway.uklon.com.ua/docs
 */
enum IdleState: string
{
    case None = 'none';
    case Free = 'free';
    case Paid = 'paid';
}
