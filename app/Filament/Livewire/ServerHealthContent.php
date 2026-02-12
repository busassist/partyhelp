<?php

namespace App\Filament\Livewire;

use App\Services\ApiHealthService;
use App\Services\ServerHealthService;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class ServerHealthContent extends Component
{
    protected static bool $isDiscovered = false;

    public function getViewData(): array
    {
        return [
            'cpu' => ServerHealthService::cpuLoad(),
            'disk' => ServerHealthService::diskUsage(),
            'storage' => ServerHealthService::storageFromMedia(),
            'apis' => ServerHealthService::apiStatus(),
            'scheduledTasks' => ServerHealthService::scheduledTasksStatus(),
            'api_errors' => ApiHealthService::recentErrors(20),
            'api_has_errors' => ApiHealthService::hasRecentErrors(),
        ];
    }

    public function render(): View
    {
        return view('filament.livewire.server-health-content', $this->getViewData());
    }
}
