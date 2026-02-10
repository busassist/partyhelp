<?php

namespace App\Filament\Resources\EmailTemplateResource\Pages;

use App\Filament\Resources\EmailTemplateResource;
use App\Services\SendGridTemplateSyncService;
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
            \Filament\Actions\Action::make('sync_to_sendgrid')
                ->label('Sync to SendGrid')
                ->icon('heroicon-o-cloud-arrow-up')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Sync template to SendGrid?')
                ->modalDescription('This will create or update the SendGrid dynamic template. Only changed templates are uploaded (hash-based detection).')
                ->action(function () {
                    $service = app(SendGridTemplateSyncService::class);
                    $result = $service->sync($this->record, force: false);

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
                }),
            \Filament\Actions\Action::make('sync_force')
                ->label('Force sync')
                ->icon('heroicon-o-arrow-path')
                ->color('gray')
                ->requiresConfirmation()
                ->modalHeading('Force sync to SendGrid?')
                ->modalDescription('Upload the template even if no changes were detected.')
                ->action(function () {
                    $service = app(SendGridTemplateSyncService::class);
                    $result = $service->sync($this->record, force: true);

                    Notification::make()
                        ->title('Template force-synced to SendGrid')
                        ->body("Template ID: {$result['template_id']}")
                        ->success()
                        ->send();
                    $this->record->refresh();
                }),
        ];
    }
}
