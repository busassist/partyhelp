<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Sichikawa\LaravelSendgridDriver\SendGrid;

class VenueIntroductionEmail extends Mailable
{
    use Queueable, SerializesModels, SendGrid;

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
        $templateId = config('services.sendgrid.templates.venue_introduction');

        if ($templateId) {
            $venueIntroConfig = config('partyhelp.venue_intro_email', []);

            $this->sendgrid([
                'personalizations' => [
                    [
                        'dynamic_template_data' => array_merge([
                            'logoUrl' => asset('images/brand/ph-logo-white.png'),
                            'customerName' => $this->customerName,
                            'location' => $this->location,
                            'venues' => $this->venues,
                            'viewInBrowserUrl' => $this->viewInBrowserUrl,
                            'unsubscribeUrl' => $this->unsubscribeUrl,
                            'appUrl' => config('app.url'),
                        ], $venueIntroConfig),
                    ],
                ],
                'template_id' => $templateId,
            ]);

            return new Content(view: 'emails.sendgrid-dynamic-placeholder');
        }

        return new Content(view: 'emails.venue-introduction');
    }
}
