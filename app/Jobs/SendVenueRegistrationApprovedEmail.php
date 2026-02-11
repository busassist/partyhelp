<?php

namespace App\Jobs;

use App\Mail\VenueRegistrationApprovedEmail;
use App\Models\Venue;
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

        Mail::mailer('sendgrid')->to($email)->send(new VenueRegistrationApprovedEmail($this->venue));
    }
}
