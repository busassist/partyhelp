<?php

namespace App\Filament\Livewire;

use App\Models\DebugLogEntry;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class DebugLogViewer extends Component
{
    protected static bool $isDiscovered = false;

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
