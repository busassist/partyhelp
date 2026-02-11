<?php

namespace App\Services;

use App\Models\Lead;
use App\Models\Venue;
use Illuminate\Support\Collection;

class LeadMatchingService
{
    public function findMatches(Lead $lead): Collection
    {
        $maxMatches = (int) config('partyhelp.lead.max_matches', 30);

        $venues = Venue::where('status', 'active')
            ->where('credit_balance', '>', 0)
            ->with('rooms')
            ->get();

        $scored = $venues->map(fn (Venue $venue) => [
            'venue' => $venue,
            'score' => $this->calculateScore($lead, $venue),
        ])
            ->filter(fn (array $item) => $item['score'] > 0)
            ->sortByDesc('score')
            ->take($maxMatches);

        return $scored;
    }

    private function calculateScore(Lead $lead, Venue $venue): float
    {
        $score = 0;

        $score += $this->scoreLocation($lead, $venue) * 5;
        $score += $this->scoreCapacity($lead, $venue) * 4;
        $score += $this->scoreRoomStyle($lead, $venue) * 3;
        $score += $this->scoreOccasion($lead, $venue) * 2;
        $score += $this->scoreBudget($lead, $venue) * 1;

        return round($score, 2);
    }

    private function scoreLocation(Lead $lead, Venue $venue): float
    {
        $venueSuburbs = collect($venue->suburb_tags ?? [])
            ->push($venue->suburb)
            ->map(fn ($s) => strtolower(trim($s)));

        if ($venueSuburbs->contains(strtolower($lead->suburb))) {
            return 1.0;
        }

        return 0;
    }

    private function scoreCapacity(Lead $lead, Venue $venue): float
    {
        $matchingRooms = $venue->rooms->filter(
            fn ($room) => $room->is_active
                && $room->max_capacity >= $lead->guest_count
                && $room->min_capacity <= $lead->guest_count
        );

        return $matchingRooms->isNotEmpty() ? 1.0 : 0;
    }

    private function scoreRoomStyle(Lead $lead, Venue $venue): float
    {
        $requestedStyles = collect($lead->room_styles ?? []);

        if ($requestedStyles->isEmpty()) {
            return 0.5;
        }

        $venueStyles = $venue->rooms
            ->where('is_active', true)
            ->pluck('style')
            ->unique();

        $overlap = $requestedStyles->intersect($venueStyles)->count();

        return $overlap > 0
            ? $overlap / $requestedStyles->count()
            : 0;
    }

    private function scoreOccasion(Lead $lead, Venue $venue): float
    {
        $venueTags = collect($venue->occasion_tags ?? [])
            ->map(fn ($t) => strtolower(trim($t)));

        if ($venueTags->isEmpty()) {
            return 0.5;
        }

        return $venueTags->contains(strtolower($lead->occasion_type))
            ? 1.0
            : 0;
    }

    private function scoreBudget(Lead $lead, Venue $venue): float
    {
        if (empty($lead->budget_range)) {
            return 0.5;
        }

        $parts = explode('-', str_replace(' ', '', $lead->budget_range));

        if (count($parts) !== 2) {
            return 0.5;
        }

        $leadMin = (float) $parts[0];
        $leadMax = (float) $parts[1];

        $matchingRooms = $venue->rooms->filter(function ($room) use ($leadMin, $leadMax) {
            if (! $room->hire_cost_min && ! $room->hire_cost_max) {
                return true;
            }

            $roomMin = $room->hire_cost_min ?? 0;
            $roomMax = $room->hire_cost_max ?? PHP_FLOAT_MAX;

            return $roomMin <= $leadMax && $roomMax >= $leadMin;
        });

        return $matchingRooms->isNotEmpty() ? 1.0 : 0;
    }
}
