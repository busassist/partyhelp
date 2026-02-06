<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class OccasionType extends Model
{
    protected $fillable = [
        'key', 'label', 'sort_order', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public static function options(): array
    {
        return Cache::remember('occasion_types', 3600, function () {
            $types = static::where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('label')
                ->pluck('label', 'key');

            return $types->isNotEmpty()
                ? $types->toArray()
                : config('partyhelp.occasion_types', []);
        });
    }

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget('occasion_types'));
        static::deleted(fn () => Cache::forget('occasion_types'));
    }
}
