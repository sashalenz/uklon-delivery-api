<?php

declare(strict_types=1);

namespace Sashalenz\UklonDelivery\ApiModels\Order;

use Sashalenz\UklonDelivery\ApiModels\BaseModel;
use Sashalenz\UklonDelivery\ApiModels\Fare\Fare;
use Sashalenz\UklonDelivery\ApiModels\Order\RequestData\CancelOrderRequest;
use Sashalenz\UklonDelivery\ApiModels\Order\RequestData\CreateOrderRequest;
use Sashalenz\UklonDelivery\ApiModels\Order\ResponseData\CourierLocationData;
use Sashalenz\UklonDelivery\ApiModels\Order\ResponseData\CreatedOrderData;
use Sashalenz\UklonDelivery\ApiModels\Order\ResponseData\OrderData;
use Sashalenz\UklonDelivery\ApiModels\Order\ResponseData\OrderListData;
use Sashalenz\UklonDelivery\Exceptions\UklonDeliveryException;

/**
 * Order management.
 *
 * Typical flow: estimate a fare ({@see Fare::estimate()}),
 * then {@see self::create()} with the returned `fare_id`, then {@see self::get()}
 * to track it (or subscribe to order webhooks).
 *
 * Docs: https://deliverygateway.uklon.com.ua/docs
 */
final class Order extends BaseModel
{
    private const ORDERS = '/orders';

    /**
     * Create a new delivery order (POST /orders). Returns the new order id only.
     *
     * @throws UklonDeliveryException
     */
    public function create(CreateOrderRequest $request): ?CreatedOrderData
    {
        return $this->reset()
            ->method(self::ORDERS)
            ->params($request)
            ->post()
            ->toData(CreatedOrderData::class);
    }

    /**
     * Get full order details by id (GET /orders/{id}).
     *
     * @throws UklonDeliveryException
     */
    public function get(string $orderId): ?OrderData
    {
        return $this->reset()
            ->method(self::ORDERS.'/'.$orderId)
            ->toData(OrderData::class);
    }

    /**
     * List active (in-progress) orders (GET /orders/active).
     *
     * @throws UklonDeliveryException
     */
    public function getActive(): ?OrderListData
    {
        return $this->reset()
            ->method(self::ORDERS.'/active')
            ->toData(OrderListData::class);
    }

    /**
     * List archived (finished) orders, cursor-paginated (GET /orders/archived).
     *
     * @throws UklonDeliveryException
     */
    public function getArchived(int $limit, ?string $cursor = null): ?OrderListData
    {
        return $this->reset()
            ->method(self::ORDERS.'/archived')
            ->rawParams(['limit' => $limit, 'cursor' => $cursor])
            ->toData(OrderListData::class);
    }

    /**
     * Get the live courier position for an order (GET /orders/{id}/driver/location).
     *
     * @throws UklonDeliveryException
     */
    public function getCourierLocation(string $orderId): ?CourierLocationData
    {
        return $this->reset()
            ->method(self::ORDERS.'/'.$orderId.'/driver/location')
            ->toData(CourierLocationData::class);
    }

    /**
     * Cancel an order (PUT /orders/{id}/cancel). Throws on failure.
     *
     * @throws UklonDeliveryException
     */
    public function cancel(string $orderId, CancelOrderRequest $request): void
    {
        $this->reset()
            ->method(self::ORDERS.'/'.$orderId.'/cancel')
            ->params($request)
            ->put()
            ->send();
    }
}
