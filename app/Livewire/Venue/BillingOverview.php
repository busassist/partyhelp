<?php

namespace App\Livewire\Venue;

use App\Models\LeadPurchase;
use Livewire\Component;

class BillingOverview extends Component
{
    public function mount(): void
    {
        if (session('error')) {
            \Filament\Notifications\Notification::make()
                ->title('Error')
                ->body(session('error'))
                ->danger()
                ->send();
            session()->forget('error');
            return;
        }
        if (request()->query('cancel')) {
            \Filament\Notifications\Notification::make()
                ->title('Checkout cancelled')
                ->body('You can try again when ready.')
                ->warning()
                ->send();
            return;
        }
        if (request()->query('success')) {
            if (request()->query('setup')) {
                \Filament\Notifications\Notification::make()
                    ->title('Card added')
                    ->body('Your payment method has been saved.')
                    ->success()
                    ->send();
            } else {
                \Filament\Notifications\Notification::make()
                    ->title('Payment successful')
                    ->body('Your credits have been added.')
                    ->success()
                    ->send();
            }
        }
    }

    public function getVenue()
    {
        return auth()->user()?->venue;
    }

    public function render()
    {
        $venue = $this->getVenue();
        if (! $venue) {
            return view('livewire.venue.billing-overview', ['venue' => null, 'spentThisMonth' => 0, 'purchasesThisMonth' => 0]);
        }

        $spentThisMonth = LeadPurchase::where('venue_id', $venue->id)
            ->whereMonth('created_at', now()->month)
            ->sum('amount_paid');
        $purchasesThisMonth = LeadPurchase::where('venue_id', $venue->id)
            ->whereMonth('created_at', now()->month)
            ->count();

        return view('livewire.venue.billing-overview', [
            'venue' => $venue,
            'spentThisMonth' => $spentThisMonth,
            'purchasesThisMonth' => $purchasesThisMonth,
        ]);
    }
}
