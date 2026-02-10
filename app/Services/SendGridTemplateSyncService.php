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

        $data = array_merge($slots, [
            'useHandlebars' => true,
            'customerName' => '{{customerName}}',
            'location' => '{{location}}',
            'websiteUrl' => '{{websiteUrl}}',
            'venues' => [
                [
                    'venue_name' => '{{venue_name}}',
                    'venue_area' => '{{venue_area}}',
                    'contact_name' => '{{contact_name}}',
                    'contact_phone' => '{{contact_phone}}',
                    'email' => '{{email}}',
                    'website' => '{{website}}',
                    'room_hire' => '{{room_hire}}',
                    'minimum_spend' => '{{minimum_spend}}',
                    'rooms' => [
                        [
                            'room_name' => '{{room_name}}',
                            'description' => '{{description}}',
                            'image_url' => '{{image_url}}',
                            'capacity_min' => '{{capacity_min}}',
                            'capacity_max' => '{{capacity_max}}',
                        ],
                    ],
                ],
            ],
            'viewInBrowserUrl' => '{{viewInBrowserUrl}}',
            'unsubscribeUrl' => '{{unsubscribeUrl}}',
        ], $config);

        $view = match ($template->key) {
            'venue_introduction' => 'emails.venue-introduction',
            'form_confirmation' => 'emails.form-confirmation',
            default => throw new \InvalidArgumentException("Unknown template key: {$template->key}"),
        };

        return View::make($view, $data)->render();
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
