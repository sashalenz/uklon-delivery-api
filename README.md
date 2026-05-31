# Uklon Delivery API

[![CI](https://github.com/sashalenz/uklon-delivery-api/actions/workflows/ci.yml/badge.svg)](https://github.com/sashalenz/uklon-delivery-api/actions/workflows/ci.yml)
[![Latest Version](https://img.shields.io/packagist/v/sashalenz/uklon-delivery-api.svg)](https://packagist.org/packages/sashalenz/uklon-delivery-api)
[![License](https://img.shields.io/packagist/l/sashalenz/uklon-delivery-api.svg)](LICENSE.md)

Laravel SDK for the **[Uklon Delivery Gateway API](https://deliverygateway.uklon.com.ua/docs)** —
B2B intra-city courier delivery. Estimate fares, create and track orders, manage
webhooks, all through a small fluent facade with fully-typed
[`spatie/laravel-data`](https://github.com/spatie/laravel-data) DTOs.

```php
use Sashalenz\UklonDelivery\UklonDelivery;

$fare  = UklonDelivery::fare()->estimate($estimateRequest);   // → fare_id + cost/route
$order = UklonDelivery::order()->create($createRequest);      // → order id
$info  = UklonDelivery::order()->get($order->id);             // → full order
UklonDelivery::order()->cancel($order->id, $cancelRequest);
```

## Requirements

* PHP **8.2+**
* Laravel **11 / 12 / 13**
* `spatie/laravel-data` **^4.4**

## Installation

```bash
composer require sashalenz/uklon-delivery-api
```

The service provider is auto-discovered. Optionally publish the config:

```bash
php artisan vendor:publish --tag=uklon-delivery-api-config
```

## Configuration

Uklon issues OAuth credentials (`app_uid`, `client_id`, `client_secret`) per
company, separately for the **staging** and **production** gateways. Add them to
your `.env`:

```dotenv
UKLON_DELIVERY_STAGING=true            # false → production gateway

UKLON_DELIVERY_APP_UID=your-app-uid
UKLON_DELIVERY_CLIENT_ID=your-client-id
UKLON_DELIVERY_CLIENT_SECRET=your-client-secret

# optional
UKLON_DELIVERY_TIMEOUT=10
```

| Key | Env | Default |
|---|---|---|
| `staging` | `UKLON_DELIVERY_STAGING` | `false` |
| `url` | `UKLON_DELIVERY_API_URL` | `https://deliverygateway.uklon.com.ua/api/v1` |
| `staging_url` | `UKLON_DELIVERY_API_STAGING_URL` | `https://deliverygateway.staging.uklon.com.ua/api/v1` |
| `app_uid` | `UKLON_DELIVERY_APP_UID` | — |
| `client_id` | `UKLON_DELIVERY_CLIENT_ID` | — |
| `client_secret` | `UKLON_DELIVERY_CLIENT_SECRET` | — |
| `webhook_secret` | `UKLON_DELIVERY_WEBHOOK_SECRET` | — |
| `timeout` | `UKLON_DELIVERY_TIMEOUT` | `10` |

### Authentication & token caching

You never call the auth endpoint yourself. Before each request the SDK obtains a
bearer token from `POST /auth` and **caches it** (via Laravel's cache) for its
`expires_in` lifetime, so repeated calls reuse the same token. On a `401` the
cached token is dropped automatically so the next call re-authenticates. This is
handled by [`TokenManager`](src/TokenManager.php) using your configured cache
driver.

## Usage

### 1. Estimate a fare

Every order starts with a fare estimate. The returned `fare_id` is required to
create the order and **expires** (see `expires_at`).

```php
use Sashalenz\UklonDelivery\ApiModels\Fare\RequestData\EstimateFareRequest;
use Sashalenz\UklonDelivery\UklonDelivery;

$fare = UklonDelivery::fare()->estimate(EstimateFareRequest::from([
    'city'         => 1,                                  // see UklonDelivery::city()->all()
    'pickup_point' => ['latitude' => 50.4501, 'longitude' => 30.5234, 'address' => 'вул. Хрещатик, 1'],
    'dropoff_points' => [
        ['latitude' => 50.4547, 'longitude' => 30.5238, 'address' => 'вул. Сумська, 2'],
    ],
    'products'   => ['car' => []],                        // 'car' and/or 'courier'
    'conditions' => ['max_weight_grams' => 5000],         // optional
]));

$car = $fare->estimated_products->car;
if ($car?->isAvailable()) {
    $car->estimation->cost->recommended;          // e.g. 120.0
    $car->estimation->route->drive_time_seconds;  // e.g. 600
    $car->estimation->route->distance->getTotalMeters();
}
```

### 2. Create an order

```php
use Sashalenz\UklonDelivery\ApiModels\Order\RequestData\CreateOrderRequest;
use Sashalenz\UklonDelivery\UklonDelivery;

$created = UklonDelivery::order()->create(CreateOrderRequest::from([
    'fare_id'     => $fare->id,
    'product'     => 'car',
    'agreed_cost' => $car->estimation->cost->recommended,
    'sender'      => [
        'name'  => 'Магазин',
        'phone' => '+380660000000',
        'door'  => ['entrance' => '1', 'floor' => '2', 'apartment' => '5'],
    ],
    'receivers' => [
        [
            'name'  => 'Іван Іванов',
            'phone' => '+380501234567',
            'extra_parameters' => ['external_tracking_number' => 'A20-1001'],
            'postpayment'      => ['cost' => 350.0],   // cash on delivery
        ],
    ],
    'comment' => 'Не телефонувати після 20:00',
]));

$created->id; // Uklon order id
```

### 3. Track an order

```php
$order = UklonDelivery::order()->get($created->id);

$order->status;                              // OrderStatus enum
$order->status->isCourierAssigned();         // bool
$order->driver?->name;
$order->route->points->dropoffs[0]->status;  // DropoffStatus enum
$order->cost->total;

// Live courier position (once a courier is assigned)
$location = UklonDelivery::order()->getCourierLocation($created->id);
$location->latitude; $location->longitude; $location->next_point_eta;
```

### 4. List orders

Both lists are cursor-paginated and return an `OrderListData` (`items` + `next_cursor`).

```php
$active = UklonDelivery::order()->getActive();

$page = UklonDelivery::order()->getArchived(limit: 50);
$next = $page->next_cursor
    ? UklonDelivery::order()->getArchived(50, $page->next_cursor)
    : null;
```

### 5. Cancel an order

```php
use Sashalenz\UklonDelivery\ApiModels\Order\RequestData\CancelOrderRequest;
use Sashalenz\UklonDelivery\Enums\CancelReason;

UklonDelivery::order()->cancel(
    $created->id,
    new CancelOrderRequest(CancelReason::PlansChanged),
);
```

### Reference data — cities

A city `id` is the `city` value for fare estimates. The list rarely changes, so
cache it:

```php
$cities = UklonDelivery::city()->cache(3600)->all();

foreach ($cities->cities as $city) {
    $city->id;   // 1
    $city->name; // 'Київ'
}
```

## API surface

| Call | Method | Endpoint |
|---|---|---|
| `fare()->estimate($request)` | POST | `/fares/estimate` |
| `order()->create($request)` | POST | `/orders` |
| `order()->get($id)` | GET | `/orders/{id}` |
| `order()->getActive()` | GET | `/orders/active` |
| `order()->getArchived($limit, $cursor)` | GET | `/orders/archived` |
| `order()->getCourierLocation($id)` | GET | `/orders/{id}/driver/location` |
| `order()->cancel($id, $request)` | PUT | `/orders/{id}/cancel` |
| `webhook()->setForOrder($request)` | POST | `/webhooks/order` |
| `webhook()->getForOrder()` | GET | `/webhooks/order` |
| `webhook()->deleteForOrder()` | DELETE | `/webhooks/order` |
| `webhook()->setForDriver($request)` | POST | `/webhooks/driver` |
| `webhook()->getForDriver()` | GET | `/webhooks/driver` |
| `webhook()->deleteForDriver()` | DELETE | `/webhooks/driver` |
| `city()->all()` | GET | `/cities` |

## Webhooks

Uklon can push **order-update** and **driver-location** events to your app. You
register a subscription with a callback URL and a shared `key` (secret), one of
each per company.

### Registering a subscription

```php
use Sashalenz\UklonDelivery\ApiModels\Webhook\RequestData\SetWebhookRequest;
use Sashalenz\UklonDelivery\UklonDelivery;

UklonDelivery::webhook()->setForOrder(new SetWebhookRequest(
    url: route('webhooks.uklon.order'),
    key: config('services.uklon.webhook_secret'),
));

UklonDelivery::webhook()->setForDriver(new SetWebhookRequest(
    url: route('webhooks.uklon.driver'),
    key: config('services.uklon.webhook_secret'),
));

$current = UklonDelivery::webhook()->getForOrder(); // WebhookData { url, key }
UklonDelivery::webhook()->deleteForOrder();
```

### Handling incoming events

Parse the request body into typed DTOs in your controller:

```php
use Sashalenz\UklonDelivery\ApiModels\Webhook\ResponseData\OrderUpdateEventData;
use Sashalenz\UklonDelivery\ApiModels\Webhook\ResponseData\DriverLocationEventData;

// POST route registered as the order webhook URL
public function order(Request $request)
{
    $event = OrderUpdateEventData::from($request->all());

    foreach ($event->items as $item) {
        $item->order;        // full OrderData
        $item->order->status; // OrderStatus enum
        $item->event_id;
        $item->occurred_at;  // Unix timestamp
    }
}

// POST route registered as the driver webhook URL
public function driver(Request $request)
{
    $event = DriverLocationEventData::from($request->all());

    $event->location->latitude;
    $event->location->longitude;
    $event->location->eta;
    $event->order_context->id;                        // order id
    $event->order_context->external_tracking_numbers; // your tracking numbers
}
```

### Verifying signatures

Every webhook delivery is signed with the `key` you registered, using
**HMAC-SHA256** over the raw request body; the signature is sent in the
**`X-Signature`** header. A delivery whose signature does not match must be
ignored as malformed or forged.

The package ships a ready-to-use middleware registered under the `uklon-webhook`
alias. It reads the secret from `uklon-delivery-api.webhook_secret`
(`UKLON_DELIVERY_WEBHOOK_SECRET`) and returns `403` on a bad signature:

```dotenv
UKLON_DELIVERY_WEBHOOK_SECRET=the-key-you-registered
```

```php
Route::post('/webhooks/uklon/order',  [UklonWebhookController::class, 'order'])->middleware('uklon-webhook');
Route::post('/webhooks/uklon/driver', [UklonWebhookController::class, 'driver'])->middleware('uklon-webhook');
```

Storing secrets elsewhere (e.g. multi-tenant)? Pass a config key to the
middleware: `->middleware('uklon-webhook:services.uklon.order_secret')`.

Or verify manually with [`WebhookSignatureValidator`](src/Webhook/WebhookSignatureValidator.php):

```php
use Sashalenz\UklonDelivery\Webhook\WebhookSignatureValidator;

public function order(Request $request, WebhookSignatureValidator $validator)
{
    abort_unless(
        $validator->isValidRequest($request, config('uklon-delivery-api.webhook_secret')),
        403,
    );

    $event = OrderUpdateEventData::from($request->all());
    // ...
}
```

## Enums

| Enum | Values |
|---|---|
| `OrderStatus` | `placed`, `waiting_for_processing`, `processing`, `accepted`, `arrived`, `running`, `returning`, `completed`, `suspended`, `canceled` — plus `isCourierAssigned()`, `isFinal()` |
| `CancelReason` | `package_not_fit`, `trunk_occupied`, `plans_changed`, `driver_refused_package`, `driver_low_rating`, `driver_behavior`, `driver_was_late`, `driver_not_arrived`, `driver_confused_address`, `driver_ignore`, `driver_too_far`, `driver_asked`, `another_vehicle` |
| `Product` | `car`, `courier` |
| `DropoffStatus` | `delivering`, `arrived`, `delivered`, `not_delivered`, `return_requested`, `returning`, `returned` |
| `IdleState` | `none`, `free`, `paid` |
| `DisabilityType` | `none`, `deaf`, `hard_hearing` |

## Caching

Any read can be memoised through the configured cache driver:

```php
UklonDelivery::city()->cache(3600)->all();   // TTL in seconds
UklonDelivery::city()->cache()->all();        // remember forever
```

## Error handling

All errors extend a single base exception:

```php
use Sashalenz\UklonDelivery\Exceptions\UklonDeliveryApiUnavailableException;
use Sashalenz\UklonDelivery\Exceptions\UklonDeliveryException;

try {
    $order = UklonDelivery::order()->create($request);
} catch (UklonDeliveryApiUnavailableException $e) {
    // network error / timeout / 5xx — transient, retry later
    report($e);
} catch (UklonDeliveryException $e) {
    // 4xx — bad request / expired fare / auth; message carries the API's
    // "{message} [{subcode}]" when available
    report($e);
}
```

`UklonDeliveryApiUnavailableException` extends `UklonDeliveryException`, so catch
the base type if you don't need to distinguish them.

## Testing

```bash
composer test       # Pest
composer analyse    # PHPStan (level 6)
composer format     # Pint
```

## License

The MIT License (MIT). See [License File](LICENSE.md).
