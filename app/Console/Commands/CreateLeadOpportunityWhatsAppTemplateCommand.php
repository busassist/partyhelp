<?php

namespace App\Console\Commands;

use App\Models\EmailTemplate;
use App\Services\TwilioWhatsAppService;
use Illuminate\Console\Command;

class CreateLeadOpportunityWhatsAppTemplateCommand extends Command
{
    protected $signature = 'whatsapp:create-lead-opportunity-template
                            {--save : Save the returned Content SID to the lead_opportunity template}';

    protected $description = 'Create the lead-opportunity Accept/Ignore Content Template in Twilio using template wording; optionally save SID to template';

    public function handle(): int
    {
        $service = app(TwilioWhatsAppService::class);
        if (! $service->isConfigured()) {
            $this->error('Twilio is not configured (TWILIO_SID_* and TWILIO_AUTH_TOKEN_* in .env).');

            return self::FAILURE;
        }

        $template = EmailTemplate::where('key', 'lead_opportunity')->first();
        if (! $template) {
            $this->error('No email template with key lead_opportunity found.');

            return self::FAILURE;
        }

        $this->info('Creating Content Template in Twilio (using wording from Manage Emails template)...');

        try {
            $sid = $service->createLeadOpportunityContentTemplate($template);
            if (! $sid) {
                $this->error('Twilio did not return a Content SID.');

                return self::FAILURE;
            }

            $this->info("Content SID: {$sid}");

            if ($this->option('save')) {
                $template->twilio_content_sid = $sid;
                $template->save();
                $this->info('Saved to lead_opportunity template (twilio_content_sid). You can send test with: php artisan whatsapp:test-lead-opportunity --to=+61...');
            } else {
                $this->line('To save this SID to the template so test/live sending works without .env, run with --save');
            }

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }
    }
}
