<?php

declare(strict_types=1);

namespace Sashalenz\UklonDelivery\Webhook\Middleware;

use Closure;
use Illuminate\Http\Request;
use Sashalenz\UklonDelivery\Webhook\WebhookSignatureValidator;
use Symfony\Component\HttpFoundation\Response;

/**
 * Rejects Uklon webhook requests whose `X-Signature` does not match.
 *
 * Registered under the `uklon-webhook` alias, so you can guard your webhook
 * routes with it directly:
 *
 * ```php
 * Route::post('/webhooks/uklon/order', OrderWebhookController::class)
 *     ->middleware('uklon-webhook');
 * ```
 *
 * By default the shared secret is read from `uklon-delivery-api.webhook_secret`.
 * Pass a different config key (or a literal secret resolver) per route via the
 * middleware parameter when you store secrets elsewhere, e.g. multi-tenant:
 * `->middleware('uklon-webhook:services.uklon.order_secret')`.
 */
final class VerifyUklonWebhookSignature
{
    public function __construct(
        private readonly WebhookSignatureValidator $validator,
    ) {}

    public function handle(Request $request, Closure $next, ?string $secretConfigKey = null): Response
    {
        $secret = (string) config($secretConfigKey ?? 'uklon-delivery-api.webhook_secret', '');

        abort_if(
            $secret === '' || ! $this->validator->isValidRequest($request, $secret),
            Response::HTTP_FORBIDDEN,
            'Invalid Uklon webhook signature.',
        );

        return $next($request);
    }
}
