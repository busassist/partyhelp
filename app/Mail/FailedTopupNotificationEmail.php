<?php

namespace App\Mail;

use App\Models\Venue;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FailedTopupNotificationEmail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Venue $venue,
        public float $attemptedAmount,
        public ?string $failureReason = null,
    ) {}

    public function envelope(): Envelope
    {
        $from = config('mail.from');

        return new Envelope(
            subject: 'Your Partyhelp credit top-up could not be processed',
            from: new Address($from['address'], $from['name'] ?? 'Partyhelp'),
        );
    }

    public function content(): Content
    {
        $venueName = $this->venue->business_name ?? $this->venue->name ?? 'there';
        $updatePaymentUrl = config('app.url') . '/venue/billing?tab=payment-methods';
        $amountFormatted = number_format($this->attemptedAmount, 2);

        $template = \App\Models\EmailTemplate::where('key', 'failed_topup_notification')->first();
        $slots = $template?->content_slots ?? [];

        return new Content(
            view: 'emails.failed-topup-notification',
            with: array_merge($slots, [
                'venueName' => $venueName,
                'attemptedAmount' => $amountFormatted,
                'failureReason' => $this->failureReason ?? 'Payment could not be processed.',
                'updatePaymentUrl' => $updatePaymentUrl,
            ])
        );
    }
}
