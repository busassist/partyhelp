<?php

namespace App\Filament\Livewire;

use App\Services\ApiHealthService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Http;
use Livewire\Component;

class ApiHealthModalContent extends Component
{
    protected static bool $isDiscovered = false;

    public ?string $testResult = null;

    public function getViewData(): array
    {
        return [
            'errors' => ApiHealthService::recentErrors(20),
        ];
    }

    public function testMailgun(): void
    {
        $this->testResult = null;
        $secret = config('services.mailgun.secret');
        $domain = config('services.mailgun.domain');
        if (empty($secret) || empty($domain)) {
            $this->testResult = 'Mailgun: Not configured (domain or secret missing).';

            return;
        }
        $endpoint = config('services.mailgun.endpoint', 'api.mailgun.net');
        $url = 'https://' . $endpoint . '/v3/domains/' . $domain;
        try {
            $response = Http::withBasicAuth('api', $secret)
                ->timeout(10)
                ->get($url);
            if ($response->successful()) {
                $this->testResult = 'Mailgun: Connection OK.';
            } else {
                $this->testResult = 'Mailgun: API returned ' . $response->status() . ' — ' . \Illuminate\Support\Str::limit($response->body(), 100);
            }
        } catch (\Throwable $e) {
            $this->testResult = 'Mailgun: ' . $e->getMessage();
        }
    }

    public function testStripe(): void
    {
        $this->testResult = null;
        $secret = config('services.stripe.secret');
        if (empty($secret)) {
            $this->testResult = 'Stripe: Not configured (no secret).';

            return;
        }
        try {
            $stripe = new \Stripe\StripeClient($secret);
            $stripe->balance->retrieve();
            $this->testResult = 'Stripe: Connection OK.';
        } catch (\Throwable $e) {
            $this->testResult = 'Stripe: ' . $e->getMessage();
        }
    }

    public function render(): View
    {
        return view('filament.livewire.api-health-modal-content', $this->getViewData());
    }
}
