<?php

namespace App\Filament\Pages;

use App\Console\Commands\ProcessQueueCommand;
use App\Filament\Livewire\DebugLogViewer;
use App\Filament\Livewire\ServerHealthContent;
use App\Models\SystemSetting;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Artisan;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;

/**
 * @property-read Schema $form
 */
class GeneralSettings extends Page
{
    protected static ?string $slug = 'settings';

    protected static bool $shouldRegisterNavigation = false;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $title = 'General Settings';

    /** @var array<string, mixed> */
    public array $data = [];

    public function mount(): void
    {
        $this->fillForm();
    }

    protected function fillForm(): void
    {
        $this->data = [
            'lead_max_matches' => (int) SystemSetting::get(
                'lead_max_matches',
                config('partyhelp.lead.max_matches', 30)
            ),
            'debug_logging_enabled' => SystemSetting::get('debug_logging_enabled', false),
            'new_venues_email_password' => SystemSetting::get('new_venues_email_password', false),
        ];
        $this->form->fill($this->data);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Tabs::make('Settings')
                    ->persistTabInQueryString('tab')
                    ->tabs([
                        Tab::make('Settings')
                            ->id('settings')
                            ->schema([
                                Section::make('Lead matching')
                                    ->description('Control how many venues receive each lead opportunity.')
                                    ->schema([
                                        \Filament\Forms\Components\TextInput::make('lead_max_matches')
                                            ->label('Max venues matched per lead')
                                            ->helperText('Top N venues by match score receive the lead opportunity (default 30).')
                                            ->numeric()
                                            ->minValue(1)
                                            ->maxValue(100)
                                            ->required(),
                                    ]),
                                Section::make('Venues')
                                    ->description('Options for new venues created in Admin.')
                                    ->schema([
                                        \Filament\Forms\Components\Toggle::make('new_venues_email_password')
                                            ->label('New venues: email set-password link')
                                            ->helperText('When on, new venues receive a branded email with a link to set their venue portal password.'),
                                    ]),
                            ]),
                        Tab::make('Debug')
                            ->id('debug')
                            ->schema([
                                Section::make('Debug logging')
                                    ->description('When enabled, lead received, venue matches and emails sent are logged below.')
                                    ->schema([
                                        \Filament\Forms\Components\Toggle::make('debug_logging_enabled')
                                            ->label('Enable debug logging')
                                            ->helperText('Turn on to record lead received, venues matched and emails sent. Stays on until you turn it off.'),
                                    ]),
                                Section::make('Debug log')
                                    ->headerActions([
                                        Action::make('runQueueNow')
                                            ->label('Run queue now')
                                            ->icon('heroicon-o-play')
                                            ->color('primary')
                                            ->action(fn () => $this->runQueueNow())
                                            ->requiresConfirmation(false),
                                    ])
                                    ->schema([
                                        Livewire::make(DebugLogViewer::class),
                                    ]),
                            ]),
                        Tab::make('Server Health')
                            ->id('server-health')
                            ->schema([
                                Livewire::make(ServerHealthContent::class),
                            ]),
                    ]),
            ]);
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Save')
                ->submit('save')
                ->keyBindings(['mod+s']),
        ];
    }

    public function getFormContentComponent(): Component
    {
        return Form::make([EmbeddedSchema::make('form')])
            ->id('form')
            ->livewireSubmitHandler('save')
            ->footer([
                Actions::make($this->getFormActions())
                    ->alignment(\Filament\Support\Enums\Alignment::Start),
            ]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getFormContentComponent(),
            ]);
    }

    public function runQueueNow(): void
    {
        $exitCode = Artisan::call('queue:process', ['--max-time' => 60]);

        if ($exitCode === ProcessQueueCommand::EXIT_ALREADY_RUNNING) {
            Notification::make()
                ->warning()
                ->title('Queue already running')
                ->body('A queue processor is already running. Wait for it to finish or try again shortly.')
                ->send();

            return;
        }

        Notification::make()
            ->success()
            ->title('Queue processed')
            ->body('Pending jobs have been processed (or none were queued).')
            ->send();
    }

    public function save(): void
    {
        $data = $this->form->getState();

        SystemSetting::set(
            'lead_max_matches',
            (int) ($data['lead_max_matches'] ?? 30),
            'leads',
            'integer'
        );

        SystemSetting::set(
            'debug_logging_enabled',
            ! empty($data['debug_logging_enabled']),
            'general',
            'boolean'
        );

        SystemSetting::set(
            'new_venues_email_password',
            ! empty($data['new_venues_email_password']),
            'general',
            'boolean'
        );

        Notification::make()
            ->success()
            ->title('Settings saved')
            ->send();
    }

    public function getTitle(): string | Htmlable
    {
        return 'General Settings';
    }

    public function hasLogo(): bool
    {
        return true;
    }
}
