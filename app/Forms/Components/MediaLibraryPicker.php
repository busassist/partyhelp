<?php

namespace App\Forms\Components;

use Closure;
use Filament\Forms\Components\Field;
use Filament\Support\Concerns\HasExtraAlpineAttributes;

class MediaLibraryPicker extends Field
{
    use HasExtraAlpineAttributes;
    protected string $view = 'forms.components.media-library-picker';

    protected int $maxFiles = 4;

    protected int | Closure | null $venueId = null;

    protected bool | Closure $isAdmin = false;

    protected bool | Closure $autoSave = false;

    public function venueId(int | Closure | null $venueId): static
    {
        $this->venueId = $venueId;

        return $this;
    }

    public function isAdmin(bool | Closure $isAdmin = true): static
    {
        $this->isAdmin = $isAdmin;

        return $this;
    }

    public function maxFiles(int $max): static
    {
        $this->maxFiles = $max;

        return $this;
    }

    public function autoSave(bool | Closure $autoSave = true): static
    {
        $this->autoSave = $autoSave;

        return $this;
    }

    public function getVenueId(): ?int
    {
        return $this->evaluate($this->venueId);
    }

    public function getIsAdmin(): bool
    {
        return (bool) $this->evaluate($this->isAdmin);
    }

    public function getMaxFiles(): int
    {
        return $this->maxFiles;
    }

    public function getAutoSave(): bool
    {
        return (bool) $this->evaluate($this->autoSave);
    }
}
