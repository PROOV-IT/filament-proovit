# proovit/filament-proovit

Filament 5 panel plugin for ProovIT proof supervision and certification workflows.

## What it does

- adds a Filament-native overview of ProovIT integration status
- exposes ready-to-use widgets and pages
- includes native widgets for token balance, proof deposit, and recent proofs with view actions
- ships with a minimal connection settings page for endpoint selection, credentials, test connection, and company binding
- includes a one-click account creation shortcut that follows the current release branch
- keeps ProovIT-specific workflows out of custom ad-hoc Blade views when possible

## Requirements

- PHP 8.3+
- Laravel 13+
- Filament 5+
- `proovit/laravel-proovit`

## Install

```bash
composer require proovit/filament-proovit
```

## Documentation

- [Install](docs/install.md)
- [Configuration](docs/configuration.md)
- [Use cases](docs/use-cases/)
- [Settings page](docs/use-cases/settings-page.md)
- [Proof deposit](docs/use-cases/proof-deposit.md)
- [Proofs page](docs/use-cases/proofs-page.md)
- [Release notes](docs/release-notes.md)
- [V1 scope](docs/use-cases/v1-scope.md)

## Acknowledgements

- [Laravel](https://laravel.com)
- [Filament](https://filamentphp.com)
- [Spatie Laravel Package Tools](https://github.com/spatie/laravel-package-tools)
