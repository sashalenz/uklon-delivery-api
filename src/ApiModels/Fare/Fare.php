<?php

declare(strict_types=1);

namespace Sashalenz\UklonDelivery\ApiModels\Fare;

use Sashalenz\UklonDelivery\ApiModels\BaseModel;
use Sashalenz\UklonDelivery\ApiModels\Fare\RequestData\EstimateFareRequest;
use Sashalenz\UklonDelivery\ApiModels\Fare\ResponseData\FareData;
use Sashalenz\UklonDelivery\Exceptions\UklonDeliveryException;

/**
 * Fare estimation.
 *
 * Estimating a fare returns a `fare_id` together with per-product cost/route
 * estimates. That `fare_id` is required to create an order, and it expires
 * (see {@see FareData::$expires_at}).
 *
 * Docs: https://deliverygateway.uklon.com.ua/docs
 */
final class Fare extends BaseModel
{
    private const ESTIMATE = '/fares/estimate';

    /**
     * Estimate price and route for a delivery (POST /fares/estimate).
     *
     * @throws UklonDeliveryException
     */
    public function estimate(EstimateFareRequest $request): ?FareData
    {
        return $this->reset()
            ->method(self::ESTIMATE)
            ->params($request)
            ->post()
            ->toData(FareData::class);
    }
}
