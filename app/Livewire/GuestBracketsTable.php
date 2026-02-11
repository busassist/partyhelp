<?php

namespace App\Livewire;

use App\Filament\Resources\GuestBracketResource;
use App\Models\GuestBracket;
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

class GuestBracketsTable extends Component implements HasActions, HasTable, HasSchemas
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
            ->query(GuestBracket::query()->orderBy('sort_order'))
            ->modelLabel('Guest Bracket')
            ->pluralModelLabel('Guest Brackets')
            ->columns([
                Tables\Columns\TextColumn::make('label')
                    ->label('Range')
                    ->getStateUsing(function (GuestBracket $record): string {
                        if ($record->isMaximumLimit() || ($record->guest_max !== null && $record->guest_max >= 500)) {
                            return $record->guest_min . '-Maximum';
                        }
                        return $record->label;
                    }),
                Tables\Columns\TextColumn::make('guest_min')->label('From')->sortable(),
                Tables\Columns\TextColumn::make('guest_max')
                    ->label('To')
                    ->formatStateUsing(function (?int $state): string {
                        if ($state === null || $state >= 500) {
                            return 'Maximum';
                        }
                        return (string) $state;
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('sort_order')->sortable(),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
            ])
            ->headerActions([
                Actions\CreateAction::make()
                    ->url(GuestBracketResource::getUrl('create')),
            ])
            ->recordActions([
                Actions\Action::make('edit')
                    ->label('Edit')
                    ->url(fn (GuestBracket $record): string => GuestBracketResource::getUrl('edit', ['record' => $record]))
                    ->icon('heroicon-o-pencil-square'),
            ])
            ->defaultSort('sort_order');
    }

    protected function getTableQuery(): Builder
    {
        return GuestBracket::query()->orderBy('sort_order');
    }

    public function makeFilamentTranslatableContentDriver(): ?TranslatableContentDriver
    {
        return null;
    }

    public function render()
    {
        return view('livewire.guest-brackets-table');
    }
}
