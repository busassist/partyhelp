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
        if (request()->query('success') && ! request()->query('setup')) {
            $venue = $this->getVenue();
            $sessionId = request()->query('session_id');
            if ($venue && $sessionId) {
                try {
                    app(\App\Services\StripeCheckoutService::class)
                        ->processCheckoutSessionFromSuccessPage($sessionId, $venue);
                } catch (\Throwable) {
                    // Already logged or not our session
                }
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
