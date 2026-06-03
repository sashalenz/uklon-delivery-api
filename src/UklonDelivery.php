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
 * Multiple accounts: pass {@see Credentials} to any entry point and that
 * model authenticates/signs with those values instead of the config defaults —
 * tokens are cached per `client_id`, so accounts never collide:
 *
 * ```php
 * $creds = new Credentials($appUid, $clientId, $clientSecret, $webhookSecret);
 * UklonDelivery::order($creds)->create($createRequest);
 * ```
 *
 * Docs: https://deliverygateway.uklon.com.ua/docs
 */
final class UklonDelivery
{
    public static function fare(?Credentials $credentials = null): Fare
    {
        return Fare::make($credentials);
    }

    public static function order(?Credentials $credentials = null): Order
    {
        return Order::make($credentials);
    }

    public static function webhook(?Credentials $credentials = null): Webhook
    {
        return Webhook::make($credentials);
    }

    public static function city(?Credentials $credentials = null): City
    {
        return City::make($credentials);
    }
}
