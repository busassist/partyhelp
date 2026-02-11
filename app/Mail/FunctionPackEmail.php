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

class FunctionPackEmail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Lead $lead,
        public Venue $venue,
        public string $downloadUrl,
        public string $expiryNote = 'The download link will expire after 30 days.',
    ) {}

    public function envelope(): Envelope
    {
        $from = config('mail.from');

        return new Envelope(
            subject: 'Your function pack is ready to download',
            from: new Address($from['address'], $from['name'] ?? 'Partyhelp'),
        );
    }

    public function content(): Content
    {
        $venueName = $this->venue->business_name ?? $this->venue->name ?? 'there';
        $template = \App\Models\EmailTemplate::where('key', 'function_pack')->first();
        $slots = $template?->content_slots ?? [];

        return new Content(
            view: 'emails.function-pack',
            with: array_merge($slots, [
                'venueName' => $venueName,
                'downloadUrl' => $this->downloadUrl,
                'expiryNote' => $this->expiryNote,
            ])
        );
    }
}
