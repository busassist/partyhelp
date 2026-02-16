<?php

namespace App\Services;

use App\Models\EmailTemplate;
use App\Models\Lead;
use App\Models\Venue;
use Twilio\Rest\Client;
use Twilio\Rest\Content\V1\ContentModels;

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
     *
     * @param  string|null  $contentSid  Override Content SID (e.g. from template's twilio_content_sid); else uses config.
     */
    public function sendLeadOpportunityInteractive(Lead $lead, Venue $venue, string $toPhone, ?string $contentSid = null): ?string
    {
        if (! $this->isConfigured()) {
            return null;
        }

        $contentSid = $contentSid ?? config('partyhelp.twilio_lead_opportunity_content_sid');
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
     * Normalize phone to E.164 for WhatsApp.
     * Australian: 0412345678 -> +61412345678.
     * Already international (e.g. +62...): kept as-is with + prefix.
     */
    public function normalizePhoneToE164(string $phone): ?string
    {
        $digits = preg_replace('/\D/', '', $phone);
        if ($digits === '') {
            return null;
        }
        // Already has country code (e.g. +62, +61) – assume E.164
        if (str_starts_with($phone, '+') && strlen($digits) >= 10) {
            return '+' . $digits;
        }
        // Australian local
        if (str_starts_with($digits, '0')) {
            $digits = '61' . substr($digits, 1);
        } elseif (! str_starts_with($digits, '61')) {
            $digits = '61' . $digits;
        }

        return '+' . $digits;
    }

    /**
     * Create the lead-opportunity Accept/Ignore Content Template via Twilio Content API.
     * Uses wording from the given template when provided; otherwise defaults.
     * Returns the Content SID (HX...). For WhatsApp you may still need to submit for approval.
     */
    public function createLeadOpportunityContentTemplate(?EmailTemplate $template = null): ?string
    {
        if (! $this->isConfigured()) {
            return null;
        }

        $defaultBody = "Accept and pay for this lead. **Important: your Partyhelp credits balance will be automatically deducted.** Ignore this message if you do not want to pay for this lead.";
        $body = $template && (string) $template->whatsapp_body !== '' ? $template->whatsapp_body : $defaultBody;
        $acceptLabel = $template && (string) $template->whatsapp_accept_label !== '' ? $template->whatsapp_accept_label : 'Accept';
        $ignoreLabel = $template && (string) $template->whatsapp_ignore_label !== '' ? $template->whatsapp_ignore_label : 'Ignore';
        $acceptLabel = mb_substr($acceptLabel, 0, 25);
        $ignoreLabel = mb_substr($ignoreLabel, 0, 25);

        $payload = [
            'friendly_name' => 'partyhelp_lead_opportunity',
            'language' => 'en',
            'variables' => [
                '1' => 'accept_sample',
                '2' => 'ignore',
            ],
            'types' => [
                'twilio/quick-reply' => [
                    'body' => $body,
                    'actions' => [
                        ['title' => $acceptLabel, 'id' => '{{1}}'],
                        ['title' => $ignoreLabel, 'id' => '{{2}}'],
                    ],
                ],
            ],
        ];

        $request = ContentModels::createContentCreateRequest($payload);
        $content = $this->getClient()->content->v1->contents->create($request);

        return $content->sid;
    }

    /**
     * Fetch message status from Twilio (status, error_code, error_message).
     *
     * @return array{status: string, error_code: int|null, error_message: string|null, to: string|null}|null
     */
    public function getMessageStatus(string $messageSid): ?array
    {
        if (! $this->isConfigured()) {
            return null;
        }

        try {
            $msg = $this->getClient()->messages($messageSid)->fetch();

            return [
                'status' => $msg->status ?? 'unknown',
                'error_code' => $msg->errorCode,
                'error_message' => $msg->errorMessage,
                'to' => $msg->to,
            ];
        } catch (\Throwable $e) {
            return null;
        }
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
