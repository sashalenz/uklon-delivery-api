<?php

declare(strict_types=1);

namespace Sashalenz\UklonDelivery\ApiModels\Webhook;

use Sashalenz\UklonDelivery\ApiModels\BaseModel;
use Sashalenz\UklonDelivery\ApiModels\Webhook\RequestData\SetWebhookRequest;
use Sashalenz\UklonDelivery\ApiModels\Webhook\ResponseData\WebhookData;
use Sashalenz\UklonDelivery\Exceptions\UklonDeliveryException;

/**
 * Webhook subscription management.
 *
 * Each company may register one order-update and one driver-location webhook.
 * The `key` you provide is the shared secret Uklon uses to sign inbound events —
 * persist it to verify incoming payloads. Parse received events with
 * {@see ResponseData\OrderUpdateEventData} / {@see ResponseData\DriverLocationEventData}.
 *
 * Docs: https://deliverygateway.uklon.com.ua/docs (webhooks)
 */
final class Webhook extends BaseModel
{
    private const ORDER = '/webhooks/order';

    private const DRIVER = '/webhooks/driver';

    /**
     * Register/replace the order-update webhook (POST /webhooks/order).
     *
     * @throws UklonDeliveryException
     */
    public function setForOrder(SetWebhookRequest $request): void
    {
        $this->subscribe(self::ORDER, $request);
    }

    /**
     * Get the current order-update webhook (GET /webhooks/order).
     *
     * @throws UklonDeliveryException
     */
    public function getForOrder(): ?WebhookData
    {
        return $this->reset()
            ->method(self::ORDER)
            ->toData(WebhookData::class);
    }

    /**
     * Delete the order-update webhook (DELETE /webhooks/order).
     *
     * @throws UklonDeliveryException
     */
    public function deleteForOrder(): void
    {
        $this->unsubscribe(self::ORDER);
    }

    /**
     * Register/replace the driver-location webhook (POST /webhooks/driver).
     *
     * @throws UklonDeliveryException
     */
    public function setForDriver(SetWebhookRequest $request): void
    {
        $this->subscribe(self::DRIVER, $request);
    }

    /**
     * Get the current driver-location webhook (GET /webhooks/driver).
     *
     * @throws UklonDeliveryException
     */
    public function getForDriver(): ?WebhookData
    {
        return $this->reset()
            ->method(self::DRIVER)
            ->toData(WebhookData::class);
    }

    /**
     * Delete the driver-location webhook (DELETE /webhooks/driver).
     *
     * @throws UklonDeliveryException
     */
    public function deleteForDriver(): void
    {
        $this->unsubscribe(self::DRIVER);
    }

    /**
     * @throws UklonDeliveryException
     */
    private function subscribe(string $endpoint, SetWebhookRequest $request): void
    {
        $this->reset()
            ->method($endpoint)
            ->params($request)
            ->post()
            ->send();
    }

    /**
     * @throws UklonDeliveryException
     */
    private function unsubscribe(string $endpoint): void
    {
        $this->reset()
            ->method($endpoint)
            ->delete()
            ->send();
    }
}
