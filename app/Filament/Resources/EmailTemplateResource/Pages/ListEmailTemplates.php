<?php

namespace App\Filament\Resources\EmailTemplateResource\Pages;

use App\Filament\Resources\EmailTemplateResource;
use App\Models\EmailTemplate;
use App\Services\SendGridTemplateSyncService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Http\Client\RequestException;

class ListEmailTemplates extends ListRecords
{
    protected static string $resource = EmailTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('sync_all_to_sendgrid')
                ->label('Sync all templates to SendGrid')
                ->icon('heroicon-o-cloud-arrow-up')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Sync all templates to SendGrid?')
                ->modalDescription('This will sync every email template one by one. Changed templates are uploaded; unchanged ones are skipped. Failures for one template do not stop the rest.')
                ->action(function (): void {
                    $this->runSyncAll(force: false);
                }),
        ];
    }

    private function runSyncAll(bool $force): void
    {
        $service = app(SendGridTemplateSyncService::class);
        $synced = [];
        $skipped = [];
        $failed = [];

        foreach (EmailTemplate::orderBy('key')->get() as $template) {
            try {
                $result = $service->sync($template, force: $force);
                if ($result['synced']) {
                    $synced[] = $template->key;
                    $template->refresh();
                } else {
                    $skipped[] = $template->key;
                }
            } catch (RequestException $e) {
                $body = $e->response?->json();
                $message = $body['errors'][0]['message'] ?? $e->getMessage();
                $failed[$template->key] = $message;
            } catch (\Throwable $e) {
                $failed[$template->key] = $e->getMessage();
            }
        }

        $total = count($synced) + count($skipped) + count($failed);
        $parts = [];
        if (count($synced) > 0) {
            $parts[] = count($synced) . ' synced';
        }
        if (count($skipped) > 0) {
            $parts[] = count($skipped) . ' unchanged';
        }
        if (count($failed) > 0) {
            $parts[] = count($failed) . ' failed';
        }

        $body = implode(', ', $parts) . '.';
        if (count($failed) > 0) {
            $body .= ' Failed: ' . implode('; ', array_map(fn ($k, $v) => "{$k}: {$v}", array_keys($failed), $failed));
        }

        Notification::make()
            ->title("Sync complete ({$total} templates)")
            ->body($body)
            ->success(count($failed) === 0)
            ->send();
    }
}
