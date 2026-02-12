<?php

namespace App\Jobs;

use App\Mail\LowMatchAlertEmail;
use App\Models\Lead;
use App\Services\ApiHealthService;
use App\Services\DebugLogService;
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

        try {
            Mail::mailer('sendgrid')->to(trim($to))->send(new LowMatchAlertEmail($this->lead, $this->matchCount));
        } catch (\Throwable $e) {
            ApiHealthService::logError('sendgrid', $e->getMessage(), ['context' => 'low_match_alert', 'lead_id' => $this->lead->id, 'to' => trim($to)]);
            throw $e;
        }

        DebugLogService::logEmailSent('low_match_alert', [
            'lead_id' => $this->lead->id,
            'match_count' => $this->matchCount,
            'to' => trim($to),
        ]);

        Log::info('Low-match alert sent to admin', ['lead_id' => $this->lead->id, 'match_count' => $this->matchCount]);
    }
}
