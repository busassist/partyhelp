<?php

namespace App\Mail;

use App\Models\Lead;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LowMatchAlertEmail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Lead $lead,
        public int $matchCount,
    ) {}

    public function envelope(): Envelope
    {
        $from = config('mail.from');
        $suburb = is_array($this->lead->suburb) ? implode(', ', $this->lead->suburb) : ($this->lead->suburb ?? 'unknown');

        return new Envelope(
            subject: "Low-match alert: {$this->matchCount} venues for lead in {$suburb}",
            from: new Address($from['address'], $from['name'] ?? 'Partyhelp'),
        );
    }

    public function content(): Content
    {
        $suburb = is_array($this->lead->suburb) ? implode(', ', $this->lead->suburb) : ($this->lead->suburb ?? '—');
        $occasionLabel = config('partyhelp.occasion_types.' . $this->lead->occasion_type, $this->lead->occasion_type);
        $dashboardLeadUrl = config('app.url') . '/admin/leads/' . $this->lead->id;

        $template = \App\Models\EmailTemplate::where('key', 'low_match_alert')->first();
        $slots = $template?->content_slots ?? [];

        return new Content(view: 'emails.low-match-alert', with: array_merge($slots, [
            'suburb' => $suburb,
            'occasion' => $occasionLabel,
            'guestCount' => $this->lead->guest_count_display,
            'matchCount' => (string) $this->matchCount,
            'dashboardLeadUrl' => $dashboardLeadUrl,
        ]));
    }
}
