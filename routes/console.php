<?php

use App\Jobs\ExpireLeads;
use App\Jobs\ProcessAutoTopUps;
use App\Jobs\ProcessLeadDiscounts;
use App\Jobs\SyncToBigQueryJob;
use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Scheduled Tasks
|--------------------------------------------------------------------------
*/

// Discount escalation and lead expiry
Schedule::job(new ProcessLeadDiscounts)->hourly();
Schedule::job(new ExpireLeads)->hourly();

// Credit auto top-ups
Schedule::job(new ProcessAutoTopUps)->everyFiveMinutes();

// Process queue (lead matching, notifications): run every 5 minutes, max 4 min per run
Schedule::command('queue:process', ['--max-time' => 240])->everyFiveMinutes();

// BigQuery: daily sync of platform data for reporting (docs/BIGQUERY_SYNC_PROPOSAL.md)
Schedule::job(new SyncToBigQueryJob)->daily();
