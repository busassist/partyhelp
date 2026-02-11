<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GuestBracket extends Model
{
    protected $fillable = [
        'guest_min', 'guest_max', 'sort_order', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function pricingMatrixRows(): HasMany
    {
        return $this->hasMany(PricingMatrix::class, 'guest_bracket_id');
    }

    /** True when this bracket has no upper limit (guest_max is null). */
    public function isMaximumLimit(): bool
    {
        return $this->guest_max === null;
    }

    /** Label for display e.g. "10-29" or "100+" when maximum limit. */
    public function getLabelAttribute(): string
    {
        if ($this->isMaximumLimit()) {
            return $this->guest_min . '+';
        }

        return $this->guest_min === $this->guest_max
            ? (string) $this->guest_min
            : "{$this->guest_min}-{$this->guest_max}";
    }

    /** Value key e.g. "10-29" or "100-max" when maximum limit. */
    public function getValueAttribute(): string
    {
        if ($this->isMaximumLimit()) {
            return $this->guest_min . '-max';
        }

        return "{$this->guest_min}-{$this->guest_max}";
    }

    public function contains(int $guestCount): bool
    {
        if ($guestCount < $this->guest_min) {
            return false;
        }

        return $this->isMaximumLimit() || $guestCount <= $this->guest_max;
    }
}
