<?php

namespace App\Jobs;

use App\Mail\FormConfirmationEmail;
use App\Models\Lead;
use App\Services\ApiHealthService;
use App\Services\DebugLogService;
use App\Services\EmailGuard;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendCustomerConfirmationEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Lead $lead,
    ) {}

    public function handle(): void
    {
        $to = $this->lead->email;
        if (! EmailGuard::shouldSendTo($to)) {
            return;
        }

        $customerName = trim($this->lead->first_name . ' ' . $this->lead->last_name) ?: 'there';
        $mailable = new FormConfirmationEmail(
            customerName: $customerName,
            websiteUrl: config('partyhelp.public_website_url'),
        );

        try {
            Mail::mailer('sendgrid')->to($to)->send($mailable);
        } catch (\Throwable $e) {
            ApiHealthService::logError('sendgrid', $e->getMessage(), ['context' => 'form_confirmation', 'lead_id' => $this->lead->id, 'to' => $to]);
            throw $e;
        }

        DebugLogService::logEmailSent('form_confirmation', [
            'lead_id' => $this->lead->id,
            'lead_email' => $to,
        ]);

        Log::info('Customer confirmation email sent', [
            'lead_id' => $this->lead->id,
            'to' => $to,
        ]);
    }
}
