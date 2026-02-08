<?php

namespace App\Livewire;

use App\Models\Media;
use App\Services\MediaUploadService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
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

    public int $uploadIteration = 0;

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

    public function deleteImage(int $mediaId): void
    {
        $media = Media::findOrFail($mediaId);

        if (! $this->canDelete($media)) {
            return;
        }

        $disk = config('filesystems.media_disk', 'spaces');
        if (Storage::disk($disk)->exists($media->file_path)) {
            Storage::disk($disk)->delete($media->file_path);
        }
        $media->delete();
    }

    private function canDelete(Media $media): bool
    {
        if ($this->isAdmin) {
            return true;
        }

        return $media->venue_id !== null && $media->venue_id === $this->venueId;
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

            app(MediaUploadService::class)->upload(
                $this->uploadedFile,
                $this->isAdmin ? null : $this->venueId
            );

            $this->uploadedFile = null;
            $this->uploadIteration++;
            $this->resetValidation();
            $this->js("window.dispatchEvent(new CustomEvent('media-upload-complete'))");
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
