<?php

namespace App\Filament\Resources\AdditionalServiceResource\Pages;

use App\Filament\Resources\AdditionalServiceResource;
use App\Models\AdditionalServiceSubmission;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAdditionalServices extends ListRecords
{
    protected static string $resource = AdditionalServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('view_received_leads')
                ->label('View received leads')
                ->modalHeading('Received leads (additional services)')
                ->modalContent(function (): \Illuminate\Contracts\View\View {
                    $submissions = AdditionalServiceSubmission::with('lead')
                        ->orderByDesc('submitted_at')
                        ->get();

                    return view('filament.additional-service-submissions-modal', [
                        'submissions' => $submissions,
                    ]);
                })
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Close'),
            Actions\CreateAction::make(),
        ];
    }
}
