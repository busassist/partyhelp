<?php

namespace App\Livewire\Venue;

use App\Models\CreditTransaction;
use Livewire\Component;

class BillingTransactions extends Component
{
    public function getVenue(): ?\App\Models\Venue
    {
        return auth()->user()?->venue;
    }

    public function getTransactions()
    {
        $venue = $this->getVenue();
        if (! $venue) {
            return collect();
        }

        return CreditTransaction::where('venue_id', $venue->id)
            ->orderByDesc('created_at')
            ->limit(100)
            ->get();
    }

    public function typeLabel(string $type): string
    {
        return match ($type) {
            'topup' => 'One-off purchase',
            'auto_topup' => 'Auto top-up',
            'purchase' => 'Lead charge',
            'refund' => 'Refund',
            'adjustment' => 'Adjustment',
            default => ucfirst(str_replace('_', ' ', $type)),
        };
    }

    public function render()
    {
        return view('livewire.venue.billing-transactions', [
            'transactions' => $this->getTransactions(),
            'venue' => $this->getVenue(),
        ]);
    }
}
