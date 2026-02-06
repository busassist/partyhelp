<?php

namespace App\Services;

use App\Jobs\SendCustomerVenueIntroEmail;
use App\Models\Lead;
use App\Models\LeadMatch;
use App\Models\LeadPurchase;
use App\Models\Venue;
use Illuminate\Support\Facades\DB;

class LeadPurchaseService
{
    public function __construct(
        private CreditService $creditService,
    ) {}

    public function purchase(Lead $lead, Venue $venue): LeadPurchase|string
    {
        if (! $lead->isAvailable()) {
            return 'Lead is no longer available';
        }

        $match = LeadMatch::where('lead_id', $lead->id)
            ->where('venue_id', $venue->id)
            ->first();

        if (! $match) {
            return 'You were not matched with this lead';
        }

        if ($match->status === 'purchased') {
            return 'You have already purchased this lead';
        }

        $price = (float) $lead->current_price;

        if (! $venue->hasSufficientCredit($price)) {
            return 'Insufficient credit balance';
        }

        return DB::transaction(function () use ($lead, $venue, $match, $price) {
            $purchase = LeadPurchase::create([
                'lead_id' => $lead->id,
                'venue_id' => $venue->id,
                'lead_match_id' => $match->id,
                'amount_paid' => $price,
                'discount_percent' => $lead->discount_percent,
                'lead_status' => 'pending',
            ]);

            $this->creditService->debit(
                $venue,
                $price,
                "Lead #{$lead->id} purchase",
                $purchase->id,
            );

            $match->update([
                'status' => 'purchased',
                'purchased_at' => now(),
            ]);

            $lead->increment('purchase_count');
            $lead->refresh();

            if ($lead->isFulfilled()) {
                $lead->update([
                    'status' => 'fulfilled',
                    'fulfilled_at' => now(),
                ]);
            } elseif ($lead->purchase_count > 0) {
                $lead->update(['status' => 'partially_fulfilled']);
            }

            $venue->update(['last_activity_at' => now()]);

            SendCustomerVenueIntroEmail::dispatch($lead, $venue);

            return $purchase;
        });
    }
}
