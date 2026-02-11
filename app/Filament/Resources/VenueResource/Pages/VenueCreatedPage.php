<?php

namespace App\Filament\Resources\VenueResource\Pages;

use App\Filament\Resources\VenueResource;
use App\Jobs\SendVenueSetPasswordEmail;
use Filament\Resources\Pages\Page;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Actions\Action;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class VenueCreatedPage extends Page
{
    use InteractsWithRecord;

    protected static string $resource = VenueResource::class;

    protected static ?string $navigationLabel = null;

    public static function getNavigationSort(): ?int
    {
        return null;
    }

    public static function shouldRegisterNavigation(array $parameters = []): bool
    {
        return false;
    }

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);
        $this->authorizeAccess();
    }

    protected function authorizeAccess(): void
    {
        abort_unless(static::getResource()::canEdit($this->getRecord()), 403);
    }

    protected function getHeaderActions(): array
    {
        $venue = $this->getRecord();

        return [
            Action::make('sendSetPasswordEmail')
                ->label('Yes, send set-password email')
                ->color('success')
                ->action(function () use ($venue) {
                    if ($venue->user_id) {
                        SendVenueSetPasswordEmail::dispatch($venue);
                    }
                    $this->redirect(VenueResource::getUrl('edit', ['record' => $venue]));
                }),
            Action::make('skip')
                ->label('No')
                ->color('gray')
                ->action(function () use ($venue) {
                    $this->redirect(VenueResource::getUrl('edit', ['record' => $venue]));
                }),
        ];
    }

    public function getTitle(): string
    {
        return 'Venue created';
    }

    public function getHeading(): string
    {
        return 'Send venue intro email to set password?';
    }

    public function content(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make()
                ->description('Choose whether to email this venue a link to set their portal password, or set it yourself later from the venue edit page.')
                ->schema([]),
        ]);
    }
}
