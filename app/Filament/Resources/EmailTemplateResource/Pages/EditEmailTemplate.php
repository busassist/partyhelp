<?php

namespace App\Filament\Resources\EmailTemplateResource\Pages;

use App\Filament\Resources\EmailTemplateResource;
use App\Services\SendGridTemplateSyncService;
use App\Services\SendGridTestEmailService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Http\Client\RequestException;

class EditEmailTemplate extends EditRecord
{
    protected static string $resource = EmailTemplateResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $slots = config('partyhelp.email_template_slots.' . ($this->record->key ?? ''), []);
        $slotKeys = array_keys($slots);
        if (! empty($slotKeys) && isset($data['content_slots']) && is_array($data['content_slots'])) {
            $data['content_slots'] = array_intersect_key($data['content_slots'], array_flip($slotKeys));
        }

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
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
                ->modalDescription('A test email will be sent to the address below using this template with sample data.')
                ->action(function (array $data): void {
                    $this->runSendTest($data['email']);
                }),
            \Filament\Actions\Action::make('sync_to_sendgrid')
                ->label('Sync to SendGrid')
                ->icon('heroicon-o-cloud-arrow-up')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Sync template to SendGrid?')
                ->modalDescription('This will create or update the SendGrid dynamic template. Only changed templates are uploaded (hash-based detection).')
                ->action(function () {
                    $this->runSync(force: false);
                }),
            \Filament\Actions\Action::make('sync_force')
                ->label('Force sync')
                ->icon('heroicon-o-arrow-path')
                ->color('gray')
                ->requiresConfirmation()
                ->modalHeading('Force sync to SendGrid?')
                ->modalDescription('Upload the template even if no changes were detected.')
                ->action(function () {
                    $this->runSync(force: true);
                }),
        ];
    }

    private function runSendTest(string $toEmail): void
    {
        try {
            $service = app(SendGridTestEmailService::class);
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
        } catch (RequestException $e) {
            $body = $e->response?->json();
            $message = $body['errors'][0]['message'] ?? $e->getMessage();

            Notification::make()
                ->title('SendGrid error')
                ->body($message)
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

    private function runSync(bool $force): void
    {
        try {
            $service = app(SendGridTemplateSyncService::class);
            $result = $service->sync($this->record, force: $force);

            if ($result['synced']) {
                Notification::make()
                    ->title('Template synced to SendGrid')
                    ->body("Template ID: {$result['template_id']}")
                    ->success()
                    ->send();
                $this->record->refresh();
            } else {
                Notification::make()
                    ->title('No changes to sync')
                    ->body($result['reason'] ?? 'Template is up to date.')
                    ->info()
                    ->send();
            }
        } catch (RequestException $e) {
            $body = $e->response?->json();
            $message = $body['errors'][0]['message'] ?? $e->getMessage();
            $status = $e->response?->status();

            Notification::make()
                ->title('SendGrid sync failed')
                ->body($status === 403
                    ? 'Access forbidden. Ensure your SendGrid API key has permission to manage Dynamic Templates (Template Engine).'
                    : "SendGrid returned error: {$message}")
                ->danger()
                ->send();
        } catch (\Throwable $e) {
            Notification::make()
                ->title('Sync failed')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
}
