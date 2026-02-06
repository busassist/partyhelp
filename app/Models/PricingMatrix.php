<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PricingMatrix extends Model
{
    protected $table = 'pricing_matrix';

    protected $fillable = [
        'occasion_type', 'guest_min', 'guest_max',
        'price', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public static function getPrice(string $occasionType, int $guestCount): ?float
    {
        $entry = static::where('occasion_type', $occasionType)
            ->where('guest_min', '<=', $guestCount)
            ->where('guest_max', '>=', $guestCount)
            ->where('is_active', true)
            ->first();

        return $entry?->price;
    }
}
