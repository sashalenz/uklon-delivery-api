<?php

declare(strict_types=1);

namespace Sashalenz\UklonDelivery\Webhook;

use Illuminate\Http\Request;

/**
 * Verifies inbound Uklon webhook deliveries.
 *
 * Per the API docs, every webhook request is signed with the secret `key` you
 * provided at webhook creation, using the `SHA-256 HMAC` algorithm over the raw
 * UTF-8 request body. The resulting signature is sent in the `X-Signature`
 * header. A delivery whose signature does not match must be ignored as either
 * malformed or forged.
 *
 * The comparison is timing-safe ({@see hash_equals()}) and accepts the signature
 * as either lowercase/uppercase hex or base64, so it works regardless of how the
 * digest is encoded on the wire.
 *
 * Docs: https://deliverygateway.uklon.com.ua/docs (Webhooks)
 */
final class WebhookSignatureValidator
{
    /** HTTP header carrying the HMAC signature. */
    public const HEADER = 'X-Signature';

    /**
     * Compute the canonical (lowercase hex) HMAC-SHA256 signature for a payload.
     */
    public function sign(string $payload, string $key): string
    {
        return hash_hmac('sha256', $payload, $key);
    }

    /**
     * Whether `$signature` is a valid HMAC-SHA256 of `$payload` under `$key`.
     */
    public function isValid(string $payload, string $signature, string $key): bool
    {
        $signature = trim($signature);

        if ($signature === '' || $key === '') {
            return false;
        }

        $binary = hash_hmac('sha256', $payload, $key, true);

        return hash_equals(bin2hex($binary), strtolower($signature))
            || hash_equals(base64_encode($binary), $signature);
    }

    /**
     * Verify an incoming Laravel request: checks the `X-Signature` header against
     * the raw request body. Returns false when the header is missing.
     */
    public function isValidRequest(Request $request, string $key): bool
    {
        $signature = (string) $request->header(self::HEADER, '');

        if ($signature === '') {
            return false;
        }

        return $this->isValid($request->getContent(), $signature, $key);
    }
}
