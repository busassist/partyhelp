# Partyhelp Form – WordPress Plugin

## Overview

The Partyhelp Form plugin renders a party details form on your WordPress marketing site, synced with get.partyhelp.com.au for areas, occasion types, guest brackets, and budget ranges. Form submissions are sent to the Laravel webhook.

## Installation

1. Copy `wordpress-plugin/partyhelp-form.zip` to your WordPress site
2. **Plugins → Add New → Upload Plugin** → choose the zip
3. Activate the plugin
4. **Settings → Partyhelp Form** – configure URLs and click **Sync Now**

## Shortcode

Add `[partyhelp-form]` to any page or post.

## Plugin Location

- **Codebase:** `wordpress-plugin/partyhelp-form/`
- **Zip:** Run `wordpress-plugin/build-zip.sh` to generate `wordpress-plugin/partyhelp-form.zip`

## Laravel API Endpoints

| Endpoint | Response |
|----------|----------|
| `GET /api/partyhelp-form/config` | All config (areas, occasion types, guest brackets, budget ranges) |
| `GET /api/partyhelp-form/areas` | Area definitions for location checkboxes |
| `GET /api/partyhelp-form/occasion-types` | Occasion type dropdown options |
| `GET /api/partyhelp-form/guest-brackets` | Guest count bracket options |
| `GET /api/partyhelp-form/budget-ranges` | Budget range options |

## Webhook

Form submissions are proxied via WordPress (to avoid CORS) to:

`POST /api/webhook/elementor-lead`

Payload format matches the existing Elementor webhook (see `WebhookController`).

## Cron (Hourly Sync)

The plugin schedules `partyhelp_form_cron_sync` on activation. Ensure [WordPress cron](https://developer.wordpress.org/plugins/cron/) runs (e.g. via system cron or WP Crontrol).
