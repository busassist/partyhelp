<?php

namespace App\Filament\Venue\Resources\AvailableLeadResource\Pages;

use App\Filament\Venue\Resources\AvailableLeadResource;
use App\Services\TestLeadInjectionService;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListAvailableLeads extends ListRecords
{
    protected static string $resource = AvailableLeadResource::class;

    public static function canInjectTestLead(): bool
    {
        if (! config('partyhelp.test_mode', false)) {
            return false;
        }
        $email = auth()->user()?->email;

        return $email && strtolower($email) === strtolower(config('partyhelp.test_user_email', ''));
    }

    protected function getHeaderActions(): array
    {
        $actions = [];

        if (static::canInjectTestLead()) {
            $venue = auth()->user()?->venue;
            if ($venue) {
                $actions[] = Actions\Action::make('injectTestLead')
                    ->label('Inject test lead')
                    ->icon('heroicon-o-beaker')
                    ->color('gray')
                    ->requiresConfirmation()
                    ->modalHeading('Inject a test lead')
                    ->modalDescription('This will create one fake lead opportunity visible only to you, for testing the purchase flow. Only available in test mode.')
                    ->action(function () use ($venue) {
                        $service = app(TestLeadInjectionService::class);
                        $lead = $service->inject($venue);
                        Notification::make()
                            ->title('Test lead added')
                            ->body("Lead #{$lead->id} ({$lead->occasion_type}, {$lead->guest_count} guests) is now in your available leads.")
                            ->success()
                            ->send();
                    });
            }
        }

        return $actions;
    }
}
