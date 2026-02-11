<?php

namespace App\Jobs;

use App\Mail\LeadOpportunityEmail;
use App\Models\Lead;
use App\Models\Venue;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendLeadOpportunityNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Lead $lead,
        public Venue $venue,
    ) {}

    public function handle(): void
    {
        if ($this->venue->isSeedEmail()) {
            return;
        }

        $to = $this->venue->contact_email;
        if (empty($to)) {
            Log::warning('Lead opportunity skipped: venue has no contact_email', [
                'venue_id' => $this->venue->id,
                'lead_id' => $this->lead->id,
            ]);

            return;
        }

        Mail::mailer('sendgrid')
            ->to($to)
            ->send(new LeadOpportunityEmail($this->lead, $this->venue));

        Log::info('Lead opportunity notification sent via SendGrid', [
            'lead_id' => $this->lead->id,
            'venue_id' => $this->venue->id,
            'to' => $to,
        ]);
    }
}
