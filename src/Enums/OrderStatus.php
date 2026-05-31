<?php

declare(strict_types=1);

namespace Sashalenz\UklonDelivery\Enums;

/**
 * Lifecycle states of an Uklon Delivery order.
 *
 * Docs: https://deliverygateway.uklon.com.ua/docs (order status)
 */
enum OrderStatus: string
{
    case Placed = 'placed';
    case WaitingForProcessing = 'waiting_for_processing';
    case Processing = 'processing';
    case Accepted = 'accepted';
    case Arrived = 'arrived';
    case Running = 'running';
    case Returning = 'returning';
    case Completed = 'completed';
    case Suspended = 'suspended';
    case Canceled = 'canceled';

    /**
     * Whether a courier has been assigned and is (or was) handling the order.
     */
    public function isCourierAssigned(): bool
    {
        return in_array($this, [
            self::Accepted,
            self::Arrived,
            self::Running,
            self::Returning,
            self::Completed,
        ], true);
    }

    /**
     * Whether the order has reached a terminal state (no further updates).
     */
    public function isFinal(): bool
    {
        return in_array($this, [
            self::Completed,
            self::Suspended,
            self::Canceled,
        ], true);
    }
}
