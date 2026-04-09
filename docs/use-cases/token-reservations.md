# Token reservations

The plugin stores a local reservation history whenever the proof deposit flow reserves a token.

## Why it exists

- help operators verify that token reservations are created before proof initialization
- make failed or repeated reservation attempts visible in the panel
- provide a native Filament page for quick inspection of the latest reservations

## What is stored

- reservation fingerprint
- reservation ID returned by the API
- reservation status
- raw API response
- local created timestamp

## Navigation

By default the page is registered as:

- label: `Token reservations`
- slug: `/admin/proovit/token-reservations`
- navigation group: `ProovIT`

## How it is populated

The plugin listens to the `TokenReserved` event dispatched by `proovit/laravel-proovit`.
When the SDK reserves a token, the plugin persists the result locally and exposes it in the table.

## Notes

- this is a local audit trail, not a remote ProovIT API endpoint
- the page is useful even when the proof itself later fails
- the data stays within your Laravel application
