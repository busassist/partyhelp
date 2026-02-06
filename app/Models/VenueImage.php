<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VenueImage extends Model
{
    protected $fillable = [
        'venue_id', 'file_path', 'file_name',
        'sort_order', 'is_hero',
    ];

    protected function casts(): array
    {
        return [
            'is_hero' => 'boolean',
        ];
    }

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }
}
