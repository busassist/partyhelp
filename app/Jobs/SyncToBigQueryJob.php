<?php

namespace App\Jobs;

use App\Mail\BigQuerySyncFailedEmail;
use App\Models\BigQuerySyncLog;
use App\Services\BigQuerySyncService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SyncToBigQueryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;

    public int $timeout = 600;

    public function handle(): void
    {
        $startedAt = now();
        try {
            $service = new BigQuerySyncService();
            $summary = $service->sync();
            BigQuerySyncLog::create([
                'status' => 'success',
                'message' => 'Sync completed',
                'summary' => $summary,
                'started_at' => $startedAt,
                'completed_at' => now(),
            ]);
        } catch (\Throwable $e) {
            Log::error('BigQuery sync failed', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            BigQuerySyncLog::create([
                'status' => 'failed',
                'message' => $e->getMessage(),
                'error_detail' => $e->getTraceAsString(),
                'started_at' => $startedAt,
                'completed_at' => now(),
            ]);
            $adminEmail = config('partyhelp.admin_email', 'admin@partyhelp.com.au');
            Mail::to($adminEmail)->send(new BigQuerySyncFailedEmail(
                $e->getMessage(),
                $e->getTraceAsString(),
                $startedAt
            ));
            throw $e;
        }
    }
}
