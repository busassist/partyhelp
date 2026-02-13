<?php

namespace App\Jobs;

use App\Mail\VenueRegistrationApprovedEmail;
use App\Models\Venue;
use App\Services\ApiHealthService;
use App\Services\DebugLogService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendVenueRegistrationApprovedEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Venue $venue,
    ) {}

    public function handle(): void
    {
        $email = $this->venue->contact_email ?? $this->venue->user?->email;
        if (! $email) {
            return;
        }

        try {
            Mail::to($email)->send(new VenueRegistrationApprovedEmail($this->venue));
        } catch (\Throwable $e) {
            ApiHealthService::logError(config('mail.default'), $e->getMessage(), ['context' => 'venue_registration_approved', 'venue_id' => $this->venue->id, 'to' => $email]);
            throw $e;
        }

        DebugLogService::logEmailSent('venue_registration_approved', [
            'venue' => $this->venue->business_name,
            'venue_id' => $this->venue->id,
            'to' => $email,
        ]);
    }
}
