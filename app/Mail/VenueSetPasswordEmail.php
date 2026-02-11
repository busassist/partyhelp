<?php

namespace App\Mail;

use App\Models\Venue;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Sichikawa\LaravelSendgridDriver\SendGrid;

class VenueSetPasswordEmail extends Mailable
{
    use Queueable, SerializesModels, SendGrid;

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
        $templateId = config('services.sendgrid.templates.venue_set_password');
        $venueName = $this->venue->business_name ?? 'your venue';

        if ($templateId) {
            $this->sendgrid([
                'personalizations' => [
                    ['dynamic_template_data' => [
                        'venueName' => $venueName,
                        'contactName' => $this->venue->contact_name ?? '',
                        'setPasswordUrl' => $this->setPasswordUrl,
                        'appUrl' => config('app.url'),
                    ]],
                ],
                'template_id' => $templateId,
            ]);

            return new Content(view: 'emails.sendgrid-dynamic-placeholder');
        }

        return new Content(
            view: 'emails.venue-set-password',
            with: ['venueName' => $venueName, 'setPasswordUrl' => $this->setPasswordUrl]
        );
    }
}
