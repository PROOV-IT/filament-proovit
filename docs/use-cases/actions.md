# Reusable actions

`proovit/filament-proovit` exposes Filament-native actions that you can reuse outside the built-in pages and widgets.

## Proof deposit

Use the deposit action when you want to open the proof creation wizard from another page or widget in your application.

```php
use Proovit\FilamentProovit\Support\Filament\Actions\Proofs\DepositProofAction;

public function getHeaderActions(): array
{
    return [
        DepositProofAction::make(),
    ];
}
```

The action:

- reserves a token automatically before proof initialization
- builds the payload through the shared proof builder
- loads categories, folders, templates, and template custom fields dynamically
- uploads files after initialization
- signs the proof when the selected template requires it

## Proof export

Use the export bulk action to download selected proofs in a ZIP archive containing:

- a CSV summary
- proof certificates when available
- proof files when the API exposes downloadable links

```php
use Proovit\FilamentProovit\Support\Filament\Actions\Proofs\ExportProofsAction;

public function table(Table $table): Table
{
    return $table->bulkActions([
        ExportProofsAction::make(),
    ]);
}
```

## Notes

- the actions are Filament-native and can be attached to any page or table
- they rely on `proovit/laravel-proovit` for the actual API calls
- they inherit the currently persisted ProovIT connection automatically
