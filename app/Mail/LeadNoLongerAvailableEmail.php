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

class LeadNoLongerAvailableEmail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Lead $lead,
        public Venue $venue,
        public string $reason = 'fulfilled',
    ) {}

    public function envelope(): Envelope
    {
        $from = config('mail.from');
        $sub = $this->lead->suburb;
        $suburb = is_array($sub) ? implode(', ', $sub) : ($sub ?? 'your area');

        return new Envelope(
            subject: 'Lead no longer available - ' . $suburb,
            from: new Address($from['address'], $from['name'] ?? 'Partyhelp'),
        );
    }

    public function content(): Content
    {
        $sub = $this->lead->suburb;
        $suburb = is_array($sub) ? implode(', ', $sub) : ($sub ?? 'this area');
        $occasionLabel = config('partyhelp.occasion_types.' . $this->lead->occasion_type, $this->lead->occasion_type);
        $dashboardUrl = config('app.url') . '/venue/available-leads';

        $template = \App\Models\EmailTemplate::where('key', 'lead_no_longer_available')->first();
        $slots = $template ? ($template->content_slots ?? []) : [];

        $with = array_merge($slots, [
            'suburb' => $suburb,
            'occasion' => $occasionLabel,
            'reason' => $this->reason,
            'dashboardUrl' => $dashboardUrl,
        ]);

        return new Content(view: 'emails.lead-no-longer-available', with: $with);
    }
}
