<?php

namespace App\Filament\Livewire;

use App\Models\Postcode;
use App\Services\PostcodeCsvService;
use Filament\Actions;
use Filament\Forms;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\TableComponent;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;

class PostcodesTable extends TableComponent
{
    protected static bool $isDiscovered = false;

    public function mount(): void
    {
        $this->mountInteractsWithTable();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Postcode::query()->orderBy('sort_order')->orderBy('suburb'))
            ->columns([
                Tables\Columns\TextColumn::make('suburb')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('postcode')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('state')->sortable(),
            ])
            ->reorderable('sort_order')
            ->headerActions([
                Actions\Action::make('downloadCsv')
                    ->label('Download CSV template')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(fn () => response()->download(
                        app(PostcodeCsvService::class)->downloadAsCsvFile(),
                        'postcodes-template.csv',
                        ['Content-Type' => 'text/csv']
                    )->deleteFileAfterSend(true)),
                Actions\Action::make('uploadCsv')
                    ->label('Upload CSV')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->form([
                        Forms\Components\FileUpload::make('csv')
                            ->label('CSV file')
                            ->acceptedFileTypes(['text/csv', 'application/csv', 'text/plain'])
                            ->required()
                            ->disk('local')
                            ->directory('temp/postcode-import')
                            ->maxSize(2048),
                    ])
                    ->action(function (array $data): void {
                        $path = is_array($data['csv'] ?? null) ? ($data['csv'][0] ?? null) : ($data['csv'] ?? null);
                        if (!$path) {
                            \Filament\Notifications\Notification::make()->title('No file selected')->danger()->send();
                            return;
                        }
                        $result = app(PostcodeCsvService::class)->replaceFromCsv(Storage::disk('local')->path($path));
                        Storage::disk('local')->delete($path);
                        \Filament\Notifications\Notification::make()
                            ->title($result['success'] ? 'Import complete' : 'Import failed')
                            ->body($result['message'])
                            ->success($result['success'])->danger(!$result['success'])
                            ->send();
                        if ($result['success']) {
                            $this->redirect(request()->header('Referer'));
                        }
                    }),
                Actions\CreateAction::make()
                    ->model(Postcode::class)
                    ->schema([
                        Forms\Components\TextInput::make('suburb')->required()->maxLength(100),
                        Forms\Components\TextInput::make('postcode')->required()->maxLength(10),
                        Forms\Components\TextInput::make('state')->maxLength(10)->default('VIC'),
                        Forms\Components\TextInput::make('sort_order')->numeric()->default(0),
                    ]),
            ])
            ->recordActions([
                Actions\EditAction::make()
                    ->schema([
                        Forms\Components\TextInput::make('suburb')->required()->maxLength(100),
                        Forms\Components\TextInput::make('postcode')->required()->maxLength(10),
                        Forms\Components\TextInput::make('state')->maxLength(10)->default('VIC'),
                        Forms\Components\TextInput::make('sort_order')->numeric()->default(0),
                    ]),
                Actions\DeleteAction::make(),
            ]);
    }

    public function getTitle(): string
    {
        return 'Postcodes';
    }

    public function render(): View
    {
        return $this->getTable()->render();
    }
}
