# BigQuery daily sync – setup guide

The app syncs platform data to BigQuery once per day for reporting. This doc covers configuration and a one-off trial run.

**See also:** [BIGQUERY_SYNC_PROPOSAL.md](BIGQUERY_SYNC_PROPOSAL.md) for table designs and sync strategy.

---

## 1. Google Cloud setup

1. **Create or select a GCP project** (e.g. `partyhelp-bigquery`).
2. **Enable the BigQuery API** for that project: APIs & Services → Enable APIs → BigQuery API.
3. **Create a BigQuery dataset** in the project, e.g. `partyhelp_reporting` (same region as your app if possible).
4. **Create a service account** for the sync:
   - IAM & Admin → Service Accounts → Create.
   - Grant roles: **BigQuery Data Editor** and **BigQuery Job User** (so it can run load jobs and write data).
5. **Create a JSON key** for the service account and download it. Keep it secure.

---

## 2. Server configuration

1. **Upload the JSON key** to the server. Recommended path: `storage/app/bigquery-credentials.json` (ensure the file is not under public web root and not committed to git).
2. **Set environment variables** in `.env`:

   ```env
   BIGQUERY_CREDENTIALS_PATH=storage/app/bigquery-credentials.json
   BIGQUERY_PROJECT_ID=your-gcp-project-id
   BIGQUERY_DATASET=partyhelp_reporting
   ```

   `BIGQUERY_CREDENTIALS_PATH` can be relative to the project root or an absolute path.

3. **Run migrations** so `bigquery_sync_logs` exists (if not already):

   ```bash
   php artisan migrate
   ```

4. **Clear config cache** after changing `.env`:

   ```bash
   php artisan config:clear
   ```

---

## 3. Trial run (one-off sync)

To run the sync once and see output (without waiting for the daily schedule):

```bash
php artisan bigquery:sync
```

- If configuration is missing, the command prints what to set and exits.
- On success it writes a row to `bigquery_sync_logs` and prints a table summary.
- Use `--no-log` to run without writing to `bigquery_sync_logs`.

Server Health (Admin → Settings → Server Health tab) shows “BigQuery sync (daily)” as **Configured** when project, dataset, and credentials file are present, and shows the last run from `bigquery_sync_logs`.

---

## 4. Daily scheduled run and cron

The sync is scheduled to run **daily** via Laravel’s scheduler (`Schedule::job(new SyncToBigQueryJob)->daily()` in `routes/console.php`). For that to run:

1. **Cron must call the scheduler every minute.** On Laravel Forge, add a cron entry (or use the default “Run schedule”):
   - Command: `php artisan schedule:run`
   - Frequency: every minute (`* * * * *`).
   - Forge usually sets the working directory to the site path; if not, use: `cd /home/forge/get.partyhelp.com.au && php artisan schedule:run`.

2. **Queue worker** must be running so the dispatched `SyncToBigQueryJob` is processed (Forge “Queue” or a supervisor-managed `queue:work`).

Once cron is running, **Scheduled tasks** on the Server Health page will start showing runs (including the daily BigQuery job when it runs). Those entries come from `schedule_run_logs`, populated when Laravel runs scheduled tasks.

---

## 5. Failure alerts

If the daily sync job fails, the app sends an email to the address in `config('partyhelp.admin_email')` (default `admin@partyhelp.com.au`) with the error. You can also check **Server Health → API health & recent errors** and the **BigQuery sync (daily)** panel for the last run status and message.
