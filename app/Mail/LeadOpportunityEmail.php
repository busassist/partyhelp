<?php

namespace App\Mail;

use App\Models\Lead;
use App\Models\Venue;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Sichikawa\LaravelSendgridDriver\SendGrid;

class LeadOpportunityEmail extends Mailable
{
    use Queueable, SerializesModels, SendGrid;

    public function __construct(
        public Lead $lead,
        public Venue $venue,
    ) {}

    public function envelope(): Envelope
    {
        $from = config('mail.from');

        return new Envelope(
            subject: 'New lead opportunity – ' . $this->lead->occasion_type . ' – ' . ($this->lead->suburb ?? 'your area'),
            from: new Address($from['address'], $from['name'] ?? ''),
        );
    }

    public function content(): Content
    {
        $templateId = config('services.sendgrid.templates.lead_opportunity');

        if ($templateId) {
            $purchaseUrl = $this->lead->signedPurchaseUrlFor($this->venue);
            $topUpUrl = config('app.url') . '/venue/billing?tab=buy-credits';

            $this->sendgrid([
                'personalizations' => [
                    [
                        'dynamic_template_data' => [
                            'purchaseUrl' => $purchaseUrl,
                            'topUpUrl' => $topUpUrl,
                            'venueName' => $this->venue->business_name,
                            'leadOccasion' => $this->lead->occasion_type,
                            'leadGuestCount' => $this->lead->guest_count,
                            'leadPreferredDate' => $this->lead->preferred_date?->format('j M Y'),
                            'leadSuburb' => is_array($this->lead->suburb) ? implode(', ', $this->lead->suburb) : ($this->lead->suburb ?? ''),
                            'leadPrice' => (string) $this->lead->current_price,
                            'appUrl' => config('app.url'),
                        ],
                    ],
                ],
                'template_id' => $templateId,
            ]);

            return new Content(view: 'emails.sendgrid-dynamic-placeholder');
        }

        $purchaseUrl = $this->lead->signedPurchaseUrlFor($this->venue);
        $topUpUrl = config('app.url') . '/venue/billing?tab=buy-credits';
        $suburb = is_array($this->lead->suburb) ? implode(', ', $this->lead->suburb) : ($this->lead->suburb ?? '');
        $roomStyles = is_array($this->lead->room_styles) ? implode(', ', $this->lead->room_styles) : (is_string($this->lead->room_styles) ? $this->lead->room_styles : '');

        return new Content(view: 'emails.lead-opportunity', with: [
            'purchaseUrl' => $purchaseUrl,
            'topUpUrl' => $topUpUrl,
            'occasion' => $this->lead->occasion_type,
            'suburb' => $suburb,
            'guestCount' => $this->lead->guest_count,
            'preferredDate' => $this->lead->preferred_date?->format('j M Y'),
            'roomStyles' => $roomStyles ?: '—',
            'price' => (string) $this->lead->current_price,
        ]);
    }
}
