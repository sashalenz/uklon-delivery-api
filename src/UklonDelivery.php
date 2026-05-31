<?php

declare(strict_types=1);

namespace Sashalenz\UklonDelivery;

use Sashalenz\UklonDelivery\ApiModels\City\City;
use Sashalenz\UklonDelivery\ApiModels\Fare\Fare;
use Sashalenz\UklonDelivery\ApiModels\Order\Order;
use Sashalenz\UklonDelivery\ApiModels\Webhook\Webhook;

/**
 * Top-level entry-point for the Uklon Delivery Gateway API.
 *
 * ```php
 * $fare  = UklonDelivery::fare()->estimate($estimateRequest);
 * $order = UklonDelivery::order()->create($createRequest);
 * $data  = UklonDelivery::order()->get($order->id);
 * UklonDelivery::order()->cancel($order->id, $cancelRequest);
 *
 * UklonDelivery::webhook()->setForOrder($webhookRequest);
 * $cities = UklonDelivery::city()->cache(3600)->all();
 * ```
 *
 * Authentication (OAuth bearer token) is handled transparently by
 * {@see TokenManager}; just configure `app_uid`/`client_id`/`client_secret`.
 *
 * Docs: https://deliverygateway.uklon.com.ua/docs
 */
final class UklonDelivery
{
    public static function fare(): Fare
    {
        return new Fare;
    }

    public static function order(): Order
    {
        return new Order;
    }

    public static function webhook(): Webhook
    {
        return new Webhook;
    }

    public static function city(): City
    {
        return new City;
    }
}
