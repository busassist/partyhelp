<?php

namespace App\Jobs;

use App\Models\Lead;
use App\Models\Venue;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendLeadOpportunityNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Lead $lead,
        public Venue $venue,
    ) {}

    public function handle(): void
    {
        // TODO: Implement SendGrid email
        // TODO: Implement SMS via Twilio/MessageMedia
        Log::info("Lead opportunity notification", [
            'lead_id' => $this->lead->id,
            'venue_id' => $this->venue->id,
            'occasion' => $this->lead->occasion_type,
            'suburb' => $this->lead->suburb,
            'price' => $this->lead->current_price,
        ]);
    }
}
