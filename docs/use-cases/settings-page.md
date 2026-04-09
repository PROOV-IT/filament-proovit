# Settings page

The ProovIT Filament plugin ships with a dedicated configuration page.

## Purpose

- authenticate against ProovIT with email and password
- load the list of companies returned by the API
- select the company UUID that will be used for subsequent requests
- persist the session in the package settings store
- keep the browser UI small and focused on the active connection

## What it edits

- API base URL
- login email
- selected company UUID
- selected company display name
- authenticated bearer token and company list stored behind the scenes

## Authentication flow

The page exposes an `Authenticate` action.
It asks for the password only, then the plugin:

1. logs into the ProovIT API
2. fetches the available companies
3. stores the bearer token and company list in the persistent settings store
4. clears the previous selected company so the user can choose the new target company

## Behavior

- the page stores its session in the encrypted settings table shipped by `laravel-proovit`
- saved values override the matching SDK config entries
- if no company is selected yet, the page keeps the session but waits for the user to choose one
- the canonical persisted key is `connection.selected_company_uuid`; `connection.workspace_token` is still written for backward compatibility
