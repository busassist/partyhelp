<?php

namespace Database\Seeders;

use App\Models\GuestBracket;
use Illuminate\Database\Seeder;

class GuestBracketSeeder extends Seeder
{
    /** Default guest count brackets. Use guest_max = null for "maximum limit" (e.g. 100+). */
    private const BRACKETS = [
        ['guest_min' => 10, 'guest_max' => 29, 'sort_order' => 1],
        ['guest_min' => 30, 'guest_max' => 60, 'sort_order' => 2],
        ['guest_min' => 61, 'guest_max' => 100, 'sort_order' => 3],
        ['guest_min' => 100, 'guest_max' => null, 'sort_order' => 4], // maximum limit = 100+
    ];

    public function run(): void
    {
        foreach (self::BRACKETS as $b) {
            GuestBracket::firstOrCreate(
                [
                    'guest_min' => $b['guest_min'],
                    'guest_max' => $b['guest_max'],
                ],
                [
                    'sort_order' => $b['sort_order'],
                    'is_active' => true,
                ]
            );
        }
    }
}
