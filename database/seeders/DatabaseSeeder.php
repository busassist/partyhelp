<?php

namespace Database\Seeders;

use App\Models\DiscountSetting;
use App\Models\PricingMatrix;
use App\Models\SystemSetting;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        User::create([
            'name' => 'Partyhelp Admin',
            'email' => 'admin@partyhelp.com.au',
            'password' => bcrypt('adminfestive'),
            'role' => 'admin',
        ]);

        // Test venue user
        $venueUser = User::create([
            'name' => 'Test Venue',
            'email' => 'venue@partyhelp.com.au',
            'password' => bcrypt('venuefestive'),
            'role' => 'venue',
        ]);

        Venue::create([
            'user_id' => $venueUser->id,
            'business_name' => 'Test Venue Pty Ltd',
            'contact_name' => 'Test Venue',
            'contact_email' => 'venue@partyhelp.com.au',
            'contact_phone' => '0400000000',
            'address' => '123 Test St',
            'suburb' => 'Melbourne',
            'state' => 'VIC',
            'postcode' => '3000',
            'credit_balance' => 200,
            'status' => 'active',
            'approved_at' => now(),
        ]);

        $this->seedPricingMatrix();
        $this->seedDiscountSettings();
        $this->seedSystemSettings();
    }

    private function seedPricingMatrix(): void
    {
        $matrix = [
            ['21st_birthday', 10, 30, 8], ['21st_birthday', 31, 60, 12],
            ['21st_birthday', 61, 100, 18], ['21st_birthday', 101, 500, 25],
            ['30th_birthday', 10, 30, 8], ['30th_birthday', 31, 60, 12],
            ['30th_birthday', 61, 100, 18], ['30th_birthday', 101, 500, 25],
            ['engagement_party', 10, 30, 10], ['engagement_party', 31, 60, 15],
            ['engagement_party', 61, 100, 22], ['engagement_party', 101, 500, 30],
            ['wedding_reception', 10, 30, 15], ['wedding_reception', 31, 60, 22],
            ['wedding_reception', 61, 100, 30], ['wedding_reception', 101, 500, 40],
            ['corporate_function', 10, 30, 12], ['corporate_function', 31, 60, 18],
            ['corporate_function', 61, 100, 25], ['corporate_function', 101, 500, 35],
            ['christmas_party', 10, 30, 10], ['christmas_party', 31, 60, 15],
            ['christmas_party', 61, 100, 20], ['christmas_party', 101, 500, 28],
            ['other', 10, 30, 6], ['other', 31, 60, 10],
            ['other', 61, 100, 15], ['other', 101, 500, 20],
        ];

        foreach ($matrix as [$occasion, $min, $max, $price]) {
            PricingMatrix::create([
                'occasion_type' => $occasion,
                'guest_min' => $min,
                'guest_max' => $max,
                'price' => $price,
            ]);
        }
    }

    private function seedDiscountSettings(): void
    {
        DiscountSetting::create(['hours_elapsed' => 24, 'discount_percent' => 10, 'sort_order' => 1]);
        DiscountSetting::create(['hours_elapsed' => 48, 'discount_percent' => 20, 'sort_order' => 2]);
    }

    private function seedSystemSettings(): void
    {
        $settings = [
            ['lead_fulfilment_threshold', '3', 'leads', 'integer'],
            ['lead_expiry_hours', '72', 'leads', 'integer'],
            ['admin_email', 'admin@partyhelp.com.au', 'general', 'string'],
            ['min_credit_balance', '100', 'credits', 'float'],
        ];

        foreach ($settings as [$key, $value, $group, $type]) {
            SystemSetting::create(compact('key', 'value', 'group', 'type'));
        }
    }
}
