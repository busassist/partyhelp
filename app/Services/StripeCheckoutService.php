<?php

namespace App\Services;

use App\Models\Venue;
use Stripe\Checkout\Session as StripeSession;
use Stripe\StripeClient;

class StripeCheckoutService
{
    private const CREDIT_AMOUNTS = [75, 150, 250, 500];

    public function __construct(
        private CreditService $creditService,
    ) {}

    public static function allowedAmounts(): array
    {
        return self::CREDIT_AMOUNTS;
    }

    public function createCheckoutSession(
        Venue $venue,
        int $amountDollars,
        bool $saveForAutoTopup,
        string $successUrl,
        string $cancelUrl,
    ): string {
        if (! in_array($amountDollars, self::CREDIT_AMOUNTS, true)) {
            throw new \InvalidArgumentException('Invalid credit amount');
        }

        $stripe = $this->stripeClient();
        $params = [
            'mode' => 'payment',
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => config('services.stripe.currency', 'aud'),
                    'product_data' => [
                        'name' => 'Partyhelp credits',
                        'description' => "\${$amountDollars} credit for lead purchases",
                    ],
                    'unit_amount' => $amountDollars * 100,
                ],
                'quantity' => 1,
            ]],
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
            'client_reference_id' => (string) $venue->id,
            'metadata' => [
                'venue_id' => (string) $venue->id,
                'amount_dollars' => (string) $amountDollars,
                'save_for_auto_topup' => $saveForAutoTopup ? '1' : '0',
            ],
        ];

        if ($saveForAutoTopup) {
            $params['payment_intent_data'] = [
                'setup_future_usage' => 'off_session',
            ];
            if ($venue->stripe_customer_id) {
                $params['customer'] = $venue->stripe_customer_id;
            } else {
                $params['customer_email'] = $venue->contact_email;
            }
        }

        $session = $stripe->checkout->sessions->create($params);

        return $session->url;
    }

    public function createSetupSession(Venue $venue, string $successUrl, string $cancelUrl): string
    {
        $stripe = $this->stripeClient();
        $params = [
            'mode' => 'setup',
            'payment_method_types' => ['card'],
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
            'client_reference_id' => (string) $venue->id,
            'metadata' => ['venue_id' => (string) $venue->id],
        ];

        if ($venue->stripe_customer_id) {
            $params['customer'] = $venue->stripe_customer_id;
        } else {
            $params['customer_email'] = $venue->contact_email;
        }

        $session = $stripe->checkout->sessions->create($params);

        return $session->url;
    }

    public function handleCheckoutCompleted(StripeSession $session): void
    {
        $venueId = (int) ($session->metadata['venue_id'] ?? $session->client_reference_id);
        $venue = Venue::find($venueId);
        if (! $venue) {
            return;
        }

        if ($session->mode === 'setup') {
            $this->attachPaymentMethodFromSetup($session, $venue);
            return;
        }

        $amountCents = (int) $session->amount_total;
        $amountDollars = $amountCents / 100;
        $saveForAuto = ($session->metadata['save_for_auto_topup'] ?? '0') === '1';
        $piId = $session->payment_intent ?? null;
        $paymentIntentId = $piId ? (is_string($piId) ? $piId : $piId->id) : null;

        $customerId = $session->customer;
        if (is_object($customerId)) {
            $customerId = $customerId->id ?? null;
        }
        if ($customerId && ! $venue->stripe_customer_id) {
            $venue->update(['stripe_customer_id' => $customerId]);
        }
        if ($saveForAuto && $session->payment_intent) {
            $piId = is_string($session->payment_intent) ? $session->payment_intent : $session->payment_intent->id;
            $pi = $this->stripeClient()->paymentIntents->retrieve($piId);
            $pm = $pi->payment_method;
            if ($pm) {
                $venue->update(['stripe_payment_method_id' => is_string($pm) ? $pm : $pm->id]);
            }
        }

        $this->creditService->credit(
            $venue,
            (float) $amountDollars,
            'topup',
            "Credit purchase \${$amountDollars}",
            $paymentIntentId
        );
    }

    private function attachPaymentMethodFromSetup(StripeSession $session, Venue $venue): void
    {
        $setupIntentId = $session->setup_intent;
        if (! $setupIntentId) {
            return;
        }
        $setupIntent = $this->stripeClient()->setupIntents->retrieve(
            is_string($setupIntentId) ? $setupIntentId : $setupIntentId->id
        );
        $pm = $setupIntent->payment_method;
        if (! $pm) {
            return;
        }
        $paymentMethodId = is_string($pm) ? $pm : $pm->id;
        $customerId = $session->customer;
        if (is_object($customerId)) {
            $customerId = $customerId->id ?? null;
        }
        if ($customerId && ! $venue->stripe_customer_id) {
            $venue->update(['stripe_customer_id' => $customerId]);
        }
        $venue->update(['stripe_payment_method_id' => $paymentMethodId]);
    }

    private function stripeClient(): StripeClient
    {
        $secret = config('services.stripe.secret');
        if (! $secret) {
            throw new \RuntimeException('Stripe secret is not configured.');
        }

        return new StripeClient($secret);
    }
}
