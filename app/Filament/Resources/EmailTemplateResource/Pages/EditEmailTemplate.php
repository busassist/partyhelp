<?php

namespace App\Filament\Resources\EmailTemplateResource\Pages;

use App\Filament\Resources\EmailTemplateResource;
use App\Services\EmailTemplateTestService;
use App\Services\TwilioWhatsAppService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Exceptions\Halt;

class EditEmailTemplate extends EditRecord
{
    protected static string $resource = EmailTemplateResource::class;

    protected string $view = 'filament.resources.email-template-resource.pages.edit-email-template';

    /** Show modal when save was halted because WhatsApp details changed. */
    public bool $showWhatsappWarningModal = false;

    /** Skip WhatsApp warning and proceed with save (set when user confirms). */
    public bool $confirmWhatsappSave = false;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $slots = config('partyhelp.email_template_slots.' . ($this->record->key ?? ''), []);
        $slotKeys = array_keys($slots);
        if (! empty($slotKeys) && isset($data['content_slots']) && is_array($data['content_slots'])) {
            $data['content_slots'] = array_intersect_key($data['content_slots'], array_flip($slotKeys));
        }

        return $data;
    }

    public function save(bool $shouldRedirect = true, bool $shouldSendSavedNotification = true): void
    {
        if (! $this->confirmWhatsappSave && $this->isLeadOpportunityTemplate()) {
            $data = $this->form->getState(afterValidate: function (): void {
            });
            $data = $this->mutateFormDataBeforeSave($data);
            if ($this->whatsappDetailsChanged($data)) {
                $this->showWhatsappWarningModal = true;
                $this->confirmWhatsappSave = false;

                throw new Halt(shouldRollbackDatabaseTransaction: false);
            }
        }

        $this->confirmWhatsappSave = false;
        parent::save($shouldRedirect, $shouldSendSavedNotification);
    }

    public function confirmSaveWithWhatsappWarning(): void
    {
        $this->showWhatsappWarningModal = false;
        $this->confirmWhatsappSave = true;
        $this->save();
    }

    public function closeWhatsappWarningModal(): void
    {
        $this->showWhatsappWarningModal = false;
    }

    protected function getHeaderActions(): array
    {
        $actions = [
            \Filament\Actions\Action::make('send_test_email')
                ->label('Send test email')
                ->icon('heroicon-o-paper-airplane')
                ->color('primary')
                ->form([
                    \Filament\Forms\Components\TextInput::make('email')
                        ->label('Send test to')
                        ->email()
                        ->required()
                        ->placeholder('admin@example.com'),
                ])
                ->modalHeading('Send test email')
                ->modalDescription('A test email will be sent via the default mailer (Mailgun) using this template with sample data.')
                ->action(function (array $data): void {
                    $this->runSendTest($data['email']);
                }),
        ];

        if ($this->record && $this->isLeadOpportunityTemplate()) {
            $actions[] = \Filament\Actions\Action::make('push_to_twilio')
                ->label('Push to Twilio')
                ->icon('heroicon-o-arrow-up-circle')
                ->color('gray')
                ->action(function (): void {
                    $this->runPushToTwilio();
                })
                ->requiresConfirmation()
                ->modalHeading('Push WhatsApp template to Twilio')
                ->modalDescription('Create or update the lead-opportunity Content Template in Twilio using the current message body and button labels above. The new template may require re-approval before it takes effect.')
                ->modalSubmitActionLabel('Push');
        }

        return $actions;
    }

    private function isLeadOpportunityTemplate(): bool
    {
        return $this->record && in_array($this->record->key, ['lead_opportunity', 'lead_opportunity_discount'], true);
    }

    private function whatsappDetailsChanged(array $data): bool
    {
        $r = $this->record;
        $body = trim((string) ($data['whatsapp_body'] ?? ''));
        $accept = trim((string) ($data['whatsapp_accept_label'] ?? ''));
        $ignore = trim((string) ($data['whatsapp_ignore_label'] ?? ''));

        return $body !== trim((string) ($r->whatsapp_body ?? ''))
            || $accept !== trim((string) ($r->whatsapp_accept_label ?? ''))
            || $ignore !== trim((string) ($r->whatsapp_ignore_label ?? ''));
    }

    private function runPushToTwilio(): void
    {
        $data = $this->form->getState(afterValidate: function (): void {
            });
        $templateForPush = clone $this->record;
        $templateForPush->whatsapp_body = $data['whatsapp_body'] ?? $this->record->whatsapp_body;
        $templateForPush->whatsapp_accept_label = $data['whatsapp_accept_label'] ?? $this->record->whatsapp_accept_label;
        $templateForPush->whatsapp_ignore_label = $data['whatsapp_ignore_label'] ?? $this->record->whatsapp_ignore_label;

        $service = app(TwilioWhatsAppService::class);
        if (! $service->isConfigured()) {
            Notification::make()
                ->title('Twilio not configured')
                ->body('Set TWILIO_SID_* and TWILIO_AUTH_TOKEN_* in .env.')
                ->danger()
                ->send();

            return;
        }

        try {
            $sid = $service->createLeadOpportunityContentTemplate($templateForPush);
            if ($sid) {
                $this->record->update(['twilio_content_sid' => $sid]);
                Notification::make()
                    ->title('Template pushed to Twilio')
                    ->body("Content SID: {$sid}. Re-approval may be required before it takes effect.")
                    ->success()
                    ->send();
            } else {
                Notification::make()
                    ->title('Push failed')
                    ->body('Twilio did not return a Content SID.')
                    ->danger()
                    ->send();
            }
        } catch (\Throwable $e) {
            Notification::make()
                ->title('Push failed')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    private function runSendTest(string $toEmail): void
    {
        try {
            $service = app(EmailTemplateTestService::class);
            $service->sendTest($this->record, $toEmail);

            Notification::make()
                ->title('Test email sent')
                ->body("Sent to {$toEmail} with sample data.")
                ->success()
                ->send();
        } catch (\RuntimeException $e) {
            Notification::make()
                ->title('Cannot send test')
                ->body($e->getMessage())
                ->danger()
                ->send();
        } catch (\Throwable $e) {
            Notification::make()
                ->title('Test send failed')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
}
