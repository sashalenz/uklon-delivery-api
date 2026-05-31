<?php

declare(strict_types=1);

namespace Sashalenz\UklonDelivery\Exceptions;

/**
 * Thrown when the Uklon Delivery API is unreachable (network error, 5xx, timeout).
 *
 * Treat as a transient infrastructure issue — retry later or show a cached
 * fallback. Route to a lower severity in your error tracker:
 *
 * ```php
 * // bootstrap/app.php
 * ->withExceptions(fn (Exceptions $e) => $e
 *     ->report(function (UklonDeliveryApiUnavailableException $ex) {
 *         Bugsnag::notifyException($ex, fn ($r) => $r->setSeverity('warning'));
 *         return false;
 *     })
 * )
 * ```
 */
class UklonDeliveryApiUnavailableException extends UklonDeliveryException {}
