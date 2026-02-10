<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class VenueStyle extends Model
{
    protected $fillable = [
        'name', 'image_path', 'sort_order', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function venues(): BelongsToMany
    {
        return $this->belongsToMany(Venue::class, 'venue_venue_style');
    }

    public function getImageUrlAttribute(): ?string
    {
        if (! $this->image_path) {
            return null;
        }

        return Media::buildMediaUrl($this->image_path);
    }

    public static function optionsForSelect(): array
    {
        return static::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->mapWithKeys(fn (VenueStyle $s) => [$s->id => $s->name])
            ->toArray();
    }
}
