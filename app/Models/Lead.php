<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\URL;

class Lead extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'first_name', 'last_name', 'email', 'phone',
        'occasion_type', 'guest_count', 'preferred_date',
        'suburb', 'suburb_preferences', 'location_selections', 'room_styles', 'budget_range',
        'special_requirements', 'base_price', 'current_price',
        'discount_percent', 'status', 'purchase_target',
        'purchase_count', 'distributed_at', 'fulfilled_at',
        'expires_at', 'additional_services_email_sent_at', 'webhook_payload',
    ];

    protected function casts(): array
    {
        return [
            'room_styles' => 'array',
            'suburb_preferences' => 'array',
            'location_selections' => 'array',
            'preferred_date' => 'date',
            'base_price' => 'decimal:2',
            'current_price' => 'decimal:2',
            'distributed_at' => 'datetime',
            'fulfilled_at' => 'datetime',
            'expires_at' => 'datetime',
            'additional_services_email_sent_at' => 'datetime',
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

    /**
     * Guest count for display (e.g. in emails). Uses raw webhook value when it
     * was a range (e.g. "46-60"); otherwise the stored numeric guest_count.
     */
    public function getGuestCountDisplayAttribute(): string
    {
        $raw = $this->webhook_payload;
        if (is_array($raw)) {
            $keys = ['Number_of_Guests', 'guest_count', 'guests'];
            foreach ($keys as $key) {
                if (isset($raw[$key]) && is_string($raw[$key]) && trim($raw[$key]) !== '') {
                    $val = trim($raw[$key]);
                    if (preg_match('/^\d+\s*-\s*\d+$/', $val)) {
                        return $val;
                    }
                }
            }
        }

        return (string) (int) $this->guest_count;
    }

    /** Signed URL for a venue to view/purchase this lead (used in lead opportunity emails). */
    public function signedPurchaseUrlFor(Venue $venue): string
    {
        return URL::signedRoute('lead.purchase.show', ['lead' => $this, 'venue' => $venue]);
    }

    /** Signed URL for the customer to select additional services (used in additional services email). */
    public function signedAdditionalServicesUrl(): string
    {
        return URL::signedRoute('additional-services.show', ['lead' => $this]);
    }

    /** Preferred locations from form (multiple suburbs). Falls back to single suburb. */
    public function getPreferredLocationsAttribute(): array
    {
        $prefs = $this->suburb_preferences;
        if (is_array($prefs) && count($prefs) > 0) {
            return array_values(array_filter($prefs));
        }

        return $this->suburb ? [$this->suburb] : [];
    }

    /**
     * Location hierarchy for display: area-only and area→suburb.
     * Each item: ['label' => 'Location → CBD' or 'Location → CBD → Southbank']
     */
    public function getLocationHierarchyLinesAttribute(): array
    {
        $selections = $this->location_selections;
        if (is_array($selections) && count($selections) > 0) {
            $lines = [];
            foreach ($selections as $item) {
                $type = $item['type'] ?? null;
                $area = $item['area'] ?? null;
                $name = $item['name'] ?? '';
                if ($name === '') {
                    continue;
                }
                if ($type === 'area') {
                    $lines[] = 'Location → ' . $name;
                } elseif ($type === 'suburb' && $area !== null && $area !== '') {
                    $lines[] = 'Location → ' . $area . ' → ' . $name;
                } else {
                    $lines[] = 'Location → ' . $name;
                }
            }

            return $lines;
        }

        $prefs = $this->suburb_preferences;
        if (is_array($prefs) && count($prefs) > 0) {
            return array_map(fn (string $s) => 'Location → ' . $s, $prefs);
        }

        $payload = $this->webhook_payload;
        if (is_array($payload) && ! empty($payload['location']) && is_array($payload['location'])) {
            return $this->linesFromRawLocationArray($payload['location']);
        }

        if ($this->suburb) {
            return ['Location → ' . $this->suburb];
        }

        return [];
    }

    private function linesFromRawLocationArray(array $arr): array
    {
        $lines = [];
        foreach ($arr as $v) {
            $s = trim((string) $v);
            if ($s === '') {
                continue;
            }
            if (str_starts_with($s, 'AREA:')) {
                $lines[] = 'Location → ' . trim(substr($s, 5));
            } elseif (str_starts_with($s, 'SUBURB:')) {
                $rest = trim(substr($s, 7));
                $parts = explode(':', $rest, 2);
                $area = trim($parts[0] ?? '');
                $sub = trim($parts[1] ?? '');
                if ($sub !== '') {
                    $lines[] = $area !== '' ? 'Location → ' . $area . ' → ' . $sub : 'Location → ' . $sub;
                }
            } else {
                $lines[] = 'Location → ' . $s;
            }
        }

        return $lines;
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
