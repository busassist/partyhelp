<?php

namespace App\Mail;

use App\Models\EmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FormConfirmationEmail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $customerName,
        public string $websiteUrl,
        public ?string $viewInBrowserUrl = null,
        public ?string $unsubscribeUrl = null,
    ) {}

    public function envelope(): Envelope
    {
        $from = config('mail.from');

        return new Envelope(
            subject: 'Your tailored list of party venues is on the way!',
            from: new Address($from['address'], $from['name'] ?? ''),
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.form-confirmation', with: $this->bladeViewData());
    }

    /** @return array<string, mixed> */
    private function bladeViewData(): array
    {
        $template = EmailTemplate::where('key', 'form_confirmation')->first();
        $slots = $template?->content_slots ?? [];
        $data = [
            'customerName' => $this->customerName,
            'websiteUrl' => $this->websiteUrl,
            'viewInBrowserUrl' => $this->viewInBrowserUrl,
            'unsubscribeUrl' => $this->unsubscribeUrl,
        ];
        foreach ($slots as $key => $value) {
            if (is_string($value)) {
                $data[$key] = str_replace('{{websiteUrl}}', $this->websiteUrl, $value);
            }
        }

        return $data;
    }
}
