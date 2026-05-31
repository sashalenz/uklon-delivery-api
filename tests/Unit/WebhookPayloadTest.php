<?php

declare(strict_types=1);

use Sashalenz\UklonDelivery\ApiModels\Webhook\ResponseData\DriverLocationEventData;
use Sashalenz\UklonDelivery\ApiModels\Webhook\ResponseData\OrderUpdateEventData;
use Sashalenz\UklonDelivery\Enums\OrderStatus;

it('hydrates an inbound order-update webhook payload', function (): void {
    $event = OrderUpdateEventData::from([
        'items' => [
            [
                'order' => orderResponseBody(['status' => 'accepted']),
                'event_id' => 'evt-1',
                'occurred_at' => 1700000123,
            ],
        ],
    ]);

    expect($event->items)->toHaveCount(1)
        ->and($event->items[0]->event_id)->toBe('evt-1')
        ->and($event->items[0]->occurred_at)->toBe(1700000123)
        ->and($event->items[0]->order->status)->toBe(OrderStatus::Accepted)
        ->and($event->items[0]->order->status->isCourierAssigned())->toBeTrue();
});

it('hydrates an inbound driver-location webhook payload', function (): void {
    $event = DriverLocationEventData::from([
        'location' => ['latitude' => 50.45, 'longitude' => 30.52, 'eta' => 180],
        'order_context' => [
            'id' => 'order-id',
            'external_tracking_numbers' => ['A20-1001', 'A20-1002'],
        ],
        'event_id' => 'evt-2',
        'occurred_at' => 1700000456,
    ]);

    expect($event->location->latitude)->toBe(50.45)
        ->and($event->location->eta)->toBe(180)
        ->and($event->order_context->id)->toBe('order-id')
        ->and($event->order_context->external_tracking_numbers)->toBe(['A20-1001', 'A20-1002'])
        ->and($event->event_id)->toBe('evt-2');
});
