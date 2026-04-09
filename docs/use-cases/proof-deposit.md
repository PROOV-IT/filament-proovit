# Proof deposit

The plugin ships with a native proof deposit widget on the dashboard and a matching create action on the proofs page.
Both open the same Filament wizard modal and build the payload through the shared `proovit/laravel-proovit` proof builder.

## What it does

- lets the user pick a proof template from the connected ProovIT workspace
- caches the proof template catalog for 10 minutes per connected company
- generates custom fields dynamically from the selected template schema
- shows or hides template-sensitive fields such as folders, categories, share emails, tags, and signature
- hides folder and category selects when the API returns no usable options
- reserves a token automatically before the proof initialization request
- submits the proof initialization request through the SDK
- uploads files and optional signature payloads after initialization
- can be reused from any Filament page by calling `DepositProofAction::make()`

## Template-driven form

The modal reads the current template schema from:

- `GET /api/v1/proof-templates?per_page=100`

The following schema flags are used to shape the form:

- `displayFolders`
- `displayCategories`
- `displayTags`
- `shared`
- `signature`
- `customFields`
- `requiredFiles`

The widget also consumes the following API collections when they are available:

- `GET /api/v1/categories?per_page=200&include_shared=true`
- `GET /api/v1/folders?per_page=200&accessible_by=write`

When either collection is empty, the related select is hidden.

## Custom fields

Custom fields are generated from the template `customFields` array.
Supported field types include:

- `text`
- `textarea`
- `number`
- `date`
- `select`
- `radio`
- `toggle`
- `checkbox`
- `email`
- `url`
- `tel`

## Caching

Template catalogs are cached for 10 minutes using Laravel cache.
The cache key is scoped to the currently connected company and endpoint so separate companies do not share stale template data.

## Example flow

1. open the dashboard proof deposit widget
2. select a proof template
3. wait for the token reservation to be created automatically
4. fill in the template-specific fields
5. upload the required files
6. optionally paste a base64 signature when the template requires one
7. submit the proof

## Notes

- the dashboard remains Filament-native
- the action is designed for proof initialization, not for advanced post-processing
- if the template does not require a signature, the signature field stays hidden
