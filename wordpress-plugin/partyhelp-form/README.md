# Partyhelp Form – WordPress Plugin

Party details form for venue recommendations, synced with get.partyhelp.com.au.

## Installation

1. Upload the `partyhelp-form.zip` (or the `partyhelp-form` folder) to `/wp-content/plugins/`
2. Activate the plugin in WordPress admin
3. Go to **Settings → Partyhelp Form** to configure API URL and webhook URL
4. Click **Sync Now** to fetch areas, occasion types, and other form config
5. Add the shortcode `[partyhelp-form]` to any page or post

## Shortcode

```
[partyhelp-form]
```

## Settings

- **API Base URL** – Where to fetch form config (default: `https://get.partyhelp.com.au/api/partyhelp-form`)
- **Webhook URL** – Where form submissions are sent (default: `https://get.partyhelp.com.au/api/webhook/elementor-lead`)

## Cron (Hourly Sync)

The plugin schedules a cron event `partyhelp_form_cron_sync` on activation. Ensure WordPress cron is running (e.g. via a system cron hitting `wp-cron.php`), or use WP Crontrol to run the event hourly.

## CSS Classes

Each field group and field has unique classes for styling:

- `.partyhelp-personal-info-group` – First name, last name, email, phone
- `.partyhelp-first-name-field`, `.partyhelp-last-name-field`, `.partyhelp-email-field`, `.partyhelp-phone-field`
- `.partyhelp-party-details-group` – Occasion, date, guests, budget
- `.partyhelp-occasion-type-field-group`, `.partyhelp-preferred-date-field`, `.partyhelp-num-guests-field-group`, `.partyhelp-estimated-budget-field`
- `.partyhelp-location-group` – Location checkboxes
- `.partyhelp-location-cbd`, `.partyhelp-location-inner-south`, etc.
- `.partyhelp-other-details-group` – Other details textarea
- `.partyhelp-submit-button` – Submit button

## API Endpoints (Laravel)

- `GET /api/partyhelp-form/config` – All config (areas, occasion types, guest brackets, budget ranges)
- `GET /api/partyhelp-form/areas`
- `GET /api/partyhelp-form/occasion-types`
- `GET /api/partyhelp-form/guest-brackets`
- `GET /api/partyhelp-form/budget-ranges`
