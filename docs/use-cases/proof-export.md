# Proof export

The proofs page includes a bulk export action.

## What it exports

The generated ZIP archive contains:

- `proofs.csv` with a row per proof
- the certificate PDF when the proof exposes a certificate URL
- the proof files downloaded from the API links when available

## Usage

Select one or more proofs in the table, then trigger the export bulk action.

The export is assembled server-side, so the user only receives a single ZIP file.

## Notes

- the export action is suitable for legal archiving and offline review
- proof file downloads depend on the links returned by the API
- the CSV is generated with normalized proof metadata and file counts
