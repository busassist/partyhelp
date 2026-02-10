<?php

namespace Database\Seeders;

use App\Models\Venue;
use App\Models\VenueStyle;
use Illuminate\Database\Seeder;

class VenueStyleSeeder extends Seeder
{
    private const INITIAL_STYLES = [
        ['name' => 'Bar', 'sort_order' => 1],
        ['name' => 'Function Room', 'sort_order' => 2],
        ['name' => 'Night Club', 'sort_order' => 3],
        ['name' => 'Courtyard', 'sort_order' => 4],
        ['name' => 'Lounge - Classy', 'sort_order' => 5],
        ['name' => 'Pub', 'sort_order' => 6],
    ];

    private const ROOM_TO_VENUE_STYLE = [
        'bar' => 'Bar',
        'function_room' => 'Function Room',
        'pub' => 'Pub',
        'club' => 'Night Club',
        'semi_outdoor' => 'Courtyard',
    ];

    public function run(): void
    {
        foreach (self::INITIAL_STYLES as $style) {
            VenueStyle::firstOrCreate(
                ['name' => $style['name']],
                ['sort_order' => $style['sort_order'], 'is_active' => true]
            );
        }

        $this->attachStylesToVenuesWithoutStyles();
    }

    private function attachStylesToVenuesWithoutStyles(): void
    {
        $venueIds = Venue::whereDoesntHave('venueStyles')->pluck('id');
        if ($venueIds->isEmpty()) {
            return;
        }

        $allStyles = VenueStyle::where('is_active', true)->pluck('id', 'name');

        foreach (Venue::whereIn('id', $venueIds)->with('rooms')->get() as $venue) {
            $styleNames = $venue->rooms
                ->pluck('style')
                ->map(fn (?string $s) => self::ROOM_TO_VENUE_STYLE[$s] ?? null)
                ->filter()
                ->unique()
                ->values();

            $ids = $styleNames->map(fn (string $n) => $allStyles[$n] ?? null)->filter()->unique()->values()->all();
            $need = rand(2, 3) - count($ids);
            if ($need > 0) {
                $remaining = $allStyles->filter(fn ($id) => ! in_array($id, $ids))->values();
                $extraCount = min($need, $remaining->count());
                if ($extraCount > 0) {
                    $ids = array_merge($ids, $remaining->random($extraCount)->all());
                }
            }
            $venue->venueStyles()->sync(array_slice(array_unique($ids), 0, 3));
        }
    }
}
