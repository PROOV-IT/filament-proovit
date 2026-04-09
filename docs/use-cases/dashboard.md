# Dashboard

The ProovIT overview is rendered through the native Filament dashboard.
It is built from widgets only, without a custom Blade page.

## Widgets

- proof deposit action widget
- token balance stats
- connection status stats
- recent proofs table with a dedicated view action

## Notes

- the dashboard stays Filament-native and does not rely on custom Blade layout code
- the proof deposit widget opens a modal action for initializing a new proof without leaving the dashboard
- the token balance widget reads the live API balance for the selected company
