<?php

namespace App\Services;

use App\Models\EmailTemplate;
use App\Models\Lead;
use App\Models\Venue;
use Illuminate\Support\Facades\Http;

class SendGridTestEmailService
{
    private string $baseUrl = 'https://api.sendgrid.com/v3';

    private string $apiKey;

    public function __construct(?string $apiKey = null)
    {
        $this->apiKey = $apiKey ?? config('services.sendgrid.api_key', '');
    }

    /**
     * Send a test email for the given template to the given address.
     * Uses sample dynamic data. Requires template to be synced to SendGrid first.
     *
     * @throws \RuntimeException If template has no SendGrid ID or API fails.
     */
    public function sendTest(EmailTemplate $template, string $toEmail): void
    {
        $templateId = $template->sendgrid_template_id;
        if (empty($templateId)) {
            throw new \RuntimeException('This template has not been synced to SendGrid yet. Use "Sync to SendGrid" first.');
        }

        $from = config('mail.from');
        $data = $this->sampleDynamicData($template);

        $payload = [
            'personalizations' => [
                [
                    'to' => [['email' => $toEmail]],
                    'dynamic_template_data' => $data,
                ],
            ],
            'from' => [
                'email' => $from['address'],
                'name' => $from['name'] ?? 'Partyhelp',
            ],
            'template_id' => $templateId,
        ];

        $res = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post("{$this->baseUrl}/mail/send", $payload);

        $res->throw();
    }

    /**
     * Build sample dynamic_template_data for the template (fictitious values for testing).
     *
     * @return array<string, mixed>
     */
    public function sampleDynamicData(EmailTemplate $template): array
    {
        $slots = $template->content_slots ?? [];
        $key = $template->key;
        $appUrl = config('app.url');

        $common = [
            'customerName' => 'Test Customer',
            'location' => 'Melbourne CBD',
            'websiteUrl' => $appUrl,
            'viewInBrowserUrl' => $appUrl . '/emails/view/test',
            'unsubscribeUrl' => $appUrl . '/unsubscribe/test',
        ];

        $venueBlock = [
            'venue_name' => 'The Sample Venue',
            'venue_area' => 'Melbourne CBD',
            'contact_name' => 'Jane Functions Manager',
            'contact_phone' => '03 9123 4567',
            'email' => 'functions@samplevenue.com.au',
            'website' => 'https://samplevenue.com.au',
            'room_hire' => '$500',
            'minimum_spend' => '$2000',
            'rooms' => [
                [
                    'room_name' => 'Main Function Room',
                    'description' => 'Spacious room with dance floor and AV.',
                    'image_url' => $appUrl . '/images/placeholder-room.jpg',
                    'capacity_min' => '50',
                    'capacity_max' => '120',
                ],
            ],
        ];

        $base = array_merge($common, ['venues' => [$venueBlock]]);

        $samples = match ($key) {
            'form_confirmation', 'venue_introduction' => $base,
            'no_few_responses_prompt', 'shortlist_check', 'additional_services_lead_expiry' => $common + [
                'occasion' => '21st Birthday',
                'guestCount' => '60',
            ],
            'lead_opportunity' => [
                'occasion' => '21st Birthday',
                'suburb' => 'Richmond',
                'guestCount' => '60',
                'preferredDate' => '2026-03-15',
                'roomStyles' => 'Function Room, Bar',
                'price' => '12.00',
                'purchaseUrl' => $this->sampleLeadPurchaseUrl($appUrl),
                'creditBalance' => '150.00',
                'topUpUrl' => $appUrl . '/venue/billing?tab=buy-credits',
                'viewInBrowserUrl' => $appUrl . '/emails/view/test',
            ],
            'lead_opportunity_discount' => [
                'occasion' => '21st Birthday',
                'suburb' => 'Richmond',
                'guestCount' => '60',
                'preferredDate' => '2026-03-15',
                'roomStyles' => 'Function Room, Bar',
                'price' => '10.80',
                'purchaseUrl' => $this->sampleLeadPurchaseUrl($appUrl),
                'creditBalance' => '150.00',
                'topUpUrl' => $appUrl . '/venue/billing?tab=buy-credits',
                'viewInBrowserUrl' => $appUrl . '/emails/view/test',
                'discountPercent' => '10',
            ],
            'lead_no_longer_available' => [
                'suburb' => 'Richmond',
                'occasion' => '21st Birthday',
                'reason' => 'fulfilled',
                'dashboardUrl' => $appUrl . '/venue/available-leads',
                'viewInBrowserUrl' => $appUrl . '/emails/view/test',
            ],
            'function_pack' => [
                'venueName' => 'Sample Venue Pty Ltd',
                'downloadUrl' => $appUrl . '/venue/function-packs/1/download',
                'expiryNote' => 'Link expires in 30 days.',
                'viewInBrowserUrl' => $appUrl . '/emails/view/test',
            ],
            'failed_topup_notification' => [
                'venueName' => 'Sample Venue Pty Ltd',
                'attemptedAmount' => '50.00',
                'failureReason' => 'Card declined.',
                'updatePaymentUrl' => $appUrl . '/venue/billing?tab=payment-methods',
                'viewInBrowserUrl' => $appUrl . '/emails/view/test',
            ],
            'invoice_receipt' => [
                'venueName' => 'Sample Venue Pty Ltd',
                'documentType' => 'Receipt',
                'invoiceNumber' => 'INV-2026-001',
                'amount' => '50.00',
                'description' => 'Credit top-up $50',
                'viewUrl' => $appUrl . '/venue/billing',
                'viewInBrowserUrl' => $appUrl . '/emails/view/test',
            ],
            'new_venue_for_approval' => [
                'venueName' => 'New Sample Venue',
                'businessName' => 'New Sample Venue Pty Ltd',
                'contactName' => 'Alex Manager',
                'reviewUrl' => $appUrl . '/admin/venues/99',
                'approveUrl' => $appUrl . '/admin/venues/99/approve',
                'rejectUrl' => $appUrl . '/admin/venues/99/reject',
                'viewInBrowserUrl' => $appUrl . '/emails/view/test',
            ],
            'low_match_alert' => [
                'suburb' => 'Richmond',
                'occasion' => 'Wedding Reception',
                'guestCount' => '120',
                'matchCount' => '7',
                'leadId' => '123',
                'dashboardLeadUrl' => $appUrl . '/admin/leads/123',
                'viewInBrowserUrl' => $appUrl . '/emails/view/test',
            ],
            default => $common,
        };

        return array_merge($slots, $samples);
    }

    /**
     * Build a signed lead-purchase URL for test emails. Uses first available lead and venue so the link works when seed data exists.
     */
    private function sampleLeadPurchaseUrl(string $appUrl): string
    {
        $lead = Lead::first();
        $venue = Venue::first();
        if ($lead && $venue) {
            return $lead->signedPurchaseUrlFor($venue);
        }
        return $appUrl . '/venue';
    }
}
