<?php

namespace App\Jobs;

use App\Models\Lead;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ExpireLeads implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $expiredLeads = Lead::whereIn('status', ['distributed', 'partially_fulfilled'])
            ->where('expires_at', '<=', now())
            ->get();

        foreach ($expiredLeads as $lead) {
            $lead->update(['status' => 'expired']);

            // TODO: Notify customer of expiry
            Log::info("Lead #{$lead->id} expired");
        }
    }
}
