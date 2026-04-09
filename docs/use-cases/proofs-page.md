# Proofs page

The `ProovIT` plugin now ships with a native Filament page dedicated to proofs.

## What it shows

- recent proofs fetched from the connected ProovIT workspace
- proof status, signed timestamp, description, and certificate link
- quick actions to open a certificate or revoke a proof when allowed by your workflow

## Navigation

By default the page is registered as:

- label: `Proofs`
- slug: `/admin/proovit/proofs`
- navigation group: `ProovIT`

## Data source

The page reads the proof list directly from the `proovit/laravel-proovit` SDK.
That means the page automatically follows the persistent ProovIT settings stored in the database when they exist, and falls back to the package configuration when they do not.

## Example usage

The page is registered by the plugin automatically:

```php
use Proovit\FilamentProovit\FilamentProovitPlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        ->plugin(FilamentProovitPlugin::make());
}
```

## Notes

- The page is intentionally lightweight and Filament-native.
- The current release focuses on browsing proofs and certificate actions.
- More workflow-specific screens can be added later without changing the underlying SDK contract.
