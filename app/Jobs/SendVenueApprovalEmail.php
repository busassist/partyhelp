<?php

namespace App\Jobs;

use App\Models\Venue;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendVenueApprovalEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Venue $venue,
    ) {}

    public function handle(): void
    {
        // TODO: Send "New venue for approval" email to admin
        // Include: Review vendor, Approve, Reject buttons
        Log::info("Venue approval email queued", [
            'venue_id' => $this->venue->id,
            'business_name' => $this->venue->business_name,
        ]);
    }
}
