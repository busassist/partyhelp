<?php

namespace App\Mail;

use App\Models\Venue;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VenueSetPasswordEmail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Venue $venue,
        public string $setPasswordUrl,
    ) {}

    public function envelope(): Envelope
    {
        $from = config('mail.from');

        return new Envelope(
            subject: 'Set your Partyhelp venue portal password',
            from: new Address($from['address'], $from['name'] ?? 'Partyhelp'),
        );
    }

    public function content(): Content
    {
        $venueName = $this->venue->business_name ?? 'your venue';

        return new Content(
            view: 'emails.venue-set-password',
            with: ['venueName' => $venueName, 'setPasswordUrl' => $this->setPasswordUrl]
        );
    }
}
