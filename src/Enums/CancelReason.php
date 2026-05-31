<?php

declare(strict_types=1);

namespace Sashalenz\UklonDelivery\Enums;

/**
 * Reasons accepted by the cancel-order endpoint.
 *
 * Docs: https://deliverygateway.uklon.com.ua/docs (cancel order)
 */
enum CancelReason: string
{
    case PackageNotFit = 'package_not_fit';
    case TrunkOccupied = 'trunk_occupied';
    case PlansChanged = 'plans_changed';
    case DriverRefusedPackage = 'driver_refused_package';
    case DriverLowRating = 'driver_low_rating';
    case DriverBehavior = 'driver_behavior';
    case DriverWasLate = 'driver_was_late';
    case DriverNotArrived = 'driver_not_arrived';
    case DriverConfusedAddress = 'driver_confused_address';
    case DriverIgnore = 'driver_ignore';
    case DriverTooFar = 'driver_too_far';
    case DriverAsked = 'driver_asked';
    case AnotherVehicle = 'another_vehicle';
}
