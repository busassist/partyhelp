<?php

namespace App\Jobs;

use App\Mail\LeadOpportunityEmail;
use App\Models\Lead;
use App\Models\Venue;
use App\Services\ApiHealthService;
use App\Services\DebugLogService;
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
        public int $discountPercent = 0,
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

        try {
            Mail::mailer('sendgrid')
                ->to($to)
                ->send(new LeadOpportunityEmail($this->lead, $this->venue, $this->discountPercent));
        } catch (\Throwable $e) {
            ApiHealthService::logError('sendgrid', $e->getMessage(), ['context' => 'lead_opportunity', 'lead_id' => $this->lead->id, 'venue_id' => $this->venue->id, 'to' => $to]);
            throw $e;
        }

        DebugLogService::logEmailSent('lead_opportunity', [
            'lead_id' => $this->lead->id,
            'lead_email' => $this->lead->email,
            'venue' => $this->venue->business_name,
            'venue_id' => $this->venue->id,
            'to' => $to,
        ]);

        Log::info('Lead opportunity notification sent via SendGrid', [
            'lead_id' => $this->lead->id,
            'venue_id' => $this->venue->id,
            'to' => $to,
        ]);
    }
}
