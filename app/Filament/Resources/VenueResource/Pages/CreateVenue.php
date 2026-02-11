<?php

namespace App\Filament\Resources\VenueResource\Pages;

use App\Filament\Resources\VenueResource;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CreateVenue extends CreateRecord
{
    protected static string $resource = VenueResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = User::create([
            'name' => $data['contact_name'] ?? $data['business_name'] ?? 'Venue',
            'email' => $data['contact_email'],
            'password' => Hash::make(Str::random(32)),
            'role' => 'venue',
        ]);
        $data['user_id'] = $user->id;

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return VenueResource::getUrl('created', ['record' => $this->record]);
    }
}
