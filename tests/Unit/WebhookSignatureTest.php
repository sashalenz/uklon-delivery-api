<?php

declare(strict_types=1);

use Illuminate\Http\Request;
use Sashalenz\UklonDelivery\Webhook\Middleware\VerifyUklonWebhookSignature;
use Sashalenz\UklonDelivery\Webhook\WebhookSignatureValidator;
use Symfony\Component\HttpKernel\Exception\HttpException;

function signedRequest(string $payload, string $signature): Request
{
    return Request::create('/webhooks/uklon', 'POST', [], [], [], [
        'HTTP_X_SIGNATURE' => $signature,
        'CONTENT_TYPE' => 'application/json',
    ], $payload);
}

beforeEach(function (): void {
    $this->validator = new WebhookSignatureValidator;
    $this->key = 'super-secret-key';
    $this->payload = '{"items":[{"event_id":"evt-1","occurred_at":1700000000}]}';
    $this->signature = hash_hmac('sha256', $this->payload, $this->key);
});

it('signs a payload with HMAC-SHA256 hex', function (): void {
    expect($this->validator->sign($this->payload, $this->key))
        ->toBe(hash_hmac('sha256', $this->payload, $this->key))
        ->toMatch('/^[0-9a-f]{64}$/');
});

it('accepts a valid hex signature', function (): void {
    expect($this->validator->isValid($this->payload, $this->signature, $this->key))->toBeTrue();
});

it('accepts a valid uppercase hex signature', function (): void {
    expect($this->validator->isValid($this->payload, strtoupper($this->signature), $this->key))->toBeTrue();
});

it('accepts a valid base64 signature', function (): void {
    $base64 = base64_encode(hash_hmac('sha256', $this->payload, $this->key, true));

    expect($this->validator->isValid($this->payload, $base64, $this->key))->toBeTrue();
});

it('rejects a tampered payload', function (): void {
    expect($this->validator->isValid($this->payload.'x', $this->signature, $this->key))->toBeFalse();
});

it('rejects a wrong key', function (): void {
    expect($this->validator->isValid($this->payload, $this->signature, 'other-key'))->toBeFalse();
});

it('rejects an empty signature or key', function (): void {
    expect($this->validator->isValid($this->payload, '', $this->key))->toBeFalse()
        ->and($this->validator->isValid($this->payload, $this->signature, ''))->toBeFalse();
});

it('validates an incoming request via the X-Signature header', function (): void {
    expect($this->validator->isValidRequest(signedRequest($this->payload, $this->signature), $this->key))->toBeTrue()
        ->and($this->validator->isValidRequest(signedRequest($this->payload, 'deadbeef'), $this->key))->toBeFalse();
});

it('returns false when the signature header is missing', function (): void {
    $request = Request::create('/webhooks/uklon', 'POST', [], [], [], [], $this->payload);

    expect($this->validator->isValidRequest($request, $this->key))->toBeFalse();
});

it('passes a correctly signed request through the middleware', function (): void {
    config()->set('uklon-delivery-api.webhook_secret', $this->key);
    $middleware = app(VerifyUklonWebhookSignature::class);

    $response = $middleware->handle(
        signedRequest($this->payload, $this->signature),
        fn () => response('handled'),
    );

    expect($response->getContent())->toBe('handled');
});

it('aborts 403 on a forged request in the middleware', function (): void {
    config()->set('uklon-delivery-api.webhook_secret', $this->key);
    $middleware = app(VerifyUklonWebhookSignature::class);

    $call = fn () => $middleware->handle(
        signedRequest($this->payload, 'forged-signature'),
        fn () => response('handled'),
    );

    expect($call)->toThrow(HttpException::class);
});

it('aborts 403 when no webhook secret is configured', function (): void {
    config()->set('uklon-delivery-api.webhook_secret', null);
    $middleware = app(VerifyUklonWebhookSignature::class);

    $call = fn () => $middleware->handle(
        signedRequest($this->payload, $this->signature),
        fn () => response('handled'),
    );

    expect($call)->toThrow(HttpException::class);
});

it('registers the uklon-webhook middleware alias', function (): void {
    $aliases = app('router')->getMiddleware();

    expect($aliases)->toHaveKey('uklon-webhook')
        ->and($aliases['uklon-webhook'])->toBe(VerifyUklonWebhookSignature::class);
});
