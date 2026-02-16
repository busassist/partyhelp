<?php

namespace App\Jobs;

use App\Mail\LeadExpiryEmail;
use App\Models\Lead;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendLeadExpiryNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Lead $lead,
    ) {}

    public function handle(): void
    {
        $to = $this->lead->email;
        if (empty($to)) {
            return;
        }

        Mail::to($to)->send(new LeadExpiryEmail($this->lead));
    }
}
