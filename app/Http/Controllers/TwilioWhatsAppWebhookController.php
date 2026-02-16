<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Venue;
use App\Services\TwilioWhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class TwilioWhatsAppWebhookController extends Controller
{
    public function __construct(
        private TwilioWhatsAppService $whatsApp
    ) {}

    /**
     * Handle incoming WhatsApp messages (e.g. Accept / Ignore button replies from lead opportunity).
     */
    public function handleIncoming(Request $request): Response
    {
        $from = $request->input('From', '');
        $body = trim((string) $request->input('Body', ''));
        $buttonPayload = trim((string) $request->input('ButtonPayload', ''));

        if ($from === '' || ! $this->whatsApp->isConfigured()) {
            return response('', 200);
        }

        $toPhone = str_replace('whatsapp:', '', $from);

        if ($buttonPayload !== '') {
            $this->handleButtonPayload($toPhone, $buttonPayload);

            return response('', 200);
        }

        return response('', 200);
    }

    private function handleButtonPayload(string $toPhone, string $payload): void
    {
        if ($payload === 'ignore') {
            $this->whatsApp->send($toPhone, 'No problem – you won’t be charged for this lead.');

            return;
        }

        if (! str_starts_with($payload, 'accept:')) {
            return;
        }

        $parts = explode(':', $payload);
        if (count($parts) !== 3) {
            return;
        }

        $leadId = (int) $parts[1];
        $venueId = (int) $parts[2];

        $lead = Lead::find($leadId);
        $venue = Venue::find($venueId);

        if (! $lead || ! $venue) {
            $this->whatsApp->send($toPhone, 'This lead link is no longer valid. Please check your email for the latest link.');

            return;
        }

        $purchaseUrl = $lead->signedPurchaseUrlFor($venue);
        $this->whatsApp->send(
            $toPhone,
            'Click to accept and pay for this lead (your credits will be deducted): ' . $purchaseUrl
        );

        Log::info('WhatsApp lead opportunity: sent purchase link', [
            'lead_id' => $leadId,
            'venue_id' => $venueId,
            'to' => $toPhone,
        ]);
    }
}
