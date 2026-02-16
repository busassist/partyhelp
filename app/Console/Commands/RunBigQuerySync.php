<?php

namespace App\Console\Commands;

use App\Models\BigQuerySyncLog;
use App\Services\BigQuerySyncService;
use Illuminate\Console\Command;

class RunBigQuerySync extends Command
{
    protected $signature = 'bigquery:sync
                            {--no-log : Do not write to bigquery_sync_logs}';

    protected $description = 'Run the BigQuery data sync once (trial or manual run). Same as the daily scheduled job.';

    public function handle(): int
    {
        $credentialsPath = config('bigquery.credentials_path');
        $projectId = config('bigquery.project_id');
        $dataset = config('bigquery.dataset');

        if (empty($projectId) || empty($dataset) || ! $credentialsPath || ! is_file($credentialsPath)) {
            $this->error('BigQuery is not configured. Set BIGQUERY_PROJECT_ID, BIGQUERY_DATASET and BIGQUERY_CREDENTIALS_PATH in .env and ensure the credentials JSON file exists.');
            $this->line('See docs/BIGQUERY_SETUP.md for setup steps.');

            return self::FAILURE;
        }

        $this->info('Starting BigQuery sync…');
        $startedAt = now();

        try {
            $service = new BigQuerySyncService();
            $summary = $service->sync();
        } catch (\Throwable $e) {
            $this->error('Sync failed: ' . $e->getMessage());

            if (! $this->option('no-log')) {
                BigQuerySyncLog::create([
                    'status' => 'failed',
                    'message' => $e->getMessage(),
                    'error_detail' => $e->getTraceAsString(),
                    'started_at' => $startedAt,
                    'completed_at' => now(),
                ]);
            }

            return self::FAILURE;
        }

        if (! $this->option('no-log')) {
            BigQuerySyncLog::create([
                'status' => 'success',
                'message' => 'Sync completed',
                'summary' => $summary,
                'started_at' => $startedAt,
                'completed_at' => now(),
            ]);
        }

        $this->info('Sync completed successfully.');
        $this->table(['Table', 'Rows'], collect($summary)->map(fn ($rows, $table) => [$table, $rows])->values()->all());

        return self::SUCCESS;
    }
}
