<?php

namespace App\Filament\Resources\PostcodeResource\Pages;

use App\Filament\Resources\PostcodeResource;
use App\Services\PostcodeCsvService;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Storage;

class ListPostcodes extends ListRecords
{
    protected static string $resource = PostcodeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('downloadCsv')
                ->label('Download CSV template')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(function () {
                    $service = app(PostcodeCsvService::class);
                    $path = $service->downloadAsCsvFile();

                    return response()->download($path, 'postcodes-template.csv', [
                        'Content-Type' => 'text/csv',
                    ])->deleteFileAfterSend(true);
                }),
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
                    $path = $data['csv'] ?? null;
                    if (is_array($path)) {
                        $path = $path[0] ?? null;
                    }

                    if (!$path) {
                        \Filament\Notifications\Notification::make()
                            ->title('No file selected')
                            ->danger()
                            ->send();

                        return;
                    }

                    $fullPath = Storage::disk('local')->path($path);
                    $service = app(PostcodeCsvService::class);
                    $result = $service->replaceFromCsv($fullPath);

                    $disk = Storage::disk('local');
                    if ($disk->exists($path)) {
                        $disk->delete($path);
                    }

                    \Filament\Notifications\Notification::make()
                        ->title($result['success'] ? 'Import complete' : 'Import failed')
                        ->body($result['message'])
                        ->success($result['success'])
                        ->danger(!$result['success'])
                        ->send();

                    if ($result['success']) {
                        $this->redirect(request()->header('Referer'));
                    }
                }),
            Actions\CreateAction::make(),
        ];
    }
}
