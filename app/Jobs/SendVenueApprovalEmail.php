<?php

namespace App\Jobs;

use App\Mail\VenueApprovalEmail;
use App\Models\Venue;
use App\Services\ApiHealthService;
use App\Services\DebugLogService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendVenueApprovalEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Venue $venue,
    ) {}

    public function handle(): void
    {
        $to = config('partyhelp.admin_email');
        if (! is_string($to) || trim($to) === '') {
            Log::warning('Venue approval email skipped: no admin_email configured');

            return;
        }
        $to = trim($to);

        try {
            Mail::mailer('sendgrid')->to($to)->send(new VenueApprovalEmail($this->venue));
        } catch (\Throwable $e) {
            ApiHealthService::logError('sendgrid', $e->getMessage(), ['context' => 'venue_approval', 'venue_id' => $this->venue->id, 'to' => $to]);
            throw $e;
        }

        Log::info('Venue approval email sent to admin', [
            'venue_id' => $this->venue->id,
            'business_name' => $this->venue->business_name,
        ]);

        DebugLogService::logEmailSent('venue_approval', [
            'venue' => $this->venue->business_name,
            'venue_id' => $this->venue->id,
            'to' => $to,
        ]);
    }
}
