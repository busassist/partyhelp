<?php

namespace App\Mail;

use App\Models\CreditTransaction;
use App\Models\EmailTemplate;
use App\Models\Venue;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VenueReceiptEmail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Venue $venue,
        public CreditTransaction $transaction,
    ) {}

    public function envelope(): Envelope
    {
        $from = config('mail.from');
        $template = EmailTemplate::byKey('invoice_receipt');
        $subject = $template?->subject ?? 'Your Partyhelp Receipt #' . $this->transaction->id;
        $data = $this->templateData();
        foreach (['documentType', 'invoiceNumber', 'amount', 'venueName'] as $key) {
            $subject = str_replace('{{' . $key . '}}', (string) ($data[$key] ?? ''), $subject);
        }

        return new Envelope(
            subject: $subject,
            from: new Address($from['address'], $from['name'] ?? 'Partyhelp'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.invoice-receipt',
            with: $this->templateData(),
        );
    }

    /** @return array<string, mixed> */
    private function templateData(): array
    {
        $appUrl = config('app.url');
        $venueName = $this->venue->name ?? $this->venue->business_name ?? 'there';

        return [
            'venueName' => $venueName,
            'documentType' => 'Receipt',
            'invoiceNumber' => (string) $this->transaction->id,
            'amount' => number_format((float) $this->transaction->amount, 2),
            'description' => $this->transaction->description ?? 'Credit purchase',
            'viewUrl' => $appUrl . '/venue/billing',
            'viewInBrowserUrl' => null,
        ];
    }
}
