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

## Troubleshooting

**Plugin won’t activate or shows two versions (e.g. 1.0.0 and 1.1.0)**  
- The plugin requires **PHP 7.4+**. If activation does nothing or the plugin “refuses to activate”, a PHP error is likely.  
- Turn on logging: in `wp-config.php` set `define('WP_DEBUG', true);` and `define('WP_DEBUG_LOG', true);`. Try activating again, then check `wp-content/debug.log` for the exact error.  
- **Two entries in the plugin list:** Delete the old plugin (Deactivate → Delete), then install only the latest zip (e.g. 1.1.1) via **Plugins → Add New → Upload**. Don’t keep two copies (e.g. an old folder and a new zip) at once.  
- **PHP version:** In WordPress go to **Tools → Site Health → Info → Server** and check “PHP version”. If it’s 7.4, use plugin version 1.1.1 or later (PHP 7.4 compatible).

## API Endpoints (Laravel)

- `GET /api/partyhelp-form/config` – All config (areas, occasion types, guest brackets, budget ranges, venue styles)
- `GET /api/partyhelp-form/areas`
- `GET /api/partyhelp-form/occasion-types`
- `GET /api/partyhelp-form/guest-brackets`
- `GET /api/partyhelp-form/budget-ranges`
- `GET /api/partyhelp-form/venue-styles`
