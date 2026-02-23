<?php

use App\Jobs\ExpireLeads;
use App\Jobs\ProcessAdditionalServicesEmails;
use App\Jobs\ProcessAutoTopUps;
use App\Jobs\ProcessLeadDiscounts;
use App\Jobs\SyncToBigQueryJob;
use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Scheduled Tasks
|--------------------------------------------------------------------------
*/

// MySQL backup to DO Spaces (hourly; weekly/monthly when due). See Server Health for last backup timestamps.
Schedule::command('backup:mysql')->hourly();

// Discount escalation and lead expiry
Schedule::job(new ProcessLeadDiscounts)->hourly();
Schedule::job(new ExpireLeads)->hourly();

// Additional services email: send to customers X hours after lead (configurable in Settings)
Schedule::job(new ProcessAdditionalServicesEmails)->hourly();

// Credit auto top-ups
Schedule::job(new ProcessAutoTopUps)->everyFiveMinutes();

// Process queue (lead matching, notifications): run every 5 minutes, max 4 min per run
Schedule::command('queue:process', ['--max-time' => 240])->everyFiveMinutes();

// BigQuery: daily sync of platform data for reporting (docs/BIGQUERY_SYNC_PROPOSAL.md)
Schedule::job(new SyncToBigQueryJob)->daily();
