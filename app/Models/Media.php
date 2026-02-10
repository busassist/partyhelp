<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Media extends Model
{
    protected $fillable = [
        'venue_id', 'file_path', 'file_name',
        'mime_type', 'size',
    ];

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    public function getUrlAttribute(): string
    {
        return $this->buildMediaUrl($this->file_path);
    }

    public static function buildMediaUrl(string $path): string
    {
        if (! config('filesystems.media_use_transparent_urls', true)) {
            $diskName = config('filesystems.media_disk', 'spaces');
            $diskConfig = config("filesystems.disks.{$diskName}", []);
            if (! empty($diskConfig['url'])) {
                return rtrim($diskConfig['url'], '/') . '/' . ltrim($path, '/');
            }
        }

        return route('media.serve', ['path' => $path]);
    }

    public static function scopeForVenue($query, ?int $venueId)
    {
        if ($venueId === null) {
            return $query;
        }

        return $query->where('venue_id', $venueId);
    }
}
