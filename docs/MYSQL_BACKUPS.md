# MySQL backups to DigitalOcean Spaces

Hourly MySQL dumps are uploaded to the DO Spaces bucket under a `backups/` folder. The Laravel scheduler runs the backup every hour.

## What runs

- **Command:** `php artisan backup:mysql`
- **Schedule:** hourly (via `Schedule::command('backup:mysql')->hourly()` in `routes/console.php`)
- **Cron:** Ensure the Laravel scheduler runs every minute (e.g. `* * * * * cd /path && php artisan schedule:run`). On Laravel Forge this is usually already configured.

## Retention

| Type    | Folder            | When created        | Kept |
|---------|-------------------|----------------------|------|
| Hourly  | `backups/hourly/` | Every hour           | 24   |
| Weekly  | `backups/weekly/` | Sunday 00:00         | 4    |
| Monthly | `backups/monthly/`| 1st of month 00:00  | 12   |

The oldest file in each folder is deleted when the count exceeds the limit.

## File names

- Hourly: `hourly_DDMMYYYY-HHmmss.sql.gz` (e.g. `hourly_07022026-140000.sql.gz`)
- Weekly: `weekly_DDMMYYYY-HHmmss.sql.gz`
- Monthly: `monthly_DDMMYYYY-HHmmss.sql.gz`

Content is gzipped SQL from `mysqldump` (single transaction, no lock tables).

## Requirements

- **mysqldump** and **gzip** available on the server (typical on Forge/DO).
- **DO Spaces** configured in `.env`: `DO_SPACES_KEY`, `DO_SPACES_SECRET`, `DO_SPACES_BUCKET`, `DO_SPACES_ENDPOINT`, `DO_SPACES_REGION`.
- Same bucket as media is used; backups go under the `backups/` prefix. Ensure the Spaces key has write access to the bucket.

## Server Health page

Admin → Settings → Server Health tab shows **MySQL backups (DO Spaces)** with the timestamp of the most recent hourly, weekly, and monthly backup. The scheduled task **backup:mysql** appears in the Scheduled tasks list.

## Manual run

```bash
php artisan backup:mysql
```

## Restore (manual)

Download a `.sql.gz` from Spaces, then:

```bash
gunzip -c backup.sql.gz | mysql -u USER -p DATABASE
```
