<?php

namespace App\Filament\Resources\PricingMatrixResource\Pages;

use App\Filament\Resources\PricingMatrixResource;
use App\Models\OccasionType;
use App\Models\PricingMatrix;
use Filament\Actions;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Components\EmbeddedTable;
use Filament\Schemas\Components\RenderHook;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\View\PanelsRenderHook;
use Illuminate\Contracts\Support\Htmlable;

class EditOccasionMatrix extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $resource = PricingMatrixResource::class;

    public ?string $occasionType = null;

    public function mount(string $occasionType): void
    {
        $this->occasionType = $occasionType;
    }

    public function getTitle(): string | Htmlable
    {
        $label = OccasionType::options()[$this->occasionType] ?? $this->occasionType;

        return "Edit Matrix: {$label}";
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                PricingMatrix::query()->where('occasion_type', $this->occasionType)
            )
            ->columns([
                Tables\Columns\TextInputColumn::make('guest_min')
                    ->label('From')
                    ->type('number')
                    ->rules(['required', 'integer', 'min:0']),
                Tables\Columns\TextInputColumn::make('guest_max')
                    ->label('To')
                    ->type('number')
                    ->rules(['required', 'integer', 'min:0']),
                Tables\Columns\TextInputColumn::make('price')
                    ->prefix('$')
                    ->type('number')
                    ->rules(['required', 'numeric', 'min:0']),
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Active'),
            ])
            ->recordActions([
                Actions\DeleteAction::make(),
            ])
            ->headerActions([
                Actions\CreateAction::make()
                    ->model(PricingMatrix::class)
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['occasion_type'] = $this->occasionType;

                        return $data;
                    })
                    ->form([
                        \Filament\Forms\Components\TextInput::make('guest_min')
                            ->label('From')
                            ->numeric()
                            ->required(),
                        \Filament\Forms\Components\TextInput::make('guest_max')
                            ->label('To')
                            ->numeric()
                            ->required(),
                        \Filament\Forms\Components\TextInput::make('price')
                            ->prefix('$')
                            ->numeric()
                            ->required(),
                        \Filament\Forms\Components\Toggle::make('is_active')
                            ->default(true),
                    ]),
            ])
            ->defaultSort('guest_min');
    }

    public function content(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return $schema
            ->components([
                RenderHook::make(PanelsRenderHook::RESOURCE_PAGES_LIST_RECORDS_TABLE_BEFORE),
                EmbeddedTable::make(),
                RenderHook::make(PanelsRenderHook::RESOURCE_PAGES_LIST_RECORDS_TABLE_AFTER),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('back')
                ->label('Back to list')
                ->url(PricingMatrixResource::getUrl('index'))
                ->color('gray'),
        ];
    }
}
