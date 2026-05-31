<?php

declare(strict_types=1);

namespace Sashalenz\UklonDelivery;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Sashalenz\UklonDelivery\Exceptions\UklonDeliveryApiUnavailableException;
use Sashalenz\UklonDelivery\Exceptions\UklonDeliveryException;

/**
 * Exchanges the OAuth credentials at POST /auth for a short-lived bearer token
 * and caches it for its `expires_in` lifetime (minus a safety margin), so the
 * token is reused across requests instead of authenticating every call.
 *
 * Docs: https://deliverygateway.uklon.com.ua/docs (Authentication)
 */
final class TokenManager
{
    private const AUTH_ENDPOINT = '/auth';

    /** Seconds subtracted from `expires_in` so a token never expires mid-flight. */
    private const EXPIRY_MARGIN = 30;

    /**
     * Return a valid bearer access token, authenticating only when the cache
     * is cold or the previous token has expired.
     *
     * @throws UklonDeliveryException
     */
    public function getToken(): string
    {
        $cached = Cache::get($this->getCacheKey());

        if (is_string($cached) && $cached !== '') {
            return $cached;
        }

        return $this->authenticate();
    }

    /**
     * Force a fresh authentication, replacing any cached token.
     *
     * @throws UklonDeliveryException
     */
    public function authenticate(): string
    {
        try {
            $response = Http::timeout((int) config('uklon-delivery-api.timeout', 10))
                ->retry(
                    (int) config('uklon-delivery-api.retry_times', 3),
                    (int) config('uklon-delivery-api.retry_sleep', 100),
                    throw: false,
                )
                ->baseUrl($this->baseUrl())
                ->asJson()
                ->acceptJson()
                ->post(self::AUTH_ENDPOINT, [
                    'app_uid' => (string) config('uklon-delivery-api.app_uid'),
                    'client_id' => (string) config('uklon-delivery-api.client_id'),
                    'client_secret' => (string) config('uklon-delivery-api.client_secret'),
                ])
                ->throw();
        } catch (ConnectionException $e) {
            throw new UklonDeliveryApiUnavailableException(
                'Uklon Delivery API unreachable during authentication. '.$e->getMessage(),
                previous: $e,
            );
        } catch (RequestException $e) {
            if ($e->response->serverError()) {
                throw new UklonDeliveryApiUnavailableException(
                    'Uklon Delivery API server error during authentication. '.$e->getMessage(),
                    previous: $e,
                );
            }

            throw new UklonDeliveryException(
                'Uklon Delivery API authentication failed. '.$e->getMessage(),
                previous: $e,
            );
        }

        $token = (string) $response->json('access_token', '');

        if ($token === '') {
            throw new UklonDeliveryException('Uklon Delivery API returned an empty access token.');
        }

        $ttl = max(1, (int) $response->json('expires_in', 0) - self::EXPIRY_MARGIN);

        Cache::put($this->getCacheKey(), $token, $ttl);

        return $token;
    }

    /**
     * Drop the cached token (e.g. after a 401) so the next call re-authenticates.
     */
    public function forget(): void
    {
        Cache::forget($this->getCacheKey());
    }

    private function baseUrl(): string
    {
        return (bool) config('uklon-delivery-api.staging')
            ? (string) config('uklon-delivery-api.staging_url')
            : (string) config('uklon-delivery-api.url');
    }

    private function getCacheKey(): string
    {
        return 'uklon-delivery_token_'.md5((string) config('uklon-delivery-api.client_id'));
    }
}
