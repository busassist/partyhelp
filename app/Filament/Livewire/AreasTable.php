<?php

namespace App\Filament\Livewire;

use App\Models\Area;
use App\Models\Postcode;
use App\Models\Venue;
use Filament\Actions;
use Filament\Forms;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\TableComponent;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;

class AreasTable extends TableComponent
{
    protected static bool $isDiscovered = false;

    public function mount(): void
    {
        $this->mountInteractsWithTable();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Area::query()->orderBy('sort_order')->orderBy('name'))
            ->columns([
                Tables\Columns\TextColumn::make('name')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('postcodes_count')
                    ->label('Postcodes')
                    ->counts('postcodes')
                    ->sortable(),
                Tables\Columns\TextColumn::make('venues_count')
                    ->label('Venues')
                    ->counts('venues')
                    ->sortable(),
            ])
            ->headerActions([
                Actions\CreateAction::make()
                    ->model(Area::class)
                    ->schema($this->getAreaFormSchema())
                    ->using(function (array $data): Area {
                        $record = Area::create(Arr::only($data, ['name', 'sort_order']));
                        $record->postcodes()->sync($data['postcodes'] ?? []);
                        $this->syncVenuesForArea($record, $data['venues'] ?? []);
                        return $record;
                    }),
            ])
            ->recordActions([
                Actions\EditAction::make()
                    ->schema($this->getAreaFormSchema())
                    ->fillForm(fn ($livewire, Area $record, $table): array => [
                        'name' => $record->name,
                        'sort_order' => $record->sort_order,
                        'postcodes' => $record->postcodes->pluck('id')->toArray(),
                        'venues' => $record->venues->pluck('id')->toArray(),
                    ])
                    ->using(function (array $data, $livewire, Area $record, $table): void {
                        $record->update(Arr::only($data, ['name', 'sort_order']));
                        $record->postcodes()->sync($data['postcodes'] ?? []);
                        $this->syncVenuesForArea($record, $data['venues'] ?? []);
                    }),
                Actions\DeleteAction::make(),
            ]);
    }

    protected function getAreaFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('name')->required()->maxLength(255),
            Forms\Components\TextInput::make('sort_order')->numeric()->default(0),
            Forms\Components\Select::make('postcodes')
                ->options(fn () => Postcode::optionsForSelect())
                ->multiple()
                ->preload()
                ->searchable()
                ->label('Postcodes (suburbs)'),
            Forms\Components\Select::make('venues')
                ->options(fn () => Venue::query()
                    ->orderBy('business_name')
                    ->get()
                    ->mapWithKeys(fn (Venue $v) => [$v->id => $v->business_name])
                    ->toArray())
                ->multiple()
                ->preload()
                ->searchable()
                ->label('Venues in this location'),
        ];
    }

    protected function syncVenuesForArea(Area $area, array $venueIds): void
    {
        Venue::where('area_id', $area->id)->update(['area_id' => null]);
        Venue::whereIn('id', $venueIds)->update(['area_id' => $area->id]);
    }

    public function getTitle(): string
    {
        return 'Locations (Areas)';
    }

    public function render(): View
    {
        return $this->getTable()->render();
    }
}
