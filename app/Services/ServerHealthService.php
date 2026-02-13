<?php

namespace App\Services;

use App\Models\BigQuerySyncLog;
use App\Models\Media;
use App\Models\ScheduleRunLog;
use App\Models\Venue;

class ServerHealthService
{
    /** Expected scheduled task display names (from schedule). Used to highlight required tasks. */
    private const REQUIRED_SCHEDULED_TASKS = [
        'App\Jobs\ProcessLeadDiscounts',
        'App\Jobs\ExpireLeads',
        'App\Jobs\ProcessAutoTopUps',
        'App\Jobs\SyncToBigQueryJob',
        'Process the queue (stop when empty) with a lock so only one processor runs at a time.',
    ];
    /**
     * @return array{load_1: float|null, load_5: float|null, load_15: float|null, available: bool}
     */
    public static function cpuLoad(): array
    {
        if (! function_exists('sys_getloadavg')) {
            return ['load_1' => null, 'load_5' => null, 'load_15' => null, 'available' => false];
        }

        $load = @sys_getloadavg();

        return [
            'load_1' => isset($load[0]) ? round($load[0], 2) : null,
            'load_5' => isset($load[1]) ? round($load[1], 2) : null,
            'load_15' => isset($load[2]) ? round($load[2], 2) : null,
            'available' => $load !== false,
        ];
    }

    /**
     * @return array{path: string, total_bytes: int, free_bytes: int, used_bytes: int, total_human: string, free_human: string, used_human: string, used_percent: float}
     */
    public static function diskUsage(string $path = null): array
    {
        $path = $path ?? base_path();
        $total = @disk_total_space($path) ?: 0;
        $free = @disk_free_space($path) ?: 0;
        $used = $total - $free;
        $usedPercent = $total > 0 ? round(($used / $total) * 100, 1) : 0.0;

        return [
            'path' => $path,
            'total_bytes' => $total,
            'free_bytes' => $free,
            'used_bytes' => $used,
            'total_human' => self::bytesToHuman($total),
            'free_human' => self::bytesToHuman($free),
            'used_human' => self::bytesToHuman($used),
            'used_percent' => $usedPercent,
        ];
    }

    /**
     * Storage (Spaces/S3) from Media records: total and by venue (contact_email).
     *
     * @return array{total_bytes: int, total_human: string, count: int, by_venue: array<int, array{venue_id: int, email: string, business_name: string, bytes: int, human: string, count: int}>}
     */
    public static function storageFromMedia(): array
    {
        $total = (int) Media::query()->sum('size');
        $count = Media::query()->count();

        $byVenue = Media::query()
            ->selectRaw('venue_id, SUM(size) as total_size, COUNT(*) as file_count')
            ->groupBy('venue_id')
            ->get();

        $venueIds = $byVenue->pluck('venue_id')->filter()->unique()->values()->all();
        $venues = Venue::query()
            ->whereIn('id', $venueIds)
            ->get()
            ->keyBy('id');

        $byVenueList = $byVenue->map(function ($row) use ($venues) {
            $venue = $row->venue_id ? $venues->get($row->venue_id) : null;
            $bytes = (int) $row->total_size;

            return [
                'venue_id' => $row->venue_id,
                'email' => $venue?->contact_email ?? '—',
                'business_name' => $venue?->business_name ?? 'Unknown',
                'bytes' => $bytes,
                'human' => self::bytesToHuman($bytes),
                'count' => (int) $row->file_count,
            ];
        })->sortByDesc('bytes')->values()->all();

        return [
            'total_bytes' => $total,
            'total_human' => self::bytesToHuman($total),
            'count' => $count,
            'by_venue' => $byVenueList,
        ];
    }

    /**
     * @return array<string, array{configured: bool, label: string, detail?: string}>
     */
    public static function apiStatus(): array
    {
        $mailgunDomain = config('services.mailgun.domain');
        $mailgunSecret = config('services.mailgun.secret');
        $stripeSecret = config('services.stripe.secret');
        $spacesKey = config('filesystems.disks.spaces.key');
        $spacesSecret = config('filesystems.disks.spaces.secret');
        $spacesBucket = config('filesystems.disks.spaces.bucket');

        return [
            'mailgun' => [
                'configured' => ! empty($mailgunDomain) && ! empty($mailgunSecret),
                'label' => 'Mailgun',
                'detail' => 'Email sending API',
            ],
            'stripe' => [
                'configured' => ! empty($stripeSecret),
                'label' => 'Stripe',
                'detail' => (config('services.stripe.mode') ?? 'unknown') . ' mode',
            ],
            'spaces' => [
                'configured' => ! empty($spacesKey) && ! empty($spacesSecret) && ! empty($spacesBucket),
                'label' => 'DigitalOcean Spaces',
                'detail' => $spacesBucket ? 'Bucket: ' . $spacesBucket : null,
            ],
        ];
    }

    /**
     * Scheduled tasks: last run per task, recent failures, and required-task status.
     *
     * @return array{last_runs: array<int, array{task_display_name: string, status: string, message: string|null, ran_at: string, is_required: bool}>, recent_failures: array<int, array{task_display_name: string, message: string|null, ran_at: string}>, required_task_names: array<int, string>}
     */
    public static function scheduledTasksStatus(): array
    {
        $lastRunPerTask = ScheduleRunLog::query()
            ->orderByDesc('ran_at')
            ->get()
            ->unique('task_display_name')
            ->values()
            ->map(fn (ScheduleRunLog $log) => [
                'task_display_name' => $log->task_display_name,
                'status' => $log->status,
                'message' => $log->message,
                'ran_at' => $log->ran_at->format('Y-m-d H:i:s'),
                'is_required' => in_array($log->task_display_name, self::REQUIRED_SCHEDULED_TASKS, true),
            ])
            ->all();

        $recentFailures = ScheduleRunLog::query()
            ->where('status', 'failed')
            ->orderByDesc('ran_at')
            ->limit(5)
            ->get()
            ->map(fn (ScheduleRunLog $log) => [
                'task_display_name' => $log->task_display_name,
                'message' => $log->message,
                'ran_at' => $log->ran_at->format('Y-m-d H:i:s'),
            ])
            ->all();

        $requiredCount = count(self::REQUIRED_SCHEDULED_TASKS);
        $requiredWithSuccess = collect($lastRunPerTask)
            ->filter(fn (array $r) => $r['is_required'] && $r['status'] === 'finished')
            ->count();
        $requiredWithFailure = collect($lastRunPerTask)
            ->filter(fn (array $r) => $r['is_required'] && $r['status'] === 'failed')
            ->count();

        return [
            'last_runs' => $lastRunPerTask,
            'recent_failures' => $recentFailures,
            'required_task_names' => self::REQUIRED_SCHEDULED_TASKS,
            'has_failures' => count($recentFailures) > 0 || $requiredWithFailure > 0,
            'required_ok' => $requiredWithSuccess,
            'required_total' => $requiredCount,
            'required_failed' => $requiredWithFailure,
        ];
    }

    /**
     * Last BigQuery sync run (from bigquery_sync_logs).
     *
     * @return array{last_run: array{status: string, message: string|null, error_detail: string|null, started_at: string, completed_at: string|null, summary: array|null}|null, configured: bool}
     */
    public static function bigquerySyncStatus(): array
    {
        $log = BigQuerySyncLog::latestRun();
        $credentialsPath = config('bigquery.credentials_path');
        $configured = ! empty(config('bigquery.project_id')) && ! empty(config('bigquery.dataset')) && $credentialsPath && is_file($credentialsPath);

        return [
            'last_run' => $log ? [
                'status' => $log->status,
                'message' => $log->message,
                'error_detail' => $log->error_detail,
                'started_at' => $log->started_at->format('Y-m-d H:i:s'),
                'completed_at' => $log->completed_at?->format('Y-m-d H:i:s'),
                'summary' => $log->summary,
            ] : null,
            'configured' => $configured,
        ];
    }

    public static function bytesToHuman(int $bytes): string
    {
        if ($bytes < 1024) {
            return $bytes . ' B';
        }
        if ($bytes < 1024 * 1024) {
            return round($bytes / 1024, 1) . ' KB';
        }
        if ($bytes < 1024 * 1024 * 1024) {
            return round($bytes / (1024 * 1024), 1) . ' MB';
        }

        return round($bytes / (1024 * 1024 * 1024), 2) . ' GB';
    }
}
