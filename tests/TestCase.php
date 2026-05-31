<?php

declare(strict_types=1);

namespace Sashalenz\UklonDelivery\Tests;

use Illuminate\Support\Facades\Cache;
use Orchestra\Testbench\TestCase as Orchestra;
use Sashalenz\UklonDelivery\UklonDeliveryServiceProvider;
use Spatie\LaravelData\LaravelDataServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        // The OAuth token is cached between requests; clear it so each test
        // starts cold and the /auth call count is deterministic.
        Cache::flush();
    }

    protected function getPackageProviders($app): array
    {
        return [
            LaravelDataServiceProvider::class,
            UklonDeliveryServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        config()->set('uklon-delivery-api.staging', true);
        config()->set('uklon-delivery-api.url', 'https://deliverygateway.uklon.com.ua/api/v1');
        config()->set('uklon-delivery-api.staging_url', 'https://deliverygateway.staging.uklon.com.ua/api/v1');
        config()->set('uklon-delivery-api.app_uid', 'test-app-uid');
        config()->set('uklon-delivery-api.client_id', 'test-client-id');
        config()->set('uklon-delivery-api.client_secret', 'test-client-secret');
    }
}
