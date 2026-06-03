<?php

declare(strict_types=1);

namespace Sashalenz\UklonDelivery;

/**
 * Per-account API credentials.
 *
 * Pass an instance to a facade entry point — `UklonDelivery::order($credentials)`,
 * `UklonDelivery::fare($credentials)`, etc. — and every request issued through
 * that model authenticates (and, for webhooks, signs) with these values instead
 * of the global `uklon-delivery-api.*` config defaults. This is what lets a host
 * app drive multiple Uklon accounts ("senders") from a single process.
 *
 * Omit it (or pass `null`) and the SDK falls back to the config file exactly as
 * before — existing single-account callers keep working unchanged.
 *
 * `webhookSecret` is only needed when registering/verifying webhooks; `staging`
 * being null means "use the global config flag".
 */
final readonly class Credentials
{
    public function __construct(
        public string $appUid,
        public string $clientId,
        public string $clientSecret,
        public ?string $webhookSecret = null,
        public ?bool $staging = null,
    ) {}

    /**
     * Build credentials from the package config — the implicit set used when a
     * caller passes no explicit credentials.
     */
    public static function fromConfig(): self
    {
        return new self(
            appUid: (string) config('uklon-delivery-api.app_uid'),
            clientId: (string) config('uklon-delivery-api.client_id'),
            clientSecret: (string) config('uklon-delivery-api.client_secret'),
            webhookSecret: config('uklon-delivery-api.webhook_secret'),
            staging: (bool) config('uklon-delivery-api.staging'),
        );
    }

    /**
     * Whether requests should target the staging gateway. A null `staging`
     * defers to the global config flag.
     */
    public function isStaging(): bool
    {
        return $this->staging ?? (bool) config('uklon-delivery-api.staging');
    }

    /**
     * Resolve the API base URL for this account's environment.
     */
    public function baseUrl(): string
    {
        return $this->isStaging()
            ? (string) config('uklon-delivery-api.staging_url')
            : (string) config('uklon-delivery-api.url');
    }
}
