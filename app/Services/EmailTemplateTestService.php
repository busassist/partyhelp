<?php

namespace App\Services;

use App\Mail\AdditionalServicesEmail;
use App\Mail\FormConfirmationEmail;
use App\Mail\LeadExpiryEmail;
use App\Mail\VenueIntroductionEmail;
use App\Models\CreditTransaction;
use App\Models\EmailTemplate;
use App\Models\Lead;
use App\Models\Media;
use App\Models\Venue;
use Illuminate\Support\Facades\Mail;

/**
 * Sends test emails for email templates using Blade mailables and the default mailer (e.g. Mailgun).
 * No external template sync required.
 */
class EmailTemplateTestService
{
    /**
     * Send a test email for the given template to the given address.
     * Uses sample data and the appropriate Mailable. Not all template keys are supported.
     *
     * @throws \RuntimeException If template key is not supported or required data is missing.
     */
    public function sendTest(EmailTemplate $template, string $toEmail): void
    {
        $mailable = $this->buildTestMailable($template);
        if ($mailable === null) {
            throw new \RuntimeException("Test send is not available for template key: {$template->key}.");
        }

        Mail::to($toEmail)->send($mailable);
    }

    /**
     * Build the appropriate Mailable with sample data for the template key, or null if unsupported.
     */
    private function buildTestMailable(EmailTemplate $template): ?\Illuminate\Mail\Mailable
    {
        $appUrl = config('app.url');
        $viewUrl = $appUrl . '/emails/view/test';
        $unsubUrl = $appUrl . '/unsubscribe?token=test';

        return match ($template->key) {
            'form_confirmation' => new FormConfirmationEmail(
                customerName: 'Test Customer',
                websiteUrl: config('partyhelp.public_website_url', $appUrl),
                viewInBrowserUrl: $viewUrl,
                unsubscribeUrl: $unsubUrl,
            ),
            'venue_introduction' => new VenueIntroductionEmail(
                customerName: 'Test Customer',
                location: 'INNER NORTH - Carlton, Collingwood, Fitzroy',
                venues: $this->sampleVenues(),
                viewInBrowserUrl: $viewUrl,
                unsubscribeUrl: $unsubUrl,
            ),
            'venue_set_password' => $this->buildVenueSetPasswordTest(),
            'invoice_receipt' => $this->buildInvoiceReceiptTest(),
            'lead_opportunity', 'lead_opportunity_discount' => $this->buildLeadOpportunityTest($template->key),
            'new_venue_for_approval' => $this->buildVenueApprovalTest(),
            'venue_registration_approved' => $this->buildVenueRegistrationApprovedTest(),
            'lead_expiry' => $this->buildLeadExpiryTest(),
            'additional_services' => $this->buildAdditionalServicesTest(),
            default => null,
        };
    }

    private function sampleVenues(): array
    {
        $placeholder = 'https://via.placeholder.com/600x450/1e0f3d/9ca3af?text=Room';
        $imageUrls = Media::orderByDesc('created_at')->take(6)->get()->map(fn ($m) => url('/media/' . $m->file_path))->values();
        $next = fn () => $imageUrls->isEmpty() ? $placeholder : $imageUrls->shift();

        return [
            [
                'venue_name' => 'Sample Venue One',
                'venue_area' => 'Carlton',
                'contact_name' => 'Jane',
                'contact_phone' => '03 9123 4567',
                'email' => 'functions@sample.com',
                'website' => 'https://sample.com',
                'room_hire' => '$500',
                'minimum_spend' => '$2000',
                'rooms' => [
                    ['room_name' => 'Main Room', 'description' => 'Spacious.', 'image_url' => $next(), 'capacity_min' => 20, 'capacity_max' => 80],
                ],
            ],
            [
                'venue_name' => 'Sample Venue Two',
                'venue_area' => 'Fitzroy',
                'contact_name' => 'Coordinator',
                'contact_phone' => '03 9999 0000',
                'email' => 'bookings@sample2.com',
                'website' => 'https://sample2.com',
                'room_hire' => '$0',
                'minimum_spend' => '$1500',
                'rooms' => [
                    ['room_name' => 'Function Space', 'description' => 'Great for events.', 'image_url' => $next(), 'capacity_min' => 30, 'capacity_max' => 120],
                ],
            ],
        ];
    }

    private function buildVenueSetPasswordTest(): ?\App\Mail\VenueSetPasswordEmail
    {
        $venue = Venue::first();
        if (! $venue) {
            return null;
        }

        return new \App\Mail\VenueSetPasswordEmail(
            $venue,
            $this->appUrl() . '/venue/set-password?token=test&email=' . urlencode($venue->contact_email ?? 'venue@example.com'),
        );
    }

    private function buildInvoiceReceiptTest(): ?\App\Mail\VenueReceiptEmail
    {
        $venue = Venue::first();
        $transaction = CreditTransaction::first();
        if (! $venue || ! $transaction) {
            return null;
        }

        return new \App\Mail\VenueReceiptEmail($venue, $transaction);
    }

    private function buildLeadOpportunityTest(string $key): ?\App\Mail\LeadOpportunityEmail
    {
        $lead = Lead::first();
        $venue = Venue::first();
        if (! $lead || ! $venue) {
            return null;
        }

        $discountPercent = $key === 'lead_opportunity_discount' ? 10 : 0;

        return new \App\Mail\LeadOpportunityEmail($lead, $venue, $discountPercent);
    }

    private function buildVenueApprovalTest(): ?\App\Mail\VenueApprovalEmail
    {
        $venue = Venue::first();
        if (! $venue) {
            return null;
        }

        return new \App\Mail\VenueApprovalEmail($venue);
    }

    private function buildVenueRegistrationApprovedTest(): ?\App\Mail\VenueRegistrationApprovedEmail
    {
        $venue = Venue::first();
        if (! $venue) {
            return null;
        }

        return new \App\Mail\VenueRegistrationApprovedEmail($venue);
    }

    private function buildLeadExpiryTest(): ?LeadExpiryEmail
    {
        $lead = Lead::first();
        if (! $lead) {
            return null;
        }

        $appUrl = config('app.url');

        return new LeadExpiryEmail($lead, $appUrl . '/emails/view/test', $appUrl . '/unsubscribe?token=test');
    }

    private function buildAdditionalServicesTest(): ?AdditionalServicesEmail
    {
        $lead = Lead::first();
        if (! $lead) {
            return null;
        }

        $appUrl = config('app.url');

        return new AdditionalServicesEmail($lead, $appUrl . '/emails/view/test', $appUrl . '/unsubscribe?token=test');
    }

    private function appUrl(): string
    {
        return config('app.url');
    }
}
