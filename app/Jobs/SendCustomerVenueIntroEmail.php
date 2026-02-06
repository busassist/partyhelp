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

class SendCustomerVenueIntroEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Lead $lead,
        public Venue $venue,
    ) {}

    public function handle(): void
    {
        // TODO: Implement SendGrid email to customer
        // Include venue details, images, function pack links
        Log::info("Customer venue intro email", [
            'lead_id' => $this->lead->id,
            'customer_email' => $this->lead->email,
            'venue' => $this->venue->business_name,
        ]);
    }
}
