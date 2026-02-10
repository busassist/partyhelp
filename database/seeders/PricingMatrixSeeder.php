<?php

namespace Database\Seeders;

use App\Models\PricingMatrix;
use Illuminate\Database\Seeder;

class PricingMatrixSeeder extends Seeder
{
    /** Guest count brackets matching the form (PartyhelpFormConfigController fallback). */
    private const GUEST_BRACKETS = [
        [10, 29],   // 10-29
        [30, 60],   // 30-60
        [61, 100],  // 61-100
        [101, 500], // 100+
    ];

    /**
     * Sample prices per bracket [min-29, 30-60, 61-100, 101-500].
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

    public function run(): void
    {
        $occasionTypes = array_keys(config('partyhelp.occasion_types', []));

        foreach ($occasionTypes as $occasionType) {
            $prices = self::PRICES_BY_OCCASION[$occasionType]
                ?? self::PRICES_BY_OCCASION['default'];

            foreach (self::GUEST_BRACKETS as $index => [$guestMin, $guestMax]) {
                $price = $prices[$index] ?? $prices[0];

                PricingMatrix::updateOrCreate(
                    [
                        'occasion_type' => $occasionType,
                        'guest_min' => $guestMin,
                        'guest_max' => $guestMax,
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
