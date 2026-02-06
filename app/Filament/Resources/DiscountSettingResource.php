<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DiscountSettingResource\Pages;
use App\Models\DiscountSetting;
use Filament\Forms;
use Filament\Actions;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class DiscountSettingResource extends Resource
{
    protected static ?string $model = DiscountSetting::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-receipt-percent';

    protected static string | \UnitEnum | null $navigationGroup = 'Manage System Data';

    protected static ?string $navigationLabel = 'Discount Settings';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\TextInput::make('hours_elapsed')
                ->numeric()->required()->label('Hours after distribution'),
            Forms\Components\TextInput::make('discount_percent')
                ->numeric()->required()->suffix('%'),
            Forms\Components\Toggle::make('resend_notification')
                ->label('Re-send to venues')->default(true),
            Forms\Components\Toggle::make('is_active')->default(true),
            Forms\Components\TextInput::make('sort_order')
                ->numeric()->default(0),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('hours_elapsed')
                    ->label('After (hours)')->sortable(),
                Tables\Columns\TextColumn::make('discount_percent')
                    ->label('Discount')->suffix('%'),
                Tables\Columns\IconColumn::make('resend_notification')
                    ->label('Resend')->boolean(),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
            ])
            ->recordActions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->defaultSort('sort_order');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDiscountSettings::route('/'),
            'create' => Pages\CreateDiscountSetting::route('/create'),
            'edit' => Pages\EditDiscountSetting::route('/{record}/edit'),
        ];
    }
}
