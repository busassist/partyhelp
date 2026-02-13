<?php

namespace App\Filament\Resources\EmailTemplateResource\Pages;

use App\Filament\Resources\EmailTemplateResource;
use App\Services\EmailTemplateTestService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

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
                ->modalDescription('A test email will be sent via the default mailer (Mailgun) using this template with sample data.')
                ->action(function (array $data): void {
                    $this->runSendTest($data['email']);
                }),
        ];
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
