<?php

namespace App\Services;

use App\Models\Lead;
use App\Models\LeadMatch;
use App\Models\PricingMatrix;
use App\Models\SystemSetting;
use App\Models\Venue;
use Illuminate\Support\Str;

/**
 * Injects a single fake lead and notifies the given venue (test mode only).
 * Used when APP_ENV is not production and the test user (e.g. venue@partyhelp.com.au)
 * wants to see a lead on the Available Leads page.
 */
class TestLeadInjectionService
{
    private const SUBURBS = [
        'Richmond', 'Fitzroy', 'Collingwood', 'St Kilda', 'South Yarra',
        'Melbourne', 'South Melbourne', 'Port Melbourne', 'Carlton', 'Prahran',
    ];

    public function inject(Venue $venue): Lead
    {
        $occasionTypes = array_keys(\App\Models\OccasionType::options());
        $roomStyles = array_keys(config('partyhelp.room_styles', []));
        $occasion = $occasionTypes[array_rand($occasionTypes)];
        $guestCount = (int) fake()->randomElement([20, 40, 60, 80, 100]);
        $basePrice = PricingMatrix::getPrice($occasion, $guestCount) ?? 29.99;
        $currentPrice = round($basePrice, 2);
        $purchaseTarget = (int) SystemSetting::get('lead_fulfilment_threshold', 3);
        $expiryHours = (int) SystemSetting::get('lead_expiry_hours', 72);

        $suburb = $this->suburbForVenue($venue);
        $roomStylesForLead = $roomStyles ? fake()->randomElements($roomStyles, min(2, count($roomStyles))) : ['function_room'];

        $lead = Lead::create([
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => 'test+' . Str::random(8) . '@partyhelp.com.au',
            'phone' => '04' . fake()->numerify('########'),
            'occasion_type' => $occasion,
            'guest_count' => $guestCount,
            'preferred_date' => now()->addWeeks(rand(2, 12)),
            'suburb' => $suburb,
            'room_styles' => $roomStylesForLead,
            'budget_range' => fake()->optional(0.6)->randomElement(['1500-3000', '3000-5000', '5000-10000']),
            'special_requirements' => fake()->optional(0.3)->sentence(),
            'base_price' => $basePrice,
            'current_price' => $currentPrice,
            'discount_percent' => 0,
            'status' => 'distributed',
            'purchase_target' => $purchaseTarget,
            'purchase_count' => 0,
            'distributed_at' => now(),
            'fulfilled_at' => null,
            'expires_at' => now()->addHours($expiryHours),
        ]);

        $matchScore = $this->matchScoreForVenue($lead, $venue);

        LeadMatch::create([
            'lead_id' => $lead->id,
            'venue_id' => $venue->id,
            'match_score' => $matchScore,
            'status' => 'notified',
            'notified_at' => now(),
        ]);

        return $lead;
    }

    private function suburbForVenue(Venue $venue): string
    {
        $suburbs = collect([$venue->suburb])
            ->merge($venue->suburb_tags ?? [])
            ->filter()
            ->map(fn ($s) => trim((string) $s))
            ->unique()
            ->values();

        if ($suburbs->isNotEmpty()) {
            return $suburbs->random();
        }

        return self::SUBURBS[array_rand(self::SUBURBS)];
    }

    private function matchScoreForVenue(Lead $lead, Venue $venue): float
    {
        $score = 50.0;
        if ($venue->suburb && strcasecmp(trim($venue->suburb), trim($lead->suburb)) === 0) {
            $score += 25;
        } elseif ($venue->suburb_tags && in_array($lead->suburb, array_map('trim', (array) $venue->suburb_tags))) {
            $score += 15;
        }
        if ($venue->occasion_tags && in_array($lead->occasion_type, (array) $venue->occasion_tags)) {
            $score += 15;
        }

        return min(100, round($score + rand(0, 10), 2));
    }
}
