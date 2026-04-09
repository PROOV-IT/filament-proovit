# Proof exports page

The plugin ships with a dedicated native Filament page for bulk proof exports.

## What it does

- lists the recent proofs available in the connected ProovIT workspace
- lets you select one or more proofs in the table
- downloads a ZIP archive containing:
  - a `proofs.csv` manifest
  - the certificate PDF when available
  - the downloadable proof files when the API exposes them

## Why it exists

The exports page gives you a dedicated place to build legal archives or offline snapshots without mixing that workflow with the main proofs page.

## Usage

1. Open the `Proof exports` page from the ProovIT navigation group.
2. Select the proofs you want to export.
3. Trigger the bulk export action.

## Notes

- the export runs server-side
- the archive is generated from the current API responses
- proof file downloads depend on the links returned by the ProovIT API
