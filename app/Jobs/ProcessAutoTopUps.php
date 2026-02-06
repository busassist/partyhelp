<?php

namespace App\Jobs;

use App\Models\Venue;
use App\Services\CreditService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessAutoTopUps implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(CreditService $creditService): void
    {
        $venues = Venue::where('status', 'active')
            ->where('auto_topup_enabled', true)
            ->whereNotNull('stripe_payment_method_id')
            ->whereColumn('credit_balance', '<', 'auto_topup_threshold')
            ->get();

        foreach ($venues as $venue) {
            try {
                // TODO: Charge via Stripe
                // For now, log intent
                Log::info("Auto top-up needed for venue #{$venue->id}", [
                    'balance' => $venue->credit_balance,
                    'threshold' => $venue->auto_topup_threshold,
                    'amount' => $venue->auto_topup_amount,
                ]);
            } catch (\Exception $e) {
                Log::error("Auto top-up failed for venue #{$venue->id}: {$e->getMessage()}");
            }
        }
    }
}
