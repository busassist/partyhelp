<?php

namespace App\Services;

use App\Models\CreditTransaction;
use App\Models\Venue;
use Illuminate\Support\Facades\DB;

class CreditService
{
    public function debit(Venue $venue, float $amount, string $description, ?int $leadPurchaseId = null): CreditTransaction
    {
        return DB::transaction(function () use ($venue, $amount, $description, $leadPurchaseId) {
            $venue = Venue::lockForUpdate()->find($venue->id);
            $venue->credit_balance -= $amount;
            $venue->save();

            return CreditTransaction::create([
                'venue_id' => $venue->id,
                'type' => 'purchase',
                'amount' => -$amount,
                'balance_after' => $venue->credit_balance,
                'description' => $description,
                'lead_purchase_id' => $leadPurchaseId,
            ]);
        });
    }

    public function credit(Venue $venue, float $amount, string $type, string $description, ?string $stripePaymentIntentId = null): CreditTransaction
    {
        return DB::transaction(function () use ($venue, $amount, $type, $description, $stripePaymentIntentId) {
            $venue = Venue::lockForUpdate()->find($venue->id);
            $venue->credit_balance += $amount;
            $venue->save();

            return CreditTransaction::create([
                'venue_id' => $venue->id,
                'type' => $type,
                'amount' => $amount,
                'balance_after' => $venue->credit_balance,
                'description' => $description,
                'stripe_payment_intent_id' => $stripePaymentIntentId,
            ]);
        });
    }

    public function refund(Venue $venue, float $amount, string $description, ?int $leadPurchaseId = null): CreditTransaction
    {
        return DB::transaction(function () use ($venue, $amount, $description, $leadPurchaseId) {
            $venue = Venue::lockForUpdate()->find($venue->id);
            $venue->credit_balance += $amount;
            $venue->save();

            return CreditTransaction::create([
                'venue_id' => $venue->id,
                'type' => 'refund',
                'amount' => $amount,
                'balance_after' => $venue->credit_balance,
                'description' => $description,
                'lead_purchase_id' => $leadPurchaseId,
            ]);
        });
    }

    public function adjust(Venue $venue, float $amount, string $adminNote): CreditTransaction
    {
        return DB::transaction(function () use ($venue, $amount, $adminNote) {
            $venue = Venue::lockForUpdate()->find($venue->id);
            $venue->credit_balance += $amount;
            $venue->save();

            return CreditTransaction::create([
                'venue_id' => $venue->id,
                'type' => 'adjustment',
                'amount' => $amount,
                'balance_after' => $venue->credit_balance,
                'description' => 'Admin adjustment',
                'admin_note' => $adminNote,
            ]);
        });
    }

    public function needsTopUp(Venue $venue): bool
    {
        return $venue->auto_topup_enabled
            && $venue->credit_balance < $venue->auto_topup_threshold
            && $venue->stripe_payment_method_id;
    }
}
