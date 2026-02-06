<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostcodeResource\Pages;
use App\Models\Postcode;
use App\Services\PostcodeCsvService;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class PostcodeResource extends Resource
{
    protected static ?string $model = Postcode::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-map-pin';

    protected static string | \UnitEnum | null $navigationGroup = 'Manage System Data';

    protected static ?string $navigationLabel = 'Postcodes';

    protected static ?string $modelLabel = 'Postcode';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\TextInput::make('suburb')->required()->maxLength(100),
            Forms\Components\TextInput::make('postcode')->required()->maxLength(10),
            Forms\Components\TextInput::make('state')->maxLength(10)->default('VIC'),
            Forms\Components\TextInput::make('sort_order')->numeric()->default(0),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('suburb')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('postcode')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('state')->sortable(),
            ])
            ->recordActions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->defaultSort('suburb');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPostcodes::route('/'),
            'create' => Pages\CreatePostcode::route('/create'),
            'edit' => Pages\EditPostcode::route('/{record}/edit'),
        ];
    }
}
