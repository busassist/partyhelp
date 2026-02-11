<?php

namespace App\Livewire;

use App\Filament\Resources\PricingMatrixResource;
use App\Models\OccasionType;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Support\Contracts\TranslatableContentDriver;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class OccasionTypesTable extends Component implements HasActions, HasTable, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithTable;
    use InteractsWithSchemas;

    public function mount(): void
    {
        $this->mountInteractsWithTable();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(OccasionType::query()->orderBy('sort_order')->orderBy('label'))
            ->modelLabel('Occasion Type')
            ->pluralModelLabel('Occasion Types')
            ->recordTitleAttribute('label')
            ->columns([
                Tables\Columns\TextColumn::make('label')
                    ->label('Occasion Type')
                    ->searchable()
                    ->sortable(),
            ])
            ->recordActions([
                Actions\Action::make('editMatrix')
                    ->label('Edit Matrix')
                    ->url(fn (OccasionType $record): string => PricingMatrixResource::getUrl(
                        'editOccasion',
                        ['occasionType' => $record->key]
                    ))
                    ->icon('heroicon-o-pencil-square'),
            ])
            ->defaultSort('sort_order');
    }

    protected function getTableQuery(): Builder
    {
        return OccasionType::query()->orderBy('sort_order')->orderBy('label');
    }

    public function makeFilamentTranslatableContentDriver(): ?TranslatableContentDriver
    {
        return null;
    }

    public function render()
    {
        return view('livewire.occasion-types-table');
    }
}
