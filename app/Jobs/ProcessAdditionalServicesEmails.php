<?php

namespace App\Jobs;

use App\Models\Lead;
use App\Models\SystemSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessAdditionalServicesEmails implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $hours = (int) SystemSetting::get('additional_services_email_hours', 72);
        $cutoff = now()->subHours($hours);

        Lead::query()
            ->whereNull('additional_services_email_sent_at')
            ->where('created_at', '<=', $cutoff)
            ->whereIn('status', ['distributed', 'partially_fulfilled', 'fulfilled', 'expired'])
            ->each(function (Lead $lead): void {
                SendAdditionalServicesEmail::dispatch($lead);
            });
    }
}
