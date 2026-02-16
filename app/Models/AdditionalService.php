<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdditionalService extends Model
{
    protected $fillable = ['name', 'thumbnail_path', 'sort_order', 'is_active'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function getThumbnailUrlAttribute(): ?string
    {
        if (empty($this->thumbnail_path)) {
            return null;
        }

        // Legacy: paths from old FileUpload (storage/app/public) stay as asset()
        if (str_starts_with($this->thumbnail_path, 'additional_services/')) {
            return asset('storage/' . ltrim($this->thumbnail_path, '/'));
        }

        return \App\Models\Media::buildMediaUrl($this->thumbnail_path);
    }

    public static function ordered(): \Illuminate\Database\Eloquent\Builder
    {
        return static::query()->where('is_active', true)->orderBy('sort_order')->orderBy('name');
    }
}
