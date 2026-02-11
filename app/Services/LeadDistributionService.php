<?php

namespace App\Services;

use App\Jobs\SendLeadOpportunityNotification;
use App\Models\Lead;
use App\Models\LeadMatch;
use App\Models\PricingMatrix;
use App\Models\SystemSetting;
use App\Services\DebugLogService;

class LeadDistributionService
{
    public function __construct(
        private LeadMatchingService $matchingService,
    ) {}

    public function distribute(Lead $lead): void
    {
        $price = PricingMatrix::getPrice(
            $lead->occasion_type,
            $lead->guest_count,
        ) ?? 29.99;

        $lead->base_price = $price;
        $lead->current_price = $price;

        $lead->purchase_target = (int) SystemSetting::get('lead_fulfilment_threshold', 3);
        $lead->expires_at = now()->addHours(
            (int) SystemSetting::get('lead_expiry_hours', 72)
        );

        $matches = $this->matchingService->findMatches($lead);

        if ($matches->isEmpty()) {
            $lead->status = 'new';
            $lead->save();
            $this->notifyAdminLowMatches($lead, 0);
            DebugLogService::logVenuesMatched($lead, 0, []);

            return;
        }

        if ($matches->count() < 10) {
            $this->notifyAdminLowMatches($lead, $matches->count());
        }

        $venueNames = $matches->pluck('venue.business_name')->filter();
        DebugLogService::logVenuesMatched($lead, $matches->count(), $venueNames);

        foreach ($matches as $matchData) {
            LeadMatch::create([
                'lead_id' => $lead->id,
                'venue_id' => $matchData['venue']->id,
                'match_score' => $matchData['score'],
                'status' => 'notified',
                'notified_at' => now(),
            ]);

            SendLeadOpportunityNotification::dispatch(
                $lead,
                $matchData['venue'],
            );
        }

        $venuesList = $venueNames->map(fn (string $name) => "venue: {$name}")->implode(', ');
        DebugLogService::logEmailSent('lead_opportunity', [
            'lead_id' => $lead->id,
            'lead_email' => $lead->email,
            'venues' => $venuesList,
        ]);

        $lead->status = 'distributed';
        $lead->distributed_at = now();
        $lead->save();
    }

    private function notifyAdminLowMatches(Lead $lead, int $matchCount): void
    {
        // TODO: Send email + SMS alert to admin
        // For now, log it
        logger()->warning("Low venue matches for lead #{$lead->id}: {$matchCount} matches found");
    }
}
