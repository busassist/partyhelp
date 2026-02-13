<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VenueIntroductionEmail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $customerName,
        public string $location,
        public array $venues,
        public ?string $viewInBrowserUrl = null,
        public ?string $unsubscribeUrl = null,
    ) {}

    public function envelope(): Envelope
    {
        $from = config('mail.from');

        return new Envelope(
            subject: 'Your personalised venue recommendations from Partyhelp',
            from: new Address($from['address'], $from['name'] ?? ''),
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.venue-introduction');
    }
}
