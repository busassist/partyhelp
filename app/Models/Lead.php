<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'first_name', 'last_name', 'email', 'phone',
        'occasion_type', 'guest_count', 'preferred_date',
        'suburb', 'room_styles', 'budget_range',
        'special_requirements', 'base_price', 'current_price',
        'discount_percent', 'status', 'purchase_target',
        'purchase_count', 'distributed_at', 'fulfilled_at',
        'expires_at', 'webhook_payload',
    ];

    protected function casts(): array
    {
        return [
            'room_styles' => 'array',
            'preferred_date' => 'date',
            'base_price' => 'decimal:2',
            'current_price' => 'decimal:2',
            'distributed_at' => 'datetime',
            'fulfilled_at' => 'datetime',
            'expires_at' => 'datetime',
            'webhook_payload' => 'array',
        ];
    }

    public function matches(): HasMany
    {
        return $this->hasMany(LeadMatch::class);
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(LeadPurchase::class);
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function isFulfilled(): bool
    {
        return $this->purchase_count >= $this->purchase_target;
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isAvailable(): bool
    {
        return ! $this->isFulfilled()
            && ! $this->isExpired()
            && in_array($this->status, ['distributed', 'partially_fulfilled']);
    }
}
