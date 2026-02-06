<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'venue_id', 'name', 'style', 'min_capacity', 'max_capacity',
        'seated_capacity', 'hire_cost_min', 'hire_cost_max',
        'description', 'features', 'images', 'sort_order', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'features' => 'array',
            'images' => 'array',
            'hire_cost_min' => 'decimal:2',
            'hire_cost_max' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    public function canAccommodate(int $guestCount): bool
    {
        $buffer = (int) ceil($guestCount * 1.2);

        return $this->min_capacity <= $guestCount
            && $this->max_capacity >= $guestCount;
    }
}
