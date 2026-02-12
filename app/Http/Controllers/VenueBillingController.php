<?php

namespace App\Http\Controllers;

use App\Services\ApiHealthService;
use App\Services\StripeCheckoutService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VenueBillingController extends Controller
{
    public function __construct(
        private StripeCheckoutService $checkoutService,
    ) {}

    public function createCheckoutSession(Request $request): RedirectResponse
    {
        $venue = $this->venue();
        if (! $venue) {
            return redirect()->route('filament.venue.pages.dashboard')
                ->with('error', 'Venue not found.');
        }

        $request->validate([
            'amount' => 'required|integer|in:75,150,250,500',
            'save_for_auto_topup' => 'nullable|boolean',
        ]);

        $amount = (int) $request->input('amount');
        $saveForAuto = (bool) $request->boolean('save_for_auto_topup');

        $base = url('/venue/billing');
        $successUrl = $base . '?session_id={CHECKOUT_SESSION_ID}&success=1';
        $cancelUrl = $base . '?cancel=1';

        try {
            $url = $this->checkoutService->createCheckoutSession(
                $venue,
                $amount,
                $saveForAuto,
                $successUrl,
                $cancelUrl
            );
        } catch (\InvalidArgumentException $e) {
            return redirect()->route('filament.venue.pages.billing')
                ->with('error', $e->getMessage());
        } catch (\RuntimeException $e) {
            return redirect()->route('filament.venue.pages.billing')
                ->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            ApiHealthService::logError('stripe', $e->getMessage(), ['context' => 'create_checkout_session', 'venue_id' => $venue->id ?? null]);
            report($e);
            $message = 'Could not start checkout. Please try again.';
            if (config('app.debug')) {
                $message .= ' ' . $e->getMessage();
            }

            return redirect()->route('filament.venue.pages.billing')
                ->with('error', $message);
        }

        return redirect()->away($url);
    }

    public function createSetupSession(Request $request): RedirectResponse
    {
        $venue = $this->venue();
        if (! $venue) {
            return redirect()->route('filament.venue.pages.dashboard')
                ->with('error', 'Venue not found.');
        }

        $base = url('/venue/billing');
        $successUrl = $base . '?setup=1&success=1';
        $cancelUrl = $base . '?cancel=1';

        try {
            $url = $this->checkoutService->createSetupSession($venue, $successUrl, $cancelUrl);
        } catch (\InvalidArgumentException $e) {
            return redirect()->route('filament.venue.pages.billing')
                ->with('error', $e->getMessage());
        } catch (\RuntimeException $e) {
            return redirect()->route('filament.venue.pages.billing')
                ->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            ApiHealthService::logError('stripe', $e->getMessage(), ['context' => 'create_setup_session', 'venue_id' => $venue->id ?? null]);
            report($e);
            $message = 'Could not start add card. Please try again.';
            if (config('app.debug')) {
                $message .= ' ' . $e->getMessage();
            }

            return redirect()->route('filament.venue.pages.billing')
                ->with('error', $message);
        }

        return redirect()->away($url);
    }

    public function updateAutoTopup(Request $request): RedirectResponse
    {
        $venue = $this->venue();
        if (! $venue) {
            return redirect()->back()->with('error', 'Venue not found.');
        }

        $request->validate([
            'auto_topup_enabled' => 'nullable|boolean',
            'auto_topup_amount' => 'required|numeric|in:75,150,250,500',
        ]);

        $venue->update([
            'auto_topup_enabled' => $request->boolean('auto_topup_enabled'),
            'auto_topup_amount' => (float) $request->input('auto_topup_amount'),
            'auto_topup_threshold' => (float) $request->input('auto_topup_amount'),
        ]);

        return redirect()->route('filament.venue.pages.billing')
            ->with('success', 'Auto top-up settings updated.');
    }

    public function disableAutoTopup(Request $request): RedirectResponse
    {
        $venue = $this->venue();
        if (! $venue) {
            return redirect()->back()->with('error', 'Venue not found.');
        }

        $venue->update(['auto_topup_enabled' => false]);

        return redirect()->route('filament.venue.pages.billing')
            ->with('success', 'Auto top-up disabled.');
    }

    private function venue(): ?\App\Models\Venue
    {
        $user = Auth::user();
        if (! $user) {
            return null;
        }

        return $user->venue;
    }
}
