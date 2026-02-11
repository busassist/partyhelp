<?php

namespace App\Jobs;

use App\Jobs\SendLeadOpportunityNotification;
use App\Models\DiscountSetting;
use App\Models\Lead;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessLeadDiscounts implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $discountSettings = DiscountSetting::where('is_active', true)
            ->orderByRaw('hours_elapsed ASC, minutes_elapsed ASC')
            ->get();

        foreach ($discountSettings as $setting) {
            $cutoff = now()
                ->subHours((int) $setting->hours_elapsed)
                ->subMinutes((int) ($setting->minutes_elapsed ?? 0));
            $leads = Lead::whereIn('status', ['distributed', 'partially_fulfilled'])
                ->where('distributed_at', '<=', $cutoff)
                ->where('discount_percent', '<', $setting->discount_percent)
                ->whereNotNull('expires_at')
                ->where('expires_at', '>', now())
                ->get();

            foreach ($leads as $lead) {
                $lead->discount_percent = $setting->discount_percent;
                $lead->current_price = round(
                    $lead->base_price * (1 - $setting->discount_percent / 100),
                    2
                );
                $lead->save();

                if ($setting->resend_notification) {
                    $this->resendToMatchedVenues($lead, (int) $setting->discount_percent);
                    Log::info("Discount applied to lead #{$lead->id}: {$setting->discount_percent}%");
                }
            }
        }
    }

    /** Send discount opportunity email to matched venues that have not purchased this lead. */
    private function resendToMatchedVenues(Lead $lead, int $discountPercent): void
    {
        $purchasedVenueIds = $lead->purchases()->pluck('venue_id')->all();
        $lead->matches()
            ->with('venue')
            ->whereNotIn('venue_id', $purchasedVenueIds)
            ->get()
            ->each(function ($match) use ($lead, $discountPercent) {
                if ($match->venue && $match->venue->contact_email) {
                    SendLeadOpportunityNotification::dispatch($lead, $match->venue, $discountPercent);
                }
            });
    }
}
