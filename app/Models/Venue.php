<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Venue extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'business_name', 'abn', 'contact_name',
        'contact_email', 'contact_phone', 'website', 'address',
        'suburb', 'state', 'postcode', 'area_id', 'suburb_tags', 'occasion_tags',
        'credit_balance', 'auto_topup_threshold', 'auto_topup_amount',
        'auto_topup_enabled', 'stripe_customer_id',
        'stripe_payment_method_id', 'status', 'approved_at',
        'last_activity_at',
    ];

    protected function casts(): array
    {
        return [
            'suburb_tags' => 'array',
            'occasion_tags' => 'array',
            'credit_balance' => 'decimal:2',
            'auto_topup_threshold' => 'decimal:2',
            'auto_topup_amount' => 'decimal:2',
            'auto_topup_enabled' => 'boolean',
            'approved_at' => 'datetime',
            'last_activity_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class)->orderBy('sort_order');
    }

    public function images(): HasMany
    {
        return $this->hasMany(VenueImage::class)->orderBy('sort_order');
    }

    public function functionPacks(): HasMany
    {
        return $this->hasMany(FunctionPack::class);
    }

    public function leadMatches(): HasMany
    {
        return $this->hasMany(LeadMatch::class);
    }

    public function leadPurchases(): HasMany
    {
        return $this->hasMany(LeadPurchase::class);
    }

    public function creditTransactions(): HasMany
    {
        return $this->hasMany(CreditTransaction::class);
    }

    public function isSeedEmail(): bool
    {
        $email = $this->contact_email ?? '';

        return str_contains(strtolower($email), '@seed.partyhelp.com.au');
    }

    public function venueStyles(): BelongsToMany
    {
        return $this->belongsToMany(VenueStyle::class, 'venue_venue_style');
    }

    public function heroImage(): ?VenueImage
    {
        return $this->images()->where('is_hero', true)->first()
            ?? $this->images()->first();
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function hasSufficientCredit(float $amount): bool
    {
        return $this->credit_balance >= $amount;
    }
}
