# Settings page

The ProovIT Filament plugin ships with a dedicated configuration page.

## Purpose

- choose the ProovIT API endpoint
- enter your login email and password
- test the connection against the live API
- load the list of companies returned by ProovIT
- select the company UUID that will be used for subsequent requests
- persist the connection data in the encrypted settings store

## What it edits

- API base URL
- login email
- login password
- bearer token
- selected company UUID
- selected company display name
- company list returned by the API

## Connection flow

The page exposes a `Test connection` action.
It reads the current form state, then the plugin:

1. builds a temporary SDK configuration from the current endpoint and stored values
2. logs into the ProovIT API using the provided email and password
3. fetches the available companies with the returned bearer token
4. stores the endpoint, login, password, bearer token, and company list in the persistent settings store
5. keeps the selected company as-is when it is still valid, otherwise waits for the user to choose a company

## Persistence rules

- the page stores its session in the encrypted settings table shipped by `laravel-proovit`
- saved values override the matching SDK config entries
- the canonical persisted key is `connection.selected_company_uuid`
- `connection.workspace_token` is still written for backward compatibility
- the saved settings are synchronized back into the page state after a successful test or save, without clearing the form
- after a successful connection test, the page emits a refresh so the companies list updates immediately

## Recommended flow

1. choose the API endpoint
2. enter your credentials
3. test the connection
4. select the company UUID
5. save the settings

## Notes

- the package intentionally keeps the UI compact
- the connection test does not require a modal
- the dropdown of companies only becomes useful after a successful authentication
