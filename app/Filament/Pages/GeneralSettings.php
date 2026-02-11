<?php

namespace App\Filament\Pages;

use App\Models\SystemSetting;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
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
        ];
        $this->form->fill($this->data);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
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

    public function save(): void
    {
        $data = $this->form->getState();

        SystemSetting::set(
            'lead_max_matches',
            (int) ($data['lead_max_matches'] ?? 30),
            'leads',
            'integer'
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
