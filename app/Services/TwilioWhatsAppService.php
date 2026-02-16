<?php

namespace App\Services;

use App\Models\Lead;
use App\Models\Venue;
use Twilio\Rest\Client;

class TwilioWhatsAppService
{
    private ?Client $client = null;

    public function isConfigured(): bool
    {
        $sid = config('services.twilio.sid');
        $token = config('services.twilio.token');

        return ! empty($sid) && ! empty($token);
    }

    /**
     * Send a WhatsApp message (plain body).
     *
     * @param  string  $to  E.164 number, e.g. +61412345678 (will be prefixed with whatsapp:)
     * @param  string  $body  Message text
     * @return string|null  Message SID or null if not configured / failed
     */
    public function send(string $to, string $body): ?string
    {
        if (! $this->isConfigured()) {
            return null;
        }

        $from = config('services.twilio.whatsapp_from', 'whatsapp:+14155238886');
        $to = str_starts_with($to, 'whatsapp:') ? $to : 'whatsapp:' . $to;

        $message = $this->getClient()->messages->create($to, [
            'from' => $from,
            'body' => $body,
        ]);

        return $message->sid;
    }

    /**
     * Send using a Twilio Content Template (contentSid + contentVariables).
     *
     * @param  string  $to  E.164 number
     * @param  string  $contentSid  Twilio Content Template SID (e.g. HXb5b62575e6e4ff6129ad7c8efe1f983e)
     * @param  array<string, string>  $contentVariables  Template variables as key => value
     * @return string|null  Message SID or null
     */
    public function sendWithContentTemplate(string $to, string $contentSid, array $contentVariables = []): ?string
    {
        if (! $this->isConfigured()) {
            return null;
        }

        $from = config('services.twilio.whatsapp_from', 'whatsapp:+14155238886');
        $to = str_starts_with($to, 'whatsapp:') ? $to : 'whatsapp:' . $to;

        $params = [
            'from' => $from,
            'contentSid' => $contentSid,
        ];
        if ($contentVariables !== []) {
            $params['contentVariables'] = json_encode($contentVariables);
        }

        $message = $this->getClient()->messages->create($to, $params);

        return $message->sid;
    }

    /**
     * Send the lead-opportunity interactive message (Accept / Ignore buttons).
     * Uses a Content Template with variable button payloads {{1}} and {{2}}.
     * When the venue taps Accept, the webhook receives the payload and we reply with the purchase link.
     */
    public function sendLeadOpportunityInteractive(Lead $lead, Venue $venue, string $toPhone): ?string
    {
        if (! $this->isConfigured()) {
            return null;
        }

        $contentSid = config('partyhelp.twilio_lead_opportunity_content_sid');
        if (empty($contentSid)) {
            return null;
        }

        $e164 = $this->normalizePhoneToE164($toPhone);
        if ($e164 === null) {
            return null;
        }

        $contentVariables = [
            '1' => 'accept:' . $lead->id . ':' . $venue->id,
            '2' => 'ignore',
        ];

        return $this->sendWithContentTemplate($e164, $contentSid, $contentVariables);
    }

    /**
     * Normalize Australian phone to E.164 for WhatsApp (e.g. 0412345678 -> +61412345678).
     */
    public function normalizePhoneToE164(string $phone): ?string
    {
        $digits = preg_replace('/\D/', '', $phone);
        if ($digits === '') {
            return null;
        }
        if (str_starts_with($digits, '0')) {
            $digits = '61' . substr($digits, 1);
        } elseif (! str_starts_with($digits, '61')) {
            $digits = '61' . $digits;
        }

        return '+' . $digits;
    }

    private function getClient(): Client
    {
        if ($this->client === null) {
            $this->client = new Client(
                config('services.twilio.sid'),
                config('services.twilio.token')
            );
        }

        return $this->client;
    }
}
