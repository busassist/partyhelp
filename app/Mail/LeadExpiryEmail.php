<?php

namespace App\Mail;

use App\Models\EmailTemplate;
use App\Models\Lead;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LeadExpiryEmail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Lead $lead,
        public ?string $viewInBrowserUrl = null,
        public ?string $unsubscribeUrl = null,
    ) {}

    public function envelope(): Envelope
    {
        $template = EmailTemplate::where('key', 'lead_expiry')->first();
        $subject = $template?->subject ?? "Your lead window has closed – here's what's next";
        $from = config('mail.from');

        return new Envelope(
            subject: $subject,
            from: new Address($from['address'], $from['name'] ?? ''),
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.lead-expiry', with: $this->viewData());
    }

    /** @return array<string, mixed> */
    private function viewData(): array
    {
        $template = EmailTemplate::where('key', 'lead_expiry')->first();
        $slots = $template?->content_slots ?? [];

        return array_merge([
            'customerName' => $this->lead->first_name,
            'viewInBrowserUrl' => $this->viewInBrowserUrl,
            'unsubscribeUrl' => $this->unsubscribeUrl,
        ], $slots);
    }
}
