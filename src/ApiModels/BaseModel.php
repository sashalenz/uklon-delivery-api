<?php

declare(strict_types=1);

namespace Sashalenz\UklonDelivery\ApiModels;

use Illuminate\Support\Collection;
use Illuminate\Support\Traits\Conditionable;
use Sashalenz\UklonDelivery\Credentials;
use Sashalenz\UklonDelivery\Exceptions\UklonDeliveryException;
use Sashalenz\UklonDelivery\Request;
use Spatie\LaravelData\Contracts\BaseData;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

/**
 * @phpstan-consistent-constructor
 */
abstract class BaseModel
{
    use Conditionable;

    protected ?string $calledMethod = null;

    protected array $params = [];

    protected string $verb = 'GET';

    protected string $dataKey = Request::DATA_KEY_ROOT;

    protected bool $canBeCached = false;

    protected int $cacheSeconds = -1;

    /**
     * Account credentials for every request this model makes. Null means "use
     * the global config defaults" — preserved across {@see self::reset()} so a
     * single model instance stays bound to one account across chained calls.
     */
    protected ?Credentials $credentials = null;

    public static function make(?Credentials $credentials = null): static
    {
        $instance = new static;
        $instance->credentials = $credentials;

        return $instance;
    }

    /**
     * Bind (or rebind) the account credentials for subsequent requests.
     */
    public function withCredentials(?Credentials $credentials): static
    {
        $this->credentials = $credentials;

        return $this;
    }

    public function cache(int $seconds = -1): static
    {
        $this->canBeCached = true;
        $this->cacheSeconds = $seconds;

        return $this;
    }

    /**
     * Return a clone with per-call state cleared. Call at the start of every
     * public method so chained reuse doesn't leak state across calls.
     */
    protected function reset(): static
    {
        $clone = clone $this;
        $clone->calledMethod = null;
        $clone->params = [];
        $clone->verb = 'GET';
        $clone->dataKey = Request::DATA_KEY_ROOT;

        return $clone;
    }

    protected function method(string $method): static
    {
        $this->calledMethod = $method;

        return $this;
    }

    protected function params(?Data $request = null): static
    {
        $this->params = $request === null ? [] : self::pruneNulls($request->toArray());

        return $this;
    }

    protected function rawParams(array $params): static
    {
        $this->params = self::pruneNulls($params);

        return $this;
    }

    protected function post(): static
    {
        $this->verb = 'POST';

        return $this;
    }

    protected function put(): static
    {
        $this->verb = 'PUT';

        return $this;
    }

    protected function delete(): static
    {
        $this->verb = 'DELETE';

        return $this;
    }

    protected function dataKey(string $key): static
    {
        $this->dataKey = $key;

        return $this;
    }

    /**
     * @return Collection<array-key, mixed>
     *
     * @throws UklonDeliveryException
     */
    protected function request(): Collection
    {
        if ($this->calledMethod === null) {
            throw new UklonDeliveryException('API method not specified before request().');
        }

        $request = new Request(
            method: $this->calledMethod,
            params: $this->params,
            verb: $this->verb,
            dataKey: $this->dataKey,
            credentials: $this->credentials,
        );

        if ($this->canBeCached) {
            return $request->cache($this->cacheSeconds);
        }

        return $request->make();
    }

    /**
     * Fire the request and return the raw collection (used for endpoints whose
     * body is discarded, e.g. cancel / webhook delete).
     *
     * @return Collection<array-key, mixed>
     *
     * @throws UklonDeliveryException
     */
    protected function send(): Collection
    {
        return $this->request();
    }

    /**
     * @throws UklonDeliveryException
     */
    protected function first(): array
    {
        $first = $this->request()->first();

        return is_array($first) ? $first : [];
    }

    /**
     * @template T of BaseData
     *
     * @param  class-string<T>  $class
     * @return T|null
     *
     * @throws UklonDeliveryException
     */
    protected function toData(string $class): ?BaseData
    {
        $payload = $this->request();

        if ($payload->isEmpty()) {
            return null;
        }

        $first = $payload->first();

        if (is_array($first) && array_is_list($first) === false) {
            /** @var T */
            return $class::from($first);
        }

        /** @var T */
        return $class::from($payload->all());
    }

    /**
     * @template T of BaseData
     *
     * @param  class-string<T>  $class
     * @return DataCollection<int, T>
     *
     * @throws UklonDeliveryException
     */
    protected function toCollection(string $class): DataCollection
    {
        return new DataCollection($class, $this->request()->all());
    }

    /**
     * Strip null values so optional fields aren't sent as empty strings/nulls.
     *
     * @param  array<string, mixed>  $params
     * @return array<string, mixed>
     */
    private static function pruneNulls(array $params): array
    {
        $out = [];

        foreach ($params as $key => $value) {
            if ($value === null) {
                continue;
            }

            $out[$key] = is_array($value) ? self::pruneNulls($value) : $value;
        }

        return $out;
    }
}
