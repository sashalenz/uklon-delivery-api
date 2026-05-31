<?php

declare(strict_types=1);

namespace Sashalenz\UklonDelivery\Exceptions;

/**
 * Base exception for all Uklon Delivery API errors.
 *
 * Catch this to handle both unavailability and application-level errors:
 *
 * ```php
 * try {
 *     $order = UklonDelivery::order()->create($request);
 * } catch (UklonDeliveryApiUnavailableException $e) {
 *     // API is down — retry later, show fallback
 * } catch (UklonDeliveryException $e) {
 *     // Bad request / auth error — log and surface to user
 *     report($e);
 * }
 * ```
 */
class UklonDeliveryException extends \RuntimeException {}
