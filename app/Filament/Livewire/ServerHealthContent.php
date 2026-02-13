<?php

namespace App\Filament\Livewire;

use App\Services\ApiHealthService;
use App\Services\ServerHealthService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class ServerHealthContent extends Component
{
    protected static bool $isDiscovered = false;

    public function getViewData(): array
    {
        try {
            return [
                'cpu' => ServerHealthService::cpuLoad(),
                'disk' => ServerHealthService::diskUsage(),
                'storage' => ServerHealthService::storageFromMedia(),
                'apis' => ServerHealthService::apiStatus(),
                'scheduledTasks' => ServerHealthService::scheduledTasksStatus(),
                'bigquerySync' => ServerHealthService::bigquerySyncStatus(),
                'api_errors' => ApiHealthService::recentErrors(20),
                'api_has_errors' => ApiHealthService::hasRecentErrors(),
            ];
        } catch (\Throwable $e) {
            Log::error('ServerHealthContent getViewData failed', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return $this->defaultViewData();
        }
    }

    /** Safe defaults when health checks fail so the Settings page still loads. */
    private function defaultViewData(): array
    {
        return [
            'cpu' => ['load_1' => null, 'load_5' => null, 'load_15' => null, 'available' => false],
            'disk' => [
                'path' => base_path(),
                'used_human' => '—',
                'free_human' => '—',
                'total_human' => '—',
                'used_percent' => 0,
            ],
            'storage' => ['total_bytes' => 0, 'total_human' => '0 B', 'count' => 0, 'by_venue' => []],
            'apis' => [],
            'scheduledTasks' => [
                'last_runs' => [],
                'recent_failures' => [],
                'required_total' => 0,
                'required_ok' => 0,
                'required_failed' => 0,
                'has_failures' => false,
            ],
            'bigquerySync' => ['configured' => false, 'last_run' => null],
            'api_errors' => [],
            'api_has_errors' => false,
        ];
    }

    public function render(): View
    {
        return view('filament.livewire.server-health-content', $this->getViewData());
    }
}
