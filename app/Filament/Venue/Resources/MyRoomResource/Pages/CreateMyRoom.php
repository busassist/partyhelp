<?php

namespace App\Filament\Venue\Resources\MyRoomResource\Pages;

use App\Filament\Venue\Resources\MyRoomResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMyRoom extends CreateRecord
{
    protected static string $resource = MyRoomResource::class;

    public function mount(): void
    {
        parent::mount();
        if (! MyRoomResource::canCreate()) {
            $max = (int) \App\Models\SystemSetting::get('max_rooms_per_venue', config('partyhelp.max_rooms_per_venue', 6));
            $this->redirect(MyRoomResource::getUrl('index'));
            \Filament\Notifications\Notification::make()
                ->warning()
                ->title('Room limit reached')
                ->body("You can have up to {$max} rooms per venue. Delete a room to add another.")
                ->send();
        }
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['venue_id'] = auth()->user()->venue->id;

        return $data;
    }
}
