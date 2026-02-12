<?php

namespace App\Console\Commands;

use App\Mail\FormConfirmationEmail;
use App\Services\DebugLogService;
use App\Services\EmailGuard;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendTestFormConfirmationEmail extends Command
{
    protected $signature = 'email:test-form-confirmation
                            {--to= : Recipient email address}
                            {--name=PipTest : Customer first name}
                            {--website=www.partyhelp.com.au : Website where form was submitted}';

    protected $description = 'Send a test Form Confirmation email with sample data';

    public function handle(): int
    {
        $to = $this->option('to') ?? config('mail.from.address');
        $name = $this->option('name');
        $website = $this->option('website');

        $viewInBrowserUrl = config('app.url') . '/emails/form-confirmation/preview?token=sample';
        $unsubscribeUrl = config('app.url') . '/unsubscribe?token=sample';

        if (! EmailGuard::shouldSendTo($to)) {
            $this->warn("Skipped: {$to} is a seed/test address (not sent via SendGrid).");

            return self::SUCCESS;
        }

        $mailable = new FormConfirmationEmail(
            customerName: $name,
            websiteUrl: $website,
            viewInBrowserUrl: $viewInBrowserUrl,
            unsubscribeUrl: $unsubscribeUrl,
        );

        Mail::mailer('sendgrid')->to($to)->send($mailable);

        DebugLogService::logEmailSent('test_form_confirmation', ['to' => $to]);

        $this->info("Test Form Confirmation email sent to: {$to}");

        return self::SUCCESS;
    }
}
