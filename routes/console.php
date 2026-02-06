<?php

use App\Jobs\ExpireLeads;
use App\Jobs\ProcessAutoTopUps;
use App\Jobs\ProcessLeadDiscounts;
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
