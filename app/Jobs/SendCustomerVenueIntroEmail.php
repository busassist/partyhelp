<?php

namespace App\Jobs;

use App\Mail\VenueIntroductionEmail;
use App\Models\Lead;
use App\Models\Venue;
use App\Services\EmailGuard;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendCustomerVenueIntroEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Lead $lead,
        public Venue $venue,
    ) {}

    public function handle(): void
    {
        $to = $this->lead->email;
        if (! EmailGuard::shouldSendTo($to)) {
            return;
        }

        $customerName = trim($this->lead->first_name . ' ' . $this->lead->last_name) ?: 'there';
        $location = is_array($this->lead->suburb) ? implode(', ', $this->lead->suburb) : ($this->lead->suburb ?? 'your area');
        $venues = [[
            'venue_name' => $this->venue->business_name,
            'venue_area' => $this->venue->suburb ?? '',
        ]];

        $mailable = new VenueIntroductionEmail(
            customerName: $customerName,
            location: $location,
            venues: $venues,
        );

        Mail::mailer('sendgrid')->to($to)->send($mailable);

        Log::info('Customer venue intro email sent', [
            'lead_id' => $this->lead->id,
            'venue_id' => $this->venue->id,
            'to' => $to,
        ]);
    }
}
