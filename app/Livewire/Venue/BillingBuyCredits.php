<?php

namespace App\Livewire\Venue;

use App\Services\ApiHealthService;
use App\Services\StripeCheckoutService;
use Livewire\Component;

class BillingBuyCredits extends Component
{
    public function getVenue()
    {
        return auth()->user()?->venue;
    }

    public function buyCredits(int $amount): void
    {
        $venue = $this->getVenue();
        if (! $venue) {
            \Filament\Notifications\Notification::make()->title('Error')->body('Venue not found.')->danger()->send();
            return;
        }
        if (! in_array($amount, StripeCheckoutService::allowedAmounts(), true)) {
            \Filament\Notifications\Notification::make()->title('Error')->body('Invalid amount.')->danger()->send();
            return;
        }
        $base = url('/venue/billing');
        $successUrl = $base . '?session_id={CHECKOUT_SESSION_ID}&success=1';
        $cancelUrl = $base . '?cancel=1';
        try {
            $url = app(StripeCheckoutService::class)->createCheckoutSession(
                $venue,
                $amount,
                false,
                $successUrl,
                $cancelUrl
            );
        } catch (\Throwable $e) {
            ApiHealthService::logError('stripe', $e->getMessage(), ['context' => 'billing_buy_credits', 'venue_id' => $venue->id]);
            report($e);
            \Filament\Notifications\Notification::make()->title('Checkout error')->body('Could not start checkout.')->danger()->send();
            return;
        }
        $this->redirect($url);
    }

    public function render()
    {
        return view('livewire.venue.billing-buy-credits', [
            'amounts' => StripeCheckoutService::allowedAmounts(),
        ]);
    }
}
