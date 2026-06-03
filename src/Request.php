<?php

declare(strict_types=1);

namespace Sashalenz\UklonDelivery;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Sashalenz\UklonDelivery\Exceptions\UklonDeliveryApiUnavailableException;
use Sashalenz\UklonDelivery\Exceptions\UklonDeliveryException;

/**
 * Low-level HTTP transport for the Uklon Delivery Gateway API.
 *
 * Auth is OAuth bearer: every request carries a token obtained and cached by
 * {@see TokenManager}. Responses are plain REST JSON (no envelope); errors are
 * signalled by HTTP status with a `{subcode, message, descriptions}` body.
 *
 * Docs: https://deliverygateway.uklon.com.ua/docs
 */
final class Request
{
    private const TIMEOUT = 10;

    private const RETRY_TIMES = 3;

    private const RETRY_SLEEP = 100;

    /** Marker: payload is at the response root, not nested under a data key. */
    public const DATA_KEY_ROOT = '__root__';

    public function __construct(
        private readonly string $method,
        private readonly array $params = [],
        private readonly string $verb = 'GET',
        private readonly string $dataKey = self::DATA_KEY_ROOT,
        private readonly ?Credentials $credentials = null,
    ) {}

    /**
     * @return Collection<array-key, mixed>
     *
     * @throws UklonDeliveryException
     */
    public function make(): Collection
    {
        try {
            $response = $this->dispatch($this->prepareRequest())->throw();
        } catch (ConnectionException $e) {
            throw new UklonDeliveryApiUnavailableException(
                'Uklon Delivery API unreachable. '.$e->getMessage(),
                previous: $e,
            );
        } catch (RequestException $e) {
            if ($e->response->status() === 401) {
                $this->tokenManager()->forget();
            }

            if ($e->response->serverError()) {
                throw new UklonDeliveryApiUnavailableException(
                    'Uklon Delivery API server error. '.$e->getMessage(),
                    previous: $e,
                );
            }

            throw new UklonDeliveryException(
                'Uklon Delivery API request error. '.$this->errorMessage($e),
                previous: $e,
            );
        }

        // 204 No Content (cancel, webhook delete) — nothing to parse.
        if ($response->status() === 204 || $response->body() === '') {
            return collect();
        }

        $payload = $response->collect();

        if ($this->dataKey === self::DATA_KEY_ROOT) {
            return $payload;
        }

        $value = $payload->get($this->dataKey, $payload->all());

        if (is_array($value)) {
            return collect($value);
        }

        return collect([$value]);
    }

    /**
     * @return Collection<array-key, mixed>
     *
     * @throws UklonDeliveryException
     */
    public function cache(int $seconds = -1): Collection
    {
        if ($seconds === -1) {
            return Cache::rememberForever($this->getCacheKey(), fn () => $this->make());
        }

        return Cache::remember($this->getCacheKey(), $seconds, fn () => $this->make());
    }

    private function dispatch(PendingRequest $request): Response
    {
        return match (strtoupper($this->verb)) {
            'POST' => $request->post($this->method, $this->params),
            'PUT' => $request->put($this->method, $this->params),
            'PATCH' => $request->patch($this->method, $this->params),
            'DELETE' => $request->delete($this->method, $this->params),
            default => $request->get($this->method, $this->params),
        };
    }

    private function prepareRequest(): PendingRequest
    {
        return Http::timeout((int) config('uklon-delivery-api.timeout', self::TIMEOUT))
            ->retry(
                (int) config('uklon-delivery-api.retry_times', self::RETRY_TIMES),
                (int) config('uklon-delivery-api.retry_sleep', self::RETRY_SLEEP),
                throw: false,
            )
            ->baseUrl($this->baseUrl())
            ->asJson()
            ->acceptJson()
            ->withToken($this->tokenManager()->getToken());
    }

    /**
     * A token manager bound to this request's credentials (or the global config
     * defaults when none were passed). Cheap to instantiate — the OAuth token it
     * issues is shared via the cache, keyed per `client_id`.
     */
    private function tokenManager(): TokenManager
    {
        return $this->credentials === null
            ? app(TokenManager::class)
            : new TokenManager($this->credentials);
    }

    private function baseUrl(): string
    {
        return ($this->credentials ?? Credentials::fromConfig())->baseUrl();
    }

    /**
     * Surface the API's `{subcode, message}` error body when present.
     */
    private function errorMessage(RequestException $e): string
    {
        $body = $e->response->json();

        if (is_array($body) && isset($body['message'])) {
            $subcode = isset($body['subcode']) ? ' ['.$body['subcode'].']' : '';

            return (string) $body['message'].$subcode;
        }

        return $e->getMessage();
    }

    private function getCacheKey(): string
    {
        return collect([
            'uklon-delivery',
            $this->method,
            strtolower($this->verb),
            base64_encode(serialize($this->params)),
        ])->implode('_');
    }
}
