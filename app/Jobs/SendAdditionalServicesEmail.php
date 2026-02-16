<?php

namespace App\Jobs;

use App\Mail\AdditionalServicesEmail;
use App\Models\Lead;
use App\Models\SystemSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendAdditionalServicesEmail implements ShouldQueue
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

        Mail::to($to)->send(new AdditionalServicesEmail($this->lead));
        $this->lead->update(['additional_services_email_sent_at' => now()]);
    }
}
