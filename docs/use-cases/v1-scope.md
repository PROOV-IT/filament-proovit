# ProovIT v1 scope

Version 1 of `proovit/filament-proovit` is intentionally small.
It provides a native Filament shell around the SDK so operators can inspect the current
integration status without duplicating business logic.

## Included in v1

- native Filament dashboard widgets
- connection status stats widget
- recent proofs table widget
- panel navigation and registration

## Deferred to later versions

- proof CRUD screens
- advanced export management
- certificate audit views
- custom action-heavy flows

## Filament example

```php
use Filament\Panel;
use Proovit\FilamentProovit\FilamentProovitPlugin;

public function panel(Panel $panel): Panel
{
    return $panel->plugins([
        FilamentProovitPlugin::make(),
    ]);
}
```
