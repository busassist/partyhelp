<?php

namespace App\Mail;

use App\Models\Venue;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class VenueApprovalEmail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Venue $venue,
    ) {}

    public function envelope(): Envelope
    {
        $from = config('mail.from');

        return new Envelope(
            subject: 'New venue pending approval: ' . $this->venue->business_name,
            from: new Address($from['address'], $from['name'] ?? 'Partyhelp'),
        );
    }

    public function content(): Content
    {
        $reviewUrl = url('/admin/venues/' . $this->venue->id . '/edit');
        $approveUrl = URL::signedRoute('venue.approve', ['venue' => $this->venue]);
        $rejectUrl = URL::signedRoute('venue.reject', ['venue' => $this->venue]);

        return new Content(
            view: 'emails.new-venue-for-approval',
            with: [
                'venueName' => $this->venue->business_name,
                'businessName' => $this->venue->business_name,
                'contactName' => $this->venue->contact_name ?? $this->venue->contact_email ?? '—',
                'reviewUrl' => $reviewUrl,
                'approveUrl' => $approveUrl,
                'rejectUrl' => $rejectUrl,
            ],
        );
    }
}
