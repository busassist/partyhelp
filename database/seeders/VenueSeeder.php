<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\Postcode;
use App\Models\Room;
use App\Models\User;
use App\Models\Venue;
use App\Models\VenueStyle;
use Illuminate\Database\Seeder;

class VenueSeeder extends Seeder
{
    private const MELBOURNE_SUBURBS = [
        ['Richmond', '3121'], ['Fitzroy', '3065'], ['Collingwood', '3066'],
        ['St Kilda', '3182'], ['South Yarra', '3141'], ['Carlton', '3053'],
        ['Brunswick', '3056'], ['Prahran', '3181'], ['South Melbourne', '3205'],
        ['Port Melbourne', '3207'], ['Melbourne', '3000'], ['Abbotsford', '3067'],
        ['North Melbourne', '3051'], ['Footscray', '3011'], ['Williamstown', '3016'],
        ['Elwood', '3184'], ['Brighton', '3186'], ['Hawthorn', '3122'],
        ['Camberwell', '3124'], ['Kew', '3101'], ['Armadale', '3143'],
        ['Windsor', '3181'], ['Balaclava', '3183'], ['Albert Park', '3206'],
        ['Docklands', '3008'], ['CBD', '3000'], ['East Melbourne', '3002'],
        ['Cremorne', '3121'], ['Clifton Hill', '3068'], ['Kensington', '3031'],
    ];

    private const VENUE_NAMES = [
        'The Fox & Hound', 'The Town Hall Richmond', 'The Prince Alfred',
        'The Local Taphouse', 'The Railway Hotel', 'The Standard Hotel',
        'The Napier Hotel', 'The Builders Arms', 'The Gertrude Hotel',
        'The Union Club', 'The Esplanade Hotel', 'The George Hotel',
        'The Corner Hotel', 'The Tote', 'The Retreat Hotel',
        'The Royal Derby', 'The Marquis of Lorne', 'The Post Office Hotel',
        'The Terminus Hotel', 'The Railway Club', 'The Park Hotel',
        'The Bridge Hotel', 'The Commercial Hotel', 'The Rising Sun',
        'The Duke of Wellington', 'The Elephant & Wheelbarrow',
        'The Drunken Poet', 'The Catfish', 'The Workers Club',
        'The Gasometer', 'The Brunswick Green', 'The Penny Black',
        'The Provincial Hotel', 'The Railway Brunswick', 'The Cornish Arms',
        'The Reverence Hotel', 'The B.East', 'The Union House',
        'The Hawthorn Hotel', 'The Auburn Hotel', 'The Camberwell Arms',
        'The Kew Hotel', 'The Armadale Hotel', 'The Windsor Castle',
        'The St Kilda RSL', 'The Espy', 'The Prince of Wales',
        'The Village Belle', 'The Greyhound', 'The Local',
    ];

    private const ROOM_NAMES = [
        'Mezzanine Bar', 'Corporate Lounge', 'Main Bar', 'Function Room',
        'Private Dining Room', 'Rooftop Terrace', 'Beer Garden', 'The Loft',
        'Garden Room', 'Front Bar', 'Back Room', 'Upstairs Lounge',
        'Cellar Bar', 'Courtyard', 'Ballroom', 'Boardroom',
        'Terrace Room', 'Wine Bar', 'Sports Bar', 'Dining Room',
    ];

    private const ROOM_STYLES = ['bar', 'function_room', 'pub', 'club', 'semi_outdoor'];

    public function run(): void
    {
        $occasionKeys = array_keys(\App\Models\OccasionType::options());
        $usedNames = [];

        for ($i = 0; $i < 50; $i++) {
            $venueName = $this->uniqueName(self::VENUE_NAMES, $usedNames);
            $suburb = self::MELBOURNE_SUBURBS[$i % count(self::MELBOURNE_SUBURBS)];
            [$suburbName, $postcode] = $suburb;

            $user = User::create([
                'name' => $venueName,
                'email' => 'venue' . ($i + 100) . '@seed.partyhelp.com.au',
                'password' => bcrypt('password'),
                'role' => 'venue',
            ]);

            $areaId = $this->areaIdForSuburb($suburbName);

            $venue = Venue::create([
                'user_id' => $user->id,
                'business_name' => $venueName,
                'abn' => $this->fakeAbn(),
                'contact_name' => fake()->name(),
                'contact_email' => $user->email,
                'contact_phone' => '04' . fake()->numerify('########'),
                'website' => 'https://' . strtolower(str_replace([' ', '&', "'"], ['', '', ''], $venueName)) . '.com.au',
                'address' => fake()->numberBetween(1, 300) . ' ' . fake()->streetName(),
                'suburb' => $suburbName,
                'state' => 'VIC',
                'postcode' => $postcode,
                'area_id' => $areaId,
                'suburb_tags' => $this->adjacentSuburbs($suburbName),
                'occasion_tags' => fake()->randomElements($occasionKeys, rand(4, 8)),
                'credit_balance' => fake()->randomFloat(2, 150, 500),
                'auto_topup_enabled' => true,
                'auto_topup_threshold' => 75,
                'auto_topup_amount' => 50,
                'status' => 'active',
                'approved_at' => now(),
                'last_activity_at' => fake()->dateTimeBetween('-30 days'),
            ]);

            $roomCount = rand(2, 4);
            $usedRoomNames = [];

            for ($r = 0; $r < $roomCount; $r++) {
                $roomName = $this->uniqueRoomName($usedRoomNames);
                $style = self::ROOM_STYLES[array_rand(self::ROOM_STYLES)];
                $minCap = [10, 20, 30, 50][rand(0, 3)];
                $maxCap = $minCap + [40, 70, 100, 150][rand(0, 3)];
                $seated = (int) ($maxCap * (0.5 + (rand(0, 50) / 100)));
                $hireMin = [500, 800, 1200, 1500, 2000][rand(0, 4)];
                $hireMax = $hireMin + [500, 1000, 1500, 2000][rand(0, 3)];

                Room::create([
                    'venue_id' => $venue->id,
                    'name' => $roomName,
                    'style' => $style,
                    'min_capacity' => $minCap,
                    'max_capacity' => $maxCap,
                    'seated_capacity' => $seated,
                    'hire_cost_min' => $hireMin,
                    'hire_cost_max' => $hireMax,
                    'description' => "Spacious {$roomName} ideal for private functions. " . fake()->sentence(),
                    'features' => $this->roomFeatures(),
                    'sort_order' => $r,
                    'is_active' => true,
                ]);
            }

            $venue->venueStyles()->sync($this->venueStylesForVenue($venue));
        }

        $this->assignAreasToVenuesWithoutArea();
    }

    private function areaIdForSuburb(string $suburbName): ?int
    {
        $postcode = Postcode::where('suburb', $suburbName)->first();
        if (! $postcode) {
            return Area::orderBy('sort_order')->value('id');
        }

        $area = $postcode->areas()->first();

        return $area?->id ?? Area::orderBy('sort_order')->value('id');
    }

    /** Ensure every venue has at least one area (for existing DB or test venue). */
    private function assignAreasToVenuesWithoutArea(): void
    {
        $venuesWithoutArea = Venue::whereNull('area_id')->get();
        $defaultAreaId = Area::orderBy('sort_order')->value('id');

        foreach ($venuesWithoutArea as $venue) {
            $areaId = $this->areaIdForSuburb($venue->suburb) ?? $defaultAreaId;
            if ($areaId) {
                $venue->update(['area_id' => $areaId]);
            }
        }
    }

    /** @return int[] */
    private function venueStylesForVenue(Venue $venue): array
    {
        $roomToVenueStyle = [
            'bar' => 'Bar',
            'function_room' => 'Function Room',
            'pub' => 'Pub',
            'club' => 'Night Club',
            'semi_outdoor' => 'Courtyard',
        ];

        $roomStyles = $venue->rooms->pluck('style')->unique()->filter()->values();
        $styleNames = $roomStyles->map(fn (string $s) => $roomToVenueStyle[$s] ?? null)->filter()->unique()->values()->all();

        $allStyles = VenueStyle::where('is_active', true)->pluck('id', 'name');
        $ids = [];
        foreach ($styleNames as $name) {
            if (isset($allStyles[$name])) {
                $ids[] = $allStyles[$name];
            }
        }

        $need = rand(2, 3) - count($ids);
        if ($need > 0) {
            $remaining = $allStyles->filter(fn ($id) => ! in_array($id, $ids))->values();
            $extraCount = min($need, $remaining->count());
            if ($extraCount > 0) {
                $extra = $remaining->random($extraCount);
                $ids = array_merge($ids, $extra->all());
            }
        }

        return array_slice(array_unique($ids), 0, 3);
    }

    private function uniqueName(array $pool, array &$used): string
    {
        $available = array_values(array_diff($pool, $used));
        $name = $available ? $available[array_rand($available)] : $pool[array_rand($pool)];
        $used[] = $name;

        return $name;
    }

    private function uniqueRoomName(array &$used): string
    {
        $available = array_values(array_diff(self::ROOM_NAMES, $used));
        $name = $available ? $available[array_rand($available)] : self::ROOM_NAMES[array_rand(self::ROOM_NAMES)];
        $used[] = $name;

        return $name;
    }

    private function fakeAbn(): string
    {
        return fake()->numerify('###########');
    }

    private function adjacentSuburbs(string $suburb): array
    {
        $all = array_column(self::MELBOURNE_SUBURBS, 0);
        $idx = array_search($suburb, $all);
        if ($idx === false) {
            return array_slice($all, 0, 3);
        }
        $adjacent = [];
        for ($i = max(0, $idx - 2); $i <= min(count($all) - 1, $idx + 2); $i++) {
            if ($all[$i] !== $suburb) {
                $adjacent[] = $all[$i];
            }
        }

        return array_slice($adjacent, 0, 4);
    }

    private function roomFeatures(): array
    {
        $keys = array_keys(\App\Models\Feature::options());

        return empty($keys)
            ? []
            : fake()->randomElements($keys, min(rand(2, 5), count($keys)));
    }
}
