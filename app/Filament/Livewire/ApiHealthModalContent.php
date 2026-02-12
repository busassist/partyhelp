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

    public function testSendGrid(): void
    {
        $this->testResult = null;
        $key = config('services.sendgrid.api_key');
        if (empty($key)) {
            $this->testResult = 'SendGrid: Not configured (no API key).';

            return;
        }
        try {
            $response = Http::withToken($key)
                ->timeout(10)
                ->get('https://api.sendgrid.com/v3/user/account');
            if ($response->successful()) {
                $this->testResult = 'SendGrid: Connection OK.';
            } else {
                $this->testResult = 'SendGrid: API returned ' . $response->status() . ' — ' . \Illuminate\Support\Str::limit($response->body(), 100);
            }
        } catch (\Throwable $e) {
            $this->testResult = 'SendGrid: ' . $e->getMessage();
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
