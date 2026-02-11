<?php

namespace Database\Seeders;

use App\Models\GuestBracket;
use App\Models\PricingMatrix;
use Illuminate\Database\Seeder;

class PricingMatrixSeeder extends Seeder
{
    /**
     * Sample prices per bracket (by sort_order: 1=first bracket, 2=second, ...).
     * Key = occasion_type from config; value = [price_bracket_1, price_bracket_2, ...].
     * Missing types use 'default'.
     */
    private const PRICES_BY_OCCASION = [
        'default' => [8, 12, 18, 25],
        '21st_birthday' => [8, 12, 18, 25],
        '30th_birthday' => [8, 12, 18, 25],
        '40th_birthday' => [10, 14, 20, 28],
        '50th_birthday' => [10, 14, 20, 28],
        '60th_birthday' => [10, 14, 20, 28],
        'other_birthday' => [8, 12, 18, 25],
        'engagement_party' => [10, 15, 22, 30],
        'wedding_reception' => [15, 22, 30, 40],
        'corporate_function' => [12, 18, 25, 35],
        'christmas_party' => [10, 15, 20, 28],
        'farewell_party' => [10, 14, 20, 28],
        'baby_shower' => [8, 12, 18, 25],
        'other' => [6, 10, 15, 20],
    ];

    /**
     * Seeds a price row for every occasion type × guest bracket.
     * Run after GuestBracketSeeder. To refresh: php artisan db:seed --class=PricingMatrixSeeder
     */
    public function run(): void
    {
        $brackets = GuestBracket::where('is_active', true)->orderBy('sort_order')->get();
        if ($brackets->isEmpty()) {
            $this->command?->warn('PricingMatrixSeeder: No guest brackets found. Run GuestBracketSeeder first.');

            return;
        }

        $occasionTypes = array_keys(config('partyhelp.occasion_types', []));
        $defaultPrices = self::PRICES_BY_OCCASION['default'];

        foreach ($occasionTypes as $occasionType) {
            $prices = self::PRICES_BY_OCCASION[$occasionType]
                ?? $defaultPrices;
            $prices = array_values($prices);

            foreach ($brackets as $index => $bracket) {
                $price = $prices[$index] ?? $prices[0] ?? $defaultPrices[0];

                PricingMatrix::updateOrCreate(
                    [
                        'occasion_type' => $occasionType,
                        'guest_bracket_id' => $bracket->id,
                    ],
                    [
                        'price' => $price,
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}
