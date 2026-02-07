<?php

namespace Database\Seeders;

use App\Models\Room;
use App\Models\Venue;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        $venue = Venue::whereHas('user', fn ($q) => $q->where('email', 'venue@partyhelp.com.au'))->first();

        if (! $venue) {
            return;
        }

        if ($venue->rooms()->exists()) {
            return;
        }

        $rooms = [
            [
                'name' => 'Main Function Room',
                'style' => 'function_room',
                'min_capacity' => 30,
                'max_capacity' => 120,
                'seated_capacity' => 80,
                'hire_cost_min' => 1200,
                'hire_cost_max' => 2500,
                'description' => 'Spacious main function room with natural light, perfect for weddings, birthdays and corporate events.',
                'features' => ['av_equipment', 'dance_floor', 'private_bar', 'projector', 'sound_system'],
                'sort_order' => 0,
            ],
            [
                'name' => 'The Loft',
                'style' => 'function_room',
                'min_capacity' => 20,
                'max_capacity' => 60,
                'seated_capacity' => 40,
                'hire_cost_min' => 800,
                'hire_cost_max' => 1500,
                'description' => 'Intimate loft space ideal for smaller gatherings and cocktail receptions.',
                'features' => ['private_bar', 'outdoor_access', 'projector'],
                'sort_order' => 1,
            ],
            [
                'name' => 'Courtyard',
                'style' => 'semi_outdoor',
                'min_capacity' => 40,
                'max_capacity' => 100,
                'seated_capacity' => 60,
                'hire_cost_min' => 1000,
                'hire_cost_max' => 2000,
                'description' => 'Semi-outdoor courtyard with heaters, perfect for summer events and cocktail parties.',
                'features' => ['outdoor_access', 'private_bar'],
                'sort_order' => 2,
            ],
            [
                'name' => 'Boardroom',
                'style' => 'function_room',
                'min_capacity' => 10,
                'max_capacity' => 30,
                'seated_capacity' => 25,
                'hire_cost_min' => 500,
                'hire_cost_max' => 900,
                'description' => 'Private boardroom for meetings and small corporate events.',
                'features' => ['av_equipment', 'projector'],
                'sort_order' => 3,
            ],
        ];

        foreach ($rooms as $data) {
            Room::create([
                'venue_id' => $venue->id,
                ...$data,
                'is_active' => true,
            ]);
        }
    }
}
