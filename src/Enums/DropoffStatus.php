<?php

declare(strict_types=1);

namespace Sashalenz\UklonDelivery\Enums;

/**
 * Delivery status of an individual drop-off point within an order route.
 *
 * Docs: https://deliverygateway.uklon.com.ua/docs
 */
enum DropoffStatus: string
{
    case Delivering = 'delivering';
    case Arrived = 'arrived';
    case Delivered = 'delivered';
    case NotDelivered = 'not_delivered';
    case ReturnRequested = 'return_requested';
    case Returning = 'returning';
    case Returned = 'returned';
}
