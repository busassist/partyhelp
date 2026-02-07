<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Feature extends Model
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

    /** Map legacy labels (from pre-master-data rooms) to current keys. */
    public static function labelToKeyMap(): array
    {
        return [
            'AV system' => 'av_equipment',
            'AV Equipment' => 'av_equipment',
            'Dance floor' => 'dance_floor',
            'Dance Floor' => 'dance_floor',
            'Private bar' => 'private_bar',
            'Private Bar' => 'private_bar',
            'Outdoor access' => 'outdoor_access',
            'Outdoor Access' => 'outdoor_access',
            'Stage' => 'stage',
            'Projector' => 'projector',
            'PA system' => 'sound_system',
            'Sound system' => 'sound_system',
            'Sound System' => 'sound_system',
            'In-house Catering' => 'catering',
            'Catering' => 'catering',
            'Heaters' => null,
            'Whiteboard' => null,
        ];
    }

    /** Normalize feature values (keys or legacy labels) to valid keys only. */
    public static function normalizeToKeys(array $values): array
    {
        $map = self::labelToKeyMap();
        $validKeys = array_keys(self::options());
        $result = [];

        foreach ($values as $v) {
            if (in_array($v, $validKeys, true)) {
                $result[] = $v;
            } elseif (isset($map[$v]) && $map[$v] !== null) {
                $result[] = $map[$v];
            }
        }

        return array_values(array_unique($result));
    }

    public static function options(): array
    {
        return Cache::remember('features', 3600, function () {
            $features = static::where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('label')
                ->pluck('label', 'key');

            return $features->isNotEmpty() ? $features->toArray() : [];
        });
    }

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget('features'));
        static::deleted(fn () => Cache::forget('features'));
    }
}
