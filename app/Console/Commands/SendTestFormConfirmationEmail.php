<?php

namespace App\Console\Commands;

use App\Mail\FormConfirmationEmail;
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

        $mailable = new FormConfirmationEmail(
            customerName: $name,
            websiteUrl: $website,
            viewInBrowserUrl: $viewInBrowserUrl,
            unsubscribeUrl: $unsubscribeUrl,
        );

        Mail::to($to)->send($mailable);

        $this->info("Test Form Confirmation email sent to: {$to}");

        return self::SUCCESS;
    }
}
