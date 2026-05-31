<?php

declare(strict_types=1);

use Sashalenz\UklonDelivery\Enums\CancelReason;
use Sashalenz\UklonDelivery\Enums\DisabilityType;
use Sashalenz\UklonDelivery\Enums\DropoffStatus;
use Sashalenz\UklonDelivery\Enums\IdleState;
use Sashalenz\UklonDelivery\Enums\OrderStatus;
use Sashalenz\UklonDelivery\Enums\Product;

it('has the exact order-status values from the API', function (): void {
    expect(OrderStatus::Placed->value)->toBe('placed')
        ->and(OrderStatus::WaitingForProcessing->value)->toBe('waiting_for_processing')
        ->and(OrderStatus::Processing->value)->toBe('processing')
        ->and(OrderStatus::Accepted->value)->toBe('accepted')
        ->and(OrderStatus::Arrived->value)->toBe('arrived')
        ->and(OrderStatus::Running->value)->toBe('running')
        ->and(OrderStatus::Returning->value)->toBe('returning')
        ->and(OrderStatus::Completed->value)->toBe('completed')
        ->and(OrderStatus::Suspended->value)->toBe('suspended')
        ->and(OrderStatus::Canceled->value)->toBe('canceled');
});

it('classifies courier-assigned order statuses', function (): void {
    expect(OrderStatus::Placed->isCourierAssigned())->toBeFalse()
        ->and(OrderStatus::Processing->isCourierAssigned())->toBeFalse()
        ->and(OrderStatus::Accepted->isCourierAssigned())->toBeTrue()
        ->and(OrderStatus::Running->isCourierAssigned())->toBeTrue()
        ->and(OrderStatus::Completed->isCourierAssigned())->toBeTrue()
        ->and(OrderStatus::Canceled->isCourierAssigned())->toBeFalse();
});

it('classifies final order statuses', function (): void {
    expect(OrderStatus::Completed->isFinal())->toBeTrue()
        ->and(OrderStatus::Suspended->isFinal())->toBeTrue()
        ->and(OrderStatus::Canceled->isFinal())->toBeTrue()
        ->and(OrderStatus::Running->isFinal())->toBeFalse();
});

it('has the exact cancel-reason values from the API', function (): void {
    expect(CancelReason::PackageNotFit->value)->toBe('package_not_fit')
        ->and(CancelReason::TrunkOccupied->value)->toBe('trunk_occupied')
        ->and(CancelReason::PlansChanged->value)->toBe('plans_changed')
        ->and(CancelReason::DriverRefusedPackage->value)->toBe('driver_refused_package')
        ->and(CancelReason::DriverLowRating->value)->toBe('driver_low_rating')
        ->and(CancelReason::DriverBehavior->value)->toBe('driver_behavior')
        ->and(CancelReason::DriverWasLate->value)->toBe('driver_was_late')
        ->and(CancelReason::DriverNotArrived->value)->toBe('driver_not_arrived')
        ->and(CancelReason::DriverConfusedAddress->value)->toBe('driver_confused_address')
        ->and(CancelReason::DriverIgnore->value)->toBe('driver_ignore')
        ->and(CancelReason::DriverTooFar->value)->toBe('driver_too_far')
        ->and(CancelReason::DriverAsked->value)->toBe('driver_asked')
        ->and(CancelReason::AnotherVehicle->value)->toBe('another_vehicle');
});

it('has the supporting enum values', function (): void {
    expect(Product::Car->value)->toBe('car')
        ->and(Product::Courier->value)->toBe('courier')
        ->and(DropoffStatus::Delivered->value)->toBe('delivered')
        ->and(DropoffStatus::NotDelivered->value)->toBe('not_delivered')
        ->and(DropoffStatus::ReturnRequested->value)->toBe('return_requested')
        ->and(IdleState::None->value)->toBe('none')
        ->and(IdleState::Free->value)->toBe('free')
        ->and(IdleState::Paid->value)->toBe('paid')
        ->and(DisabilityType::None->value)->toBe('none')
        ->and(DisabilityType::Deaf->value)->toBe('deaf')
        ->and(DisabilityType::HardHearing->value)->toBe('hard_hearing');
});
