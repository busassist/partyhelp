<?php

namespace App\Services;

use Google\Cloud\BigQuery\BigQueryClient;
use Google\Cloud\BigQuery\Table;
use Illuminate\Support\Facades\Log;

class BigQuerySyncService
{
    private BigQueryClient $client;

    private string $dataset;

    private string $projectId;

    public function __construct()
    {
        $path = config('bigquery.credentials_path');
        $absolutePath = $path && is_file($path) ? realpath($path) : $path;
        if ($absolutePath) {
            putenv('GOOGLE_APPLICATION_CREDENTIALS=' . $absolutePath);
        }

        $this->projectId = config('bigquery.project_id');
        $this->dataset = config('bigquery.dataset');
        $this->client = new BigQueryClient([
            'projectId' => $this->projectId,
            'keyFilePath' => $absolutePath,
        ]);
    }

    /**
     * Full sync: build flattened data and load into BigQuery (WRITE_TRUNCATE per table).
     *
     * @return array{table: string, rows: int}[] Summary per table
     * @throws \Throwable
     */
    public function sync(): array
    {
        $builder = new BigQuerySyncDataBuilder();
        $summary = [];

        $tables = [
            'ph_leads' => fn () => $builder->phLeads(),
            'ph_venues' => fn () => $builder->phVenues(),
            'ph_lead_purchases' => fn () => $builder->phLeadPurchases(),
            'ph_credit_transactions' => fn () => $builder->phCreditTransactions(),
            'ph_lead_matches' => fn () => $builder->phLeadMatches(),
        ];

        foreach ($tables as $tableName => $rowBuilder) {
            $rows = $rowBuilder();
            $count = count($rows);
            $this->loadTable($tableName, $rows);
            $summary[$tableName] = $count;
            Log::info("BigQuery sync: {$tableName} loaded {$count} rows");
        }

        return $summary;
    }

    /**
     * @param array<int, array<string, mixed>> $rows
     *
     * @throws \Exception
     */
    private function loadTable(string $tableName, array $rows): void
    {
        if (count($rows) === 0) {
            return;
        }

        $dataset = $this->client->dataset($this->dataset);
        $table = $dataset->table($tableName);

        $ndjson = implode("\n", array_map(static fn (array $row) => json_encode($row), $rows));

        $stream = fopen('php://temp', 'r+');
        if ($stream === false) {
            throw new \RuntimeException('Failed to open temp stream');
        }
        fwrite($stream, $ndjson);
        rewind($stream);

        $options = [
            'configuration' => [
                'load' => [
                    'sourceFormat' => 'NEWLINE_DELIMITED_JSON',
                    'writeDisposition' => 'WRITE_TRUNCATE',
                    'autodetect' => true,
                ],
            ],
        ];

        $loadConfig = $table->load($stream, $options);
        $job = $this->client->runJob($loadConfig);
        $job->waitUntilComplete();

        if (is_resource($stream)) {
            fclose($stream);
        }
    }
}
