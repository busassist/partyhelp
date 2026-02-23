# Timezone (Melbourne, Australia)

Partyhelp is configured to use **Australia/Melbourne** for all application dates and times.

## Application timezone (Laravel)

- **Config:** `config/app.php` → `'timezone' => env('APP_TIMEZONE', 'Australia/Melbourne')`
- **Optional .env:** `APP_TIMEZONE=Australia/Melbourne`

This affects:

- Server Health page (scheduled task run times, backup timestamps, BigQuery sync, etc.)
- Log timestamps in `storage/logs/laravel.log`
- All `now()`, `Carbon::now()`, and `->format()` output in the app
- Filament and Livewire UI dates/times

No restart is needed after changing `.env`; the next request or artisan command uses the new value.

## System timezone (server)

For consistency (cron logs, backup filenames, MySQL `NOW()`, etc.) you can set the server’s timezone to Melbourne:

```bash
sudo timedatectl set-timezone Australia/Melbourne
```

Check:

```bash
timedatectl
# Time zone: Australia/Melbourne (AEDT, +1100)
```

On Laravel Forge you can also set the server timezone in the server’s “Meta” or run the command via SSH.
