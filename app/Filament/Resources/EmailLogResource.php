<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmailLogResource\Pages;
use App\Models\EmailLog;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EmailLogResource extends Resource
{
    protected static ?string $model = EmailLog::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-envelope-stack';

    protected static string | \UnitEnum | null $navigationGroup = 'Manage System Data';

    protected static ?string $navigationLabel = 'Emails';

    protected static ?string $modelLabel = 'Email log';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('to_email')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('to_name')->searchable(),
                Tables\Columns\TextColumn::make('subject')->searchable()->limit(40),
                Tables\Columns\TextColumn::make('template')->badge(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'sent', 'delivered', 'opened', 'clicked' => 'success',
                        'queued' => 'warning',
                        'bounced', 'failed' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->recordActions([
                Tables\Actions\ViewAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmailLogs::route('/'),
            'view' => Pages\ViewEmailLog::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
