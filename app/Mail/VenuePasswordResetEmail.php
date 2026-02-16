<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VenuePasswordResetEmail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $resetUrl,
        public int $expireMinutes = 60,
        public string $toEmail = '',
    ) {
        $this->mailer('mailgun');
    }

    public function envelope(): Envelope
    {
        $from = config('mail.from');

        return new Envelope(
            to: [$this->toEmail],
            subject: 'Reset your Partyhelp venue password',
            from: new Address($from['address'], $from['name'] ?? 'Partyhelp'),
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.password-reset-venue');
    }
}
