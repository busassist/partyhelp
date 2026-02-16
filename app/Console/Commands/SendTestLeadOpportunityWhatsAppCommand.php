<?php

namespace App\Console\Commands;

use App\Models\Lead;
use App\Models\Venue;
use App\Services\TwilioWhatsAppService;
use Illuminate\Console\Command;

class SendTestLeadOpportunityWhatsAppCommand extends Command
{
    protected $signature = 'whatsapp:test-lead-opportunity
                            {--to= : Recipient phone (E.164 or Australian, e.g. 0412345678)}';

    protected $description = 'Send a test lead-opportunity Accept/Ignore WhatsApp to the given number (or admin_phone)';

    public function handle(): int
    {
        $to = $this->option('to') ?? config('partyhelp.admin_phone');
        if (empty($to)) {
            $this->error('Provide --to= or set PARTYHELP_ADMIN_PHONE in .env');

            return self::FAILURE;
        }

        $service = app(TwilioWhatsAppService::class);
        if (! $service->isConfigured()) {
            $this->error('Twilio is not configured (TWILIO_SID_* and TWILIO_AUTH_TOKEN_* in .env).');

            return self::FAILURE;
        }

        if (empty(config('partyhelp.twilio_lead_opportunity_content_sid'))) {
            $this->error('TWILIO_LEAD_OPPORTUNITY_CONTENT_SID is not set in .env.');

            return self::FAILURE;
        }

        $lead = Lead::query()->whereNotNull('id')->orderByDesc('id')->first();
        $venue = Venue::query()->where('status', 'active')->orderBy('id')->first();

        if (! $lead || ! $venue) {
            $this->error('Need at least one lead and one active venue in the database.');

            return self::FAILURE;
        }

        $e164 = $service->normalizePhoneToE164($to);
        if ($e164 === null) {
            $this->error("Could not normalize phone: {$to}");

            return self::FAILURE;
        }

        $this->info("Sending test lead-opportunity WhatsApp to {$e164} (lead #{$lead->id}, venue #{$venue->id})...");

        try {
            $sid = $service->sendLeadOpportunityInteractive($lead, $venue, $to);
            if ($sid) {
                $this->info("Sent. Message SID: {$sid}");
                return self::SUCCESS;
            }
            $this->warn('No SID returned (check content SID and Twilio logs).');
            return self::FAILURE;
        } catch (\Throwable $e) {
            $this->error($e->getMessage());
            return self::FAILURE;
        }
    }
}
