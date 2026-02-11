<?php

namespace App\Jobs;

use App\Mail\LowMatchAlertEmail;
use App\Models\Lead;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendLowMatchAlertEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Lead $lead,
        public int $matchCount,
    ) {}

    public function handle(): void
    {
        $to = config('partyhelp.admin_email');
        if (! is_string($to) || trim($to) === '') {
            Log::warning('Low-match alert skipped: no admin_email configured');

            return;
        }

        Mail::mailer('sendgrid')->to(trim($to))->send(new LowMatchAlertEmail($this->lead, $this->matchCount));

        Log::info('Low-match alert sent to admin', ['lead_id' => $this->lead->id, 'match_count' => $this->matchCount]);
    }
}
