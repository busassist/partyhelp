<?php

namespace App\Services;

use App\Models\EmailTemplate;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\View;

class SendGridTemplateSyncService
{
    private string $baseUrl = 'https://api.sendgrid.com/v3';

    private string $apiKey;

    public function __construct(?string $apiKey = null)
    {
        $this->apiKey = $apiKey ?? config('services.sendgrid.api_key', '');
    }

    public function sync(EmailTemplate $template, bool $force = false): array
    {
        $html = $this->renderTemplateHtml($template);
        $hash = hash('sha256', $html . $template->subject);

        if (!$force && $template->content_hash === $hash) {
            return ['synced' => false, 'reason' => 'No changes detected'];
        }

        $templateId = $template->sendgrid_template_id;

        if (!$templateId) {
            $templateId = $this->createTemplate($template);
            $template->update(['sendgrid_template_id' => $templateId]);
        }

        $this->createVersion($templateId, $template->subject, $html);
        $template->update(['content_hash' => $hash]);

        return ['synced' => true, 'template_id' => $templateId];
    }

    public function syncAll(bool $force = false): array
    {
        $results = [];
        foreach (EmailTemplate::all() as $template) {
            $results[$template->key] = $this->sync($template, $force);
        }
        return $results;
    }

    private function renderTemplateHtml(EmailTemplate $template): string
    {
        $slots = $template->content_slots ?? [];
        $config = config('partyhelp.venue_intro_email', []) + config('partyhelp.form_confirmation_email', []);
        $placeholders = $this->placeholderDataForSync($template->key);
        $data = array_merge($slots, $placeholders, $config);

        $view = $this->viewNameForTemplate($template->key);

        return View::make($view, $data)->render();
    }

    private function viewNameForTemplate(string $key): string
    {
        return match ($key) {
            'form_confirmation' => 'emails.form-confirmation',
            'venue_introduction' => 'emails.venue-introduction',
            'no_few_responses_prompt' => 'emails.no-few-responses-prompt',
            'shortlist_check' => 'emails.shortlist-check',
            'additional_services_lead_expiry' => 'emails.additional-services-lead-expiry',
            'lead_opportunity' => 'emails.lead-opportunity',
            'lead_opportunity_discount' => 'emails.lead-opportunity-discount',
            'lead_no_longer_available' => 'emails.lead-no-longer-available',
            'function_pack' => 'emails.function-pack',
            'failed_topup_notification' => 'emails.failed-topup-notification',
            'invoice_receipt' => 'emails.invoice-receipt',
            'venue_set_password' => 'emails.venue-set-password',
            'venue_registration_approved' => 'emails.venue-registration-approved',
            'new_venue_for_approval' => 'emails.new-venue-for-approval',
            'low_match_alert' => 'emails.low-match-alert',
            default => throw new \InvalidArgumentException("Unknown template key: {$key}"),
        };
    }

    /** @return array<string, mixed> Placeholder values for SendGrid sync (Handlebars-style strings). */
    private function placeholderDataForSync(string $key): array
    {
        $common = [
            'customerName' => '{{customerName}}',
            'location' => '{{location}}',
            'websiteUrl' => '{{websiteUrl}}',
            'viewInBrowserUrl' => '{{viewInBrowserUrl}}',
            'unsubscribeUrl' => '{{unsubscribeUrl}}',
        ];
        $venueBlock = [
            'venue_name' => '{{venue_name}}',
            'venue_area' => '{{venue_area}}',
            'contact_name' => '{{contact_name}}',
            'contact_phone' => '{{contact_phone}}',
            'email' => '{{email}}',
            'website' => '{{website}}',
            'room_hire' => '{{room_hire}}',
            'minimum_spend' => '{{minimum_spend}}',
            'rooms' => [['room_name' => '{{room_name}}', 'description' => '{{description}}', 'image_url' => '{{image_url}}', 'capacity_min' => '{{capacity_min}}', 'capacity_max' => '{{capacity_max}}']],
        ];
        $base = array_merge($common, ['venues' => [$venueBlock]]);

        return match ($key) {
            'form_confirmation', 'venue_introduction' => $base,
            'no_few_responses_prompt', 'shortlist_check', 'additional_services_lead_expiry' => $common + ['occasion' => '{{occasion}}', 'guestCount' => '{{guestCount}}'],
            'lead_opportunity' => [
                'occasion' => '{{occasion}}',
                'suburb' => '{{suburb}}',
                'guestCount' => '{{guestCount}}',
                'preferredDate' => '{{preferredDate}}',
                'roomStyles' => '{{roomStyles}}',
                'price' => '{{price}}',
                'purchaseUrl' => '{{purchaseUrl}}',
                'creditBalance' => '{{creditBalance}}',
                'topUpUrl' => '{{topUpUrl}}',
                'viewInBrowserUrl' => '{{viewInBrowserUrl}}',
            ],
            'lead_opportunity_discount' => [
                'occasion' => '{{occasion}}',
                'suburb' => '{{suburb}}',
                'guestCount' => '{{guestCount}}',
                'preferredDate' => '{{preferredDate}}',
                'roomStyles' => '{{roomStyles}}',
                'price' => '{{price}}',
                'purchaseUrl' => '{{purchaseUrl}}',
                'creditBalance' => '{{creditBalance}}',
                'topUpUrl' => '{{topUpUrl}}',
                'viewInBrowserUrl' => '{{viewInBrowserUrl}}',
                'discountPercent' => '{{discountPercent}}',
            ],
            'lead_no_longer_available' => [
                'suburb' => '{{suburb}}',
                'occasion' => '{{occasion}}',
                'reason' => '{{reason}}',
                'dashboardUrl' => '{{dashboardUrl}}',
                'viewInBrowserUrl' => '{{viewInBrowserUrl}}',
            ],
            'function_pack' => [
                'venueName' => '{{venueName}}',
                'downloadUrl' => '{{downloadUrl}}',
                'expiryNote' => '{{expiryNote}}',
                'viewInBrowserUrl' => '{{viewInBrowserUrl}}',
            ],
            'failed_topup_notification' => [
                'venueName' => '{{venueName}}',
                'attemptedAmount' => '{{attemptedAmount}}',
                'failureReason' => '{{failureReason}}',
                'updatePaymentUrl' => '{{updatePaymentUrl}}',
                'viewInBrowserUrl' => '{{viewInBrowserUrl}}',
            ],
            'venue_set_password' => [
                'venueName' => '{{venueName}}',
                'setPasswordUrl' => '{{setPasswordUrl}}',
                'appUrl' => '{{appUrl}}',
            ],
            'venue_registration_approved' => [
                'venueName' => '{{venueName}}',
                'loginUrl' => '{{loginUrl}}',
            ],
            'invoice_receipt' => [
                'venueName' => '{{venueName}}',
                'documentType' => '{{documentType}}',
                'invoiceNumber' => '{{invoiceNumber}}',
                'amount' => '{{amount}}',
                'description' => '{{description}}',
                'viewUrl' => '{{viewUrl}}',
                'viewInBrowserUrl' => '{{viewInBrowserUrl}}',
            ],
            'new_venue_for_approval' => [
                'venueName' => '{{venueName}}',
                'businessName' => '{{businessName}}',
                'contactName' => '{{contactName}}',
                'reviewUrl' => '{{reviewUrl}}',
                'approveUrl' => '{{approveUrl}}',
                'rejectUrl' => '{{rejectUrl}}',
                'viewInBrowserUrl' => '{{viewInBrowserUrl}}',
            ],
            'low_match_alert' => [
                'suburb' => '{{suburb}}',
                'occasion' => '{{occasion}}',
                'guestCount' => '{{guestCount}}',
                'matchCount' => '{{matchCount}}',
                'leadId' => '{{leadId}}',
                'dashboardLeadUrl' => '{{dashboardLeadUrl}}',
                'viewInBrowserUrl' => '{{viewInBrowserUrl}}',
            ],
            default => $common,
        };
    }


    private function createTemplate(EmailTemplate $template): string
    {
        $res = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post("{$this->baseUrl}/templates", [
            'name' => $template->name,
            'generation' => 'dynamic',
        ]);

        $res->throw();
        return $res->json('id');
    }

    private function createVersion(string $templateId, string $subject, string $html): void
    {
        $res = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post("{$this->baseUrl}/templates/{$templateId}/versions", [
            'name' => 'v' . now()->format('Y-m-d-His'),
            'subject' => $subject,
            'html_content' => $html,
            'active' => 1,
        ]);

        $res->throw();
    }
}
