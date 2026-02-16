<?php

namespace App\Jobs;

use App\Mail\LeadOpportunityEmail;
use App\Models\EmailTemplate;
use App\Models\Lead;
use App\Models\Venue;
use App\Services\ApiHealthService;
use App\Services\DebugLogService;
use App\Services\TwilioWhatsAppService;
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
            Mail::to($to)->send(new LeadOpportunityEmail($this->lead, $this->venue, $this->discountPercent));
        } catch (\Throwable $e) {
            ApiHealthService::logError(config('mail.default'), $e->getMessage(), ['context' => 'lead_opportunity', 'lead_id' => $this->lead->id, 'venue_id' => $this->venue->id, 'to' => $to]);
            throw $e;
        }

        DebugLogService::logEmailSent('lead_opportunity', [
            'lead_id' => $this->lead->id,
            'lead_email' => $this->lead->email,
            'venue' => $this->venue->business_name,
            'venue_id' => $this->venue->id,
            'to' => $to,
        ]);

        $this->sendWhatsAppIfEnabled();

        Log::info('Lead opportunity notification sent', [
            'lead_id' => $this->lead->id,
            'venue_id' => $this->venue->id,
            'to' => $to,
        ]);
    }

    private function sendWhatsAppIfEnabled(): void
    {
        $templateKey = $this->discountPercent > 0 ? 'lead_opportunity_discount' : 'lead_opportunity';
        $template = EmailTemplate::where('key', $templateKey)->first();
        if (! $template?->send_via_whatsapp) {
            return;
        }

        $phone = $this->venue->contact_phone;
        if (empty($phone)) {
            return;
        }

        $whatsApp = app(TwilioWhatsAppService::class);
        if (! $whatsApp->isConfigured() || config('partyhelp.twilio_lead_opportunity_content_sid') === null) {
            return;
        }

        try {
            $sid = $whatsApp->sendLeadOpportunityInteractive($this->lead, $this->venue, $phone);
            if ($sid !== null) {
                Log::info('Lead opportunity WhatsApp sent', [
                    'lead_id' => $this->lead->id,
                    'venue_id' => $this->venue->id,
                    'message_sid' => $sid,
                ]);
            }
        } catch (\Throwable $e) {
            Log::warning('Lead opportunity WhatsApp failed', [
                'lead_id' => $this->lead->id,
                'venue_id' => $this->venue->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
