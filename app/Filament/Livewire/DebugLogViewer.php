<?php

namespace App\Filament\Livewire;

use App\Console\Commands\ProcessQueueCommand;
use App\Models\DebugLogEntry;
use Filament\Notifications\Notification;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Artisan;
use Livewire\Component;

class DebugLogViewer extends Component
{
    protected static bool $isDiscovered = false;

    public function runQueueNow(): void
    {
        $exitCode = Artisan::call('queue:process', ['--max-time' => 60]);

        if ($exitCode === ProcessQueueCommand::EXIT_ALREADY_RUNNING) {
            Notification::make()
                ->warning()
                ->title('Queue already running')
                ->body('A queue processor is already running. Wait for it to finish or try again shortly.')
                ->send();

            return;
        }

        Notification::make()
            ->success()
            ->title('Queue processed')
            ->body('Pending jobs have been processed (or none were queued).')
            ->send();
    }

    public function getViewData(): array
    {
        $entries = DebugLogEntry::query()
            ->orderByDesc('created_at')
            ->limit(50)
            ->get();

        return ['entries' => $entries];
    }

    public function render(): View
    {
        return view('filament.livewire.debug-log-viewer', $this->getViewData());
    }
}
