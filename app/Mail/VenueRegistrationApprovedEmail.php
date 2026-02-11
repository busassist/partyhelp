<?php

namespace App\Mail;

use App\Models\EmailTemplate;
use App\Models\Venue;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VenueRegistrationApprovedEmail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Venue $venue,
    ) {}

    public function envelope(): Envelope
    {
        $template = EmailTemplate::where('key', 'venue_registration_approved')->first();
        $subject = $template?->subject ?? 'Your Partyhelp venue registration has been approved';

        return new Envelope(
            subject: $subject,
            from: new Address(config('mail.from.address'), config('mail.from.name') ?? 'Partyhelp'),
        );
    }

    public function content(): Content
    {
        $template = EmailTemplate::where('key', 'venue_registration_approved')->first();
        $slots = $template?->content_slots ?? [];
        $loginUrl = url('/venue/login');

        return new Content(
            view: 'emails.venue-registration-approved',
            with: array_merge($slots, [
                'venueName' => $this->venue->business_name,
                'loginUrl' => $loginUrl,
            ]),
        );
    }
}
