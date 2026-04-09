# Configuration

`config/proovit-filament.php` controls the plugin navigation label, widgets and documentation publishing.

The ProovIT settings page uses the persisted configuration from `laravel-proovit` and therefore inherits the same precedence rules:

1. persisted settings
2. package config
3. `.env` defaults

## Navigation

- `navigation.enabled`
- `navigation.label`
- `navigation.group`
- `navigation.icon`
- `navigation.sort`

## Widgets

- `widgets.enabled`
- `widgets.refresh_interval`

## Documentation

- `docs.enabled`
- `docs.path`
