<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class FunctionPack extends Model
{
    protected $fillable = [
        'venue_id', 'title', 'file_path', 'file_name',
        'mime_type', 'file_size', 'download_token', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (FunctionPack $pack) {
            if (empty($pack->download_token)) {
                $pack->download_token = Str::uuid()->toString();
            }
        });
    }

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    public function getDownloadUrlAttribute(): string
    {
        return route('function-pack.download', $this->download_token);
    }
}
