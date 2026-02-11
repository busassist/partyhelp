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
        public int $discountPercent = 0,
    ) {}

    public function envelope(): Envelope
    {
        $from = config('mail.from');
        $occasionLabel = config('partyhelp.occasion_types.' . $this->lead->occasion_type, $this->lead->occasion_type);
        $suburb = is_array($this->lead->suburb) ? implode(', ', $this->lead->suburb) : ($this->lead->suburb ?? 'your area');
        $subject = 'New lead opportunity – ' . $occasionLabel . ' – ' . $suburb;
        if ($this->discountPercent > 0) {
            $subject .= ' – ' . $this->discountPercent . '% off';
        }

        return new Envelope(
            subject: $subject,
            from: new Address($from['address'], $from['name'] ?? ''),
        );
    }

    public function content(): Content
    {
        $templateKey = $this->discountPercent > 0 ? 'lead_opportunity_discount' : 'lead_opportunity';
        $templateId = config('services.sendgrid.templates.' . $templateKey);
        $purchaseUrl = $this->lead->signedPurchaseUrlFor($this->venue);
        $topUpUrl = config('app.url') . '/venue/billing?tab=buy-credits';
        $price = (string) $this->lead->current_price;
        $creditBalance = number_format((float) $this->venue->credit_balance, 2);
        $occasionLabel = config('partyhelp.occasion_types.' . $this->lead->occasion_type, $this->lead->occasion_type);
        $roomStylesLabel = $this->roomStylesToLabels($this->lead->room_styles);
        $suburb = is_array($this->lead->suburb) ? implode(', ', $this->lead->suburb) : ($this->lead->suburb ?? '');
        $ctaButtonLabel = 'Purchase This Lead - $' . $price;
        $footerBalanceText = 'Your credit balance: $' . $creditBalance;
        $introText = $this->discountIntroText('intro_text');
        $discountIntroText = $this->discountIntroText('discount_intro_text');

        $templateData = [
            'purchaseUrl' => $purchaseUrl,
            'topUpUrl' => $topUpUrl,
            'venueName' => $this->venue->business_name,
            'leadOccasion' => $occasionLabel,
            'leadGuestCount' => $this->lead->guest_count_display,
            'leadPreferredDate' => $this->lead->preferred_date?->format('j M Y'),
            'leadSuburb' => $suburb,
            'leadRoomStyles' => $roomStylesLabel,
            'price' => $price,
            'creditBalance' => $creditBalance,
            'ctaButtonLabel' => $ctaButtonLabel,
            'footerBalanceText' => $footerBalanceText,
            'appUrl' => config('app.url'),
            'intro_text' => $introText,
            'discount_intro_text' => $discountIntroText,
            'discountPercent' => (string) $this->discountPercent,
        ];

        if ($templateId) {
            $this->sendgrid([
                'personalizations' => [['dynamic_template_data' => $templateData]],
                'template_id' => $templateId,
            ]);

            return new Content(view: 'emails.sendgrid-dynamic-placeholder');
        }

        $view = $this->discountPercent > 0 ? 'emails.lead-opportunity-discount' : 'emails.lead-opportunity';
        $with = array_merge([
            'purchaseUrl' => $purchaseUrl,
            'topUpUrl' => $topUpUrl,
            'occasion' => $occasionLabel,
            'suburb' => $suburb,
            'guestCount' => $this->lead->guest_count_display,
            'preferredDate' => $this->lead->preferred_date?->format('j M Y'),
            'roomStyles' => $roomStylesLabel ?: '—',
            'price' => $price,
            'creditBalance' => $creditBalance,
            'ctaButtonLabel' => $ctaButtonLabel,
            'footerBalanceText' => $footerBalanceText,
            'intro_text' => $introText,
            'discount_intro_text' => $discountIntroText,
        ], $this->discountPercent > 0 ? ['discountPercent' => $this->discountPercent] : []);

        return new Content(view: $view, with: $with);
    }

    private function discountIntroText(string $slot): string
    {
        if ($this->discountPercent === 0) {
            return $slot === 'intro_text'
                ? 'A new lead matches your venue. Purchase it to receive full customer details and the function pack.'
                : '';
        }
        $template = \App\Models\EmailTemplate::where('key', 'lead_opportunity_discount')->first();
        $slots = $template?->content_slots ?? [];
        $defaults = [
            'intro_text' => 'This lead is still available – now at ' . $this->discountPercent . '% off.',
            'discount_intro_text' => 'This lead is now ' . $this->discountPercent . '% off. Purchase to receive full customer details.',
        ];
        $value = $slots[$slot] ?? $defaults[$slot] ?? '';

        return $this->replacePlaceholders($value);
    }

    private function replacePlaceholders(string $text): string
    {
        $price = (string) $this->lead->current_price;
        $creditBalance = number_format((float) $this->venue->credit_balance, 2);
        $discountPercent = (string) $this->discountPercent;

        return str_replace(
            ['${{price}}', '{{price}}', '${{creditBalance}}', '{{creditBalance}}', '{{discountPercent}}'],
            [$price, $price, $creditBalance, $creditBalance, $discountPercent],
            $text
        );
    }

    /** @param  array<int, string>|null  $roomStyles  Keys from config partyhelp.room_styles */
    private function roomStylesToLabels(?array $roomStyles): string
    {
        if (empty($roomStyles)) {
            return '';
        }
        $labels = [];
        foreach ($roomStyles as $key) {
            $labels[] = config('partyhelp.room_styles.' . $key, $key);
        }

        return implode(', ', $labels);
    }
}
