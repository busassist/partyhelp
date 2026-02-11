<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PricingMatrix extends Model
{
    protected $table = 'pricing_matrix';

    protected $fillable = [
        'occasion_type', 'guest_bracket_id', 'price', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function guestBracket(): BelongsTo
    {
        return $this->belongsTo(GuestBracket::class);
    }

    public static function getPrice(string $occasionType, int $guestCount): ?float
    {
        $bracket = GuestBracket::where('is_active', true)
            ->where('guest_min', '<=', $guestCount)
            ->where(function ($q) use ($guestCount) {
                $q->whereNull('guest_max')->orWhere('guest_max', '>=', $guestCount);
            })
            ->orderBy('sort_order')
            ->first();

        if (! $bracket) {
            return null;
        }

        $entry = static::where('occasion_type', $occasionType)
            ->where('guest_bracket_id', $bracket->id)
            ->where('is_active', true)
            ->first();

        return $entry?->price;
    }
}
