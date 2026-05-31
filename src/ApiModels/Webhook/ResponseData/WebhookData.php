<?php

declare(strict_types=1);

namespace Sashalenz\UklonDelivery\ApiModels\Webhook\ResponseData;

use Spatie\LaravelData\Data;

/**
 * The currently registered webhook subscription (url + shared secret).
 */
class WebhookData extends Data
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
