<?php

declare(strict_types=1);

namespace Sashalenz\UklonDelivery;

use Illuminate\Routing\Router;
use Sashalenz\UklonDelivery\Webhook\Middleware\VerifyUklonWebhookSignature;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class UklonDeliveryServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('uklon-delivery-api')
            ->hasConfigFile('uklon-delivery-api');
    }

    public function registeringPackage(): void
    {
        // One TokenManager per container — it owns the cached OAuth token and
        // must be shared so Request::make() and TokenManager::forget() (on 401)
        // operate on the same cached state.
        $this->app->singleton(TokenManager::class);
    }

    public function packageBooted(): void
    {
        // Guard webhook routes with ->middleware('uklon-webhook').
        $this->app->make(Router::class)
            ->aliasMiddleware('uklon-webhook', VerifyUklonWebhookSignature::class);
    }
}
