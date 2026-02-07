<?php

namespace App\Livewire;

use App\Models\Media;
use App\Services\MediaUploadService;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithFileUploads;

class MediaLibraryModal extends Component
{
    use WithFileUploads;

    public ?int $venueId = null;

    public bool $isAdmin = false;

    public string $activeTab = 'library';

    public string $searchQuery = '';

    public $uploadedFile = null;

    protected $listeners = ['openModal' => 'resetState'];

    public function resetState(): void
    {
        $this->activeTab = 'library';
        $this->searchQuery = '';
        $this->uploadedFile = null;
        $this->resetValidation();
    }

    public function getMediaItemsProperty()
    {
        return Media::query()
            ->when($this->venueId !== null && ! $this->isAdmin, fn ($q) => $q->forVenue($this->venueId))
            ->when(
                filled($this->searchQuery),
                fn ($q) => $q->where('file_name', 'like', '%' . $this->searchQuery . '%')
            )
            ->orderByDesc('created_at')
            ->get();
    }

    public function selectImage(string $path): void
    {
        $this->dispatchMediaSelected($path);
    }

    public function updatedUploadedFile(): void
    {
        if ($this->uploadedFile) {
            Log::debug('[MediaLibraryModal] File received', [
                'size' => $this->uploadedFile->getSize(),
                'mime' => $this->uploadedFile->getMimeType(),
                'name' => $this->uploadedFile->getClientOriginalName(),
            ]);
            $this->upload();
        }
    }

    public function upload(): void
    {
        if (! $this->uploadedFile) {
            return;
        }

        try {
            $this->validate([
                'uploadedFile' => 'required|image|mimes:jpeg,jpg,png,gif,webp|max:10240',
            ]);

            $media = app(MediaUploadService::class)->upload(
                $this->uploadedFile,
                $this->isAdmin ? null : $this->venueId
            );

            $this->uploadedFile = null;
            $this->resetValidation();
            $this->dispatchMediaSelected($media->file_path);
        } catch (\Throwable $e) {
            Log::error('[MediaLibraryModal] Upload failed', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    private function dispatchMediaSelected(string $path): void
    {
        $pathJson = json_encode($path);
        $this->js("window.dispatchEvent(new CustomEvent('media-selected', { detail: { path: {$pathJson} } }))");
    }

    public function getThemeColorsProperty(): array
    {
        return [
            'primary' => '#7c3aed',
            'textDark' => '#1b1b18',
            'textMuted' => '#6b7280',
            'textLight' => '#9ca3af',
            'borderLight' => '#f3f4f6',
            'borderMed' => '#e5e7eb',
            'borderDark' => '#d1d5db',
            'bgInput' => '#f9fafb',
        ];
    }

    public function render()
    {
        return view('livewire.media-library-modal');
    }
}
