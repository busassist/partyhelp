<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class LeadMatch extends Model
{
    protected $fillable = [
        'lead_id', 'venue_id', 'match_score', 'status',
        'notified_at', 'viewed_at', 'purchased_at',
    ];

    protected function casts(): array
    {
        return [
            'match_score' => 'decimal:2',
            'notified_at' => 'datetime',
            'viewed_at' => 'datetime',
            'purchased_at' => 'datetime',
        ];
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    public function purchase(): HasOne
    {
        return $this->hasOne(LeadPurchase::class);
    }
}
