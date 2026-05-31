<?php

declare(strict_types=1);

namespace Sashalenz\UklonDelivery\ApiModels\Webhook\RequestData;

use Spatie\LaravelData\Data;

/**
 * Request body for registering an order/driver webhook subscription.
 *
 * `url` receives the event POSTs; `key` is the shared secret Uklon uses to sign
 * them (store it to verify inbound requests). `custom_headers` optionally adds
 * fixed HTTP headers to every delivery — shape: `['values' => [['name' => ..., 'value' => ...]]]`.
 */
class SetWebhookRequest extends Data
{
    /**
     * @param  array<string, mixed>|null  $custom_headers
     */
    public function __construct(
        public readonly string $url,
        public readonly string $key,
        public readonly ?array $custom_headers = null,
    ) {}
}
