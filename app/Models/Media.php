<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

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
        return route('media.serve', ['path' => $this->file_path]);
    }

    public static function scopeForVenue($query, ?int $venueId)
    {
        if ($venueId === null) {
            return $query;
        }

        return $query->where('venue_id', $venueId);
    }
}
