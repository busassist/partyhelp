<?php

namespace App\Livewire\Venue;

use App\Services\StripeCheckoutService;
use Livewire\Component;

class BillingPaymentMethods extends Component
{
    public bool $autoTopupEnabled = false;

    public string $autoTopupAmount = '150';

    protected $rules = [
        'autoTopupAmount' => 'required|in:75,150,250,500',
    ];

    public function mount(): void
    {
        $venue = $this->getVenue();
        if ($venue) {
            $this->autoTopupEnabled = (bool) $venue->auto_topup_enabled;
            $this->autoTopupAmount = (string) (int) $venue->auto_topup_amount ?: '150';
        }
    }

    public function getVenue()
    {
        return auth()->user()?->venue;
    }

    public function addCard(): void
    {
        $url = route('venue.billing.create-setup-session');
        $this->redirect($url);
    }

    public function updateAutoTopup(): void
    {
        $this->validate();
        $venue = $this->getVenue();
        if (! $venue) {
            return;
        }
        $amount = (float) $this->autoTopupAmount;
        $venue->update([
            'auto_topup_enabled' => $this->autoTopupEnabled,
            'auto_topup_amount' => $amount,
            'auto_topup_threshold' => $amount,
        ]);
        \Filament\Notifications\Notification::make()->title('Auto top-up updated')->success()->send();
    }

    public function disableAutoTopup(): void
    {
        $venue = $this->getVenue();
        if (! $venue) {
            return;
        }
        $venue->update(['auto_topup_enabled' => false]);
        $this->autoTopupEnabled = false;
        \Filament\Notifications\Notification::make()->title('Auto top-up disabled')->success()->send();
    }

    public function render()
    {
        $venue = $this->getVenue();
        $hasCard = $venue && $venue->stripe_payment_method_id;
        $amounts = StripeCheckoutService::allowedAmounts();

        return view('livewire.venue.billing-payment-methods', [
            'venue' => $venue,
            'hasCard' => $hasCard,
            'amounts' => $amounts,
        ]);
    }
}
