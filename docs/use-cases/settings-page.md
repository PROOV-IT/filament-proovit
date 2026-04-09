# Settings page

The ProovIT Filament plugin ships with a dedicated configuration page.

## Purpose

- edit the ProovIT connection profile
- keep company, login and endpoint values in one place
- persist settings in the database instead of relying only on `.env`
- allow the SDK to use saved values on the next request

## What it edits

- company name
- login email
- base URL
- app URL
- API key
- access token
- workspace token
- mode
- retries and timeouts
- feature flags
- certificates, exports, audit and docs defaults

## Behavior

- the page saves an encrypted payload into the package settings table
- saved values override the matching SDK config entries
- empty optional fields are ignored so the environment can keep acting as fallback
