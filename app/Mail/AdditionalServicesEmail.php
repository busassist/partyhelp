<?php

namespace App\Mail;

use App\Models\AdditionalService;
use App\Models\EmailTemplate;
use App\Models\Lead;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdditionalServicesEmail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Lead $lead,
        public ?string $viewInBrowserUrl = null,
        public ?string $unsubscribeUrl = null,
    ) {}

    public function envelope(): Envelope
    {
        $template = EmailTemplate::where('key', 'additional_services')->first();
        $subject = $template?->subject ?? 'Make your event unforgettable – add these extras';
        $occasion = config('partyhelp.occasion_types')[$this->lead->occasion_type] ?? $this->lead->occasion_type;
        $subject = str_replace('{{occasion}}', $occasion, $subject);
        $from = config('mail.from');

        return new Envelope(
            subject: $subject,
            from: new Address($from['address'], $from['name'] ?? ''),
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.additional-services', with: $this->viewData());
    }

    /** @return array<string, mixed> */
    private function viewData(): array
    {
        $template = EmailTemplate::where('key', 'additional_services')->first();
        $slots = $template?->content_slots ?? [];
        $occasion = config('partyhelp.occasion_types')[$this->lead->occasion_type] ?? $this->lead->occasion_type;

        $additionalServices = AdditionalService::ordered()->get()->map(fn ($s) => [
            'name' => $s->name,
            'thumbnail_url' => $s->thumbnail_url,
        ])->all();

        $tagline = $slots['tagline'] ?? 'Make your {{occasion}} unforgettable with these extras';
        $tagline = str_replace('{{occasion}}', $occasion, $tagline);

        return array_merge($slots, [
            'customerName' => $this->lead->first_name,
            'tagline' => $tagline,
            'additionalServices' => $additionalServices,
            'additionalServicesUrl' => $this->lead->signedAdditionalServicesUrl(),
            'viewInBrowserUrl' => $this->viewInBrowserUrl,
            'unsubscribeUrl' => $this->unsubscribeUrl,
        ]);
    }
}
