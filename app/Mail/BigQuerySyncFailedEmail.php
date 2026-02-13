<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BigQuerySyncFailedEmail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $errorMessage,
        public string $errorDetail,
        public \DateTimeInterface $startedAt,
    ) {}

    public function envelope(): Envelope
    {
        $from = config('mail.from');

        return new Envelope(
            subject: 'Partyhelp: BigQuery daily sync failed',
            from: new Address($from['address'], $from['name'] ?? 'Partyhelp'),
        );
    }

    public function content(): Content
    {
        $settingsUrl = config('app.url') . '/admin/settings?tab=server-health';
        $detailTruncated = \Illuminate\Support\Str::limit($this->errorDetail, 1500);
        $startedAtFormatted = $this->startedAt->format('Y-m-d H:i:s T');

        return new Content(
            view: 'emails.bigquery-sync-failed',
            with: [
                'errorMessage' => $this->errorMessage,
                'errorDetail' => $detailTruncated,
                'startedAt' => $startedAtFormatted,
                'settingsUrl' => $settingsUrl,
            ]
        );
    }
}
