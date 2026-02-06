<?php

namespace Database\Seeders;

use App\Models\CreditTransaction;
use App\Models\Lead;
use App\Models\LeadMatch;
use App\Models\LeadPurchase;
use App\Models\PricingMatrix;
use App\Models\Venue;
use Illuminate\Database\Seeder;

class LeadSeeder extends Seeder
{
    private const MELBOURNE_SUBURBS = [
        'Richmond', 'Fitzroy', 'Collingwood', 'St Kilda', 'South Yarra',
        'Carlton', 'Brunswick', 'Prahran', 'South Melbourne', 'Port Melbourne',
        'Melbourne', 'Abbotsford', 'North Melbourne', 'Footscray', 'Williamstown',
        'Elwood', 'Brighton', 'Hawthorn', 'Camberwell', 'Kew',
    ];

    public function run(): void
    {
        $occasionTypes = array_keys(\App\Models\OccasionType::options());
        $roomStyles = array_keys(config('partyhelp.room_styles'));
        $venues = Venue::where('status', 'active')->get();
        $purchaseTarget = (int) config('partyhelp.lead.fulfilment_threshold', 3);

        for ($i = 0; $i < 80; $i++) {
            $occasion = $occasionTypes[array_rand($occasionTypes)];
            $guestCount = fake()->randomElement([15, 25, 40, 60, 80, 120, 150]);
            $basePrice = PricingMatrix::getPrice($occasion, $guestCount) ?? rand(10, 35);
            $discountPercent = fake()->randomElement([0, 0, 0, 10, 20]);
            $currentPrice = round($basePrice * (1 - $discountPercent / 100), 2);

            $status = $this->randomStatus();
            $createdAt = fake()->dateTimeBetween('-60 days');
            $expiresAt = $this->expiresAtForStatus($status, $createdAt);
            $distributedAt = in_array($status, ['distributed', 'partially_fulfilled', 'fulfilled'])
                ? (clone $createdAt)->modify('+5 minutes') : null;

            $lead = Lead::create([
                'first_name' => fake()->firstName(),
                'last_name' => fake()->lastName(),
                'email' => fake()->unique()->safeEmail(),
                'phone' => '04' . fake()->numerify('########'),
                'occasion_type' => $occasion,
                'guest_count' => $guestCount,
                'preferred_date' => fake()->dateTimeBetween('+2 weeks', '+6 months'),
                'suburb' => self::MELBOURNE_SUBURBS[array_rand(self::MELBOURNE_SUBURBS)],
                'room_styles' => fake()->randomElements($roomStyles, rand(1, 3)),
                'budget_range' => fake()->optional(0.7)->randomElement(['1500-3000', '3000-5000', '5000-10000']),
                'special_requirements' => fake()->optional(0.3)->sentence(),
                'base_price' => $basePrice,
                'current_price' => $currentPrice,
                'discount_percent' => $discountPercent,
                'status' => $status,
                'purchase_target' => $purchaseTarget,
                'purchase_count' => 0,
                'distributed_at' => $distributedAt,
                'fulfilled_at' => null,
                'expires_at' => $expiresAt,
                'created_at' => $createdAt,
            ]);

            if ($distributedAt) {
                $this->createLeadMatches($lead, $venues, $currentPrice, $purchaseTarget);
            }
        }
    }

    private function randomStatus(): string
    {
        $weights = [
            'new' => 5,
            'distributed' => 15,
            'partially_fulfilled' => 12,
            'fulfilled' => 25,
            'expired' => 18,
            'cancelled' => 5,
        ];
        $random = rand(1, 80);
        $cumulative = 0;
        foreach ($weights as $status => $weight) {
            $cumulative += $weight;
            if ($random <= $cumulative) {
                return $status;
            }
        }

        return 'distributed';
    }

    private function expiresAtForStatus(string $status, \DateTime $created): ?\DateTime
    {
        if ($status === 'new') {
            return null;
        }
        $expires = (clone $created)->modify('+72 hours');
        if ($status === 'expired') {
            return (clone $created)->modify('-' . rand(1, 30) . ' days');
        }

        return $expires;
    }

    private function createLeadMatches(Lead $lead, $venues, float $currentPrice, int $purchaseTarget): void
    {
        $matchCount = min(30, $venues->count());
        $selectedVenues = $venues->random($matchCount);
        $purchaseCount = match ($lead->status) {
            'fulfilled' => $purchaseTarget,
            'partially_fulfilled' => rand(1, $purchaseTarget - 1),
            default => 0,
        };

        $purchasedVenueIds = [];
        if ($purchaseCount > 0) {
            $purchasedVenueIds = $selectedVenues->take($purchaseCount)->pluck('id')->toArray();
        }

        $sortOrder = 0;
        foreach ($selectedVenues as $venue) {
            $isPurchased = in_array($venue->id, $purchasedVenueIds);
            $matchScore = $this->matchScore($lead, $venue);

            $match = LeadMatch::create([
                'lead_id' => $lead->id,
                'venue_id' => $venue->id,
                'match_score' => $matchScore,
                'status' => $isPurchased ? 'purchased' : 'notified',
                'notified_at' => $lead->distributed_at,
                'viewed_at' => fake()->optional(0.6)->dateTimeBetween($lead->distributed_at, 'now'),
                'purchased_at' => $isPurchased ? fake()->dateTimeBetween($lead->distributed_at, 'now') : null,
            ]);

            if ($isPurchased) {
                $this->createPurchase($lead, $venue, $match, $currentPrice);
            }
        }

        $lead->update(['purchase_count' => $purchaseCount]);
        if ($purchaseCount >= $purchaseTarget) {
            $lead->update(['fulfilled_at' => $lead->distributed_at]);
        }
    }

    private function matchScore(Lead $lead, Venue $venue): float
    {
        $score = 50.0;
        if ($venue->suburb === $lead->suburb) {
            $score += 25;
        } elseif ($venue->suburb_tags && in_array($lead->suburb, $venue->suburb_tags)) {
            $score += 15;
        }
        if ($venue->occasion_tags && in_array($lead->occasion_type, $venue->occasion_tags)) {
            $score += 15;
        }

        return min(100, $score + rand(0, 10));
    }

    private function createPurchase(Lead $lead, Venue $venue, LeadMatch $match, float $amount): void
    {
        $venue->refresh();
        if ($venue->credit_balance < $amount) {
            $venue->update(['credit_balance' => $venue->credit_balance + 200]);
        }

        $purchase = LeadPurchase::create([
            'lead_id' => $lead->id,
            'venue_id' => $venue->id,
            'lead_match_id' => $match->id,
            'amount_paid' => $amount,
            'discount_percent' => $lead->discount_percent,
            'lead_status' => fake()->randomElement(['pending', 'contacted', 'quoted', 'booked', 'lost']),
            'notes' => fake()->optional(0.3)->sentence(),
        ]);

        $venue = Venue::lockForUpdate()->find($venue->id);
        $venue->credit_balance -= $amount;
        $venue->save();

        CreditTransaction::create([
            'venue_id' => $venue->id,
            'type' => 'purchase',
            'amount' => -$amount,
            'balance_after' => $venue->credit_balance,
            'description' => "Lead #{$lead->id} purchase",
            'lead_purchase_id' => $purchase->id,
        ]);
    }
}
