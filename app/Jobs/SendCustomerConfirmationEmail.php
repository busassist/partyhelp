<?php

namespace App\Jobs;

use App\Mail\FormConfirmationEmail;
use App\Models\Lead;
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
            websiteUrl: config('app.url'),
        );

        Mail::mailer('sendgrid')->to($to)->send($mailable);

        Log::info('Customer confirmation email sent', [
            'lead_id' => $this->lead->id,
            'to' => $to,
        ]);
    }
}
