<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class LeadPurchase extends Model
{
    protected $fillable = [
        'lead_id', 'venue_id', 'lead_match_id', 'amount_paid',
        'discount_percent', 'lead_status', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount_paid' => 'decimal:2',
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

    public function leadMatch(): BelongsTo
    {
        return $this->belongsTo(LeadMatch::class);
    }

    public function creditTransaction(): HasOne
    {
        return $this->hasOne(CreditTransaction::class);
    }
}
