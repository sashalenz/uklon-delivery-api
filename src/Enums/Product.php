<?php

declare(strict_types=1);

namespace Sashalenz\UklonDelivery\Enums;

/**
 * Delivery product (vehicle class) offered by Uklon.
 *
 * Docs: https://deliverygateway.uklon.com.ua/docs
 */
enum Product: string
{
    case Car = 'car';
    case Courier = 'courier';
}
