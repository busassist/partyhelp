<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CreditTransaction extends Model
{
    protected $fillable = [
        'venue_id', 'type', 'amount', 'balance_after',
        'description', 'stripe_payment_intent_id',
        'lead_purchase_id', 'admin_note',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'balance_after' => 'decimal:2',
        ];
    }

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    public function leadPurchase(): BelongsTo
    {
        return $this->belongsTo(LeadPurchase::class);
    }
}
