<?php

declare(strict_types=1);

namespace Sashalenz\UklonDelivery\ApiModels\City;

use Sashalenz\UklonDelivery\ApiModels\BaseModel;
use Sashalenz\UklonDelivery\ApiModels\City\ResponseData\CitiesData;
use Sashalenz\UklonDelivery\Exceptions\UklonDeliveryException;

/**
 * Reference data — cities available for delivery.
 *
 * A city `id` is the `city` value required when estimating a fare. The list
 * rarely changes, so cache it: `UklonDelivery::city()->cache(3600)->all()`.
 *
 * Docs: https://deliverygateway.uklon.com.ua/docs
 */
final class City extends BaseModel
{
    private const CITIES = '/cities';

    /**
     * Get the list of available delivery cities (GET /cities).
     *
     * @throws UklonDeliveryException
     */
    public function all(): ?CitiesData
    {
        return $this->reset()
            ->method(self::CITIES)
            ->toData(CitiesData::class);
    }
}
