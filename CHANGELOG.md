# Changelog

All notable changes to `sashalenz/uklon-delivery-api` will be documented in this file.

## Unreleased

### Added
- `UklonDelivery` entry-point facade with `fare()`, `order()`, `webhook()` and `city()` modules.
- OAuth bearer authentication with transparent token caching (`TokenManager`).
- Staging / production gateway switching via config.
- **Fare**: `estimate()` (POST `/fares/estimate`).
- **Order**: `create()`, `get()`, `getActive()`, `getArchived()`, `getCourierLocation()`, `cancel()`.
- **Webhook**: `setForOrder()` / `getForOrder()` / `deleteForOrder()` and the `…ForDriver()` variants.
- **City**: `all()` (cacheable).
- Fully-typed `spatie/laravel-data` DTOs for every request and response, including the
  inbound webhook payloads `OrderUpdateEventData` and `DriverLocationEventData`.
- Webhook signature verification: `WebhookSignatureValidator` (HMAC-SHA256, `X-Signature`)
  and the `uklon-webhook` route middleware.
- Enums: `OrderStatus`, `CancelReason`, `Product`, `DropoffStatus`, `IdleState`, `DisabilityType`.
- `UklonDeliveryException` / `UklonDeliveryApiUnavailableException` hierarchy.
