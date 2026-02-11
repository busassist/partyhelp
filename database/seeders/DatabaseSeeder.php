<?php

namespace Database\Seeders;

use App\Models\DiscountSetting;
use App\Models\SystemSetting;
use App\Models\User;
use App\Models\Venue;
use Database\Seeders\EmailTemplateSeeder;
use Database\Seeders\PricingMatrixSeeder;
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

        $this->call(GuestBracketSeeder::class);
        $this->call(PricingMatrixSeeder::class);
        $this->seedDiscountSettings();
        $this->seedSystemSettings();
        $this->call(OccasionTypeSeeder::class);
        $this->call(FeatureSeeder::class);
        $this->call(VenueStyleSeeder::class);
        $this->call(EmailTemplateSeeder::class);
        $this->call(BudgetRangeSeeder::class);
        $this->call(PostcodeSeeder::class);
        $this->call(AreaSeeder::class);
        $this->call(RoomSeeder::class);

        $this->call(VenueSeeder::class);
        $this->call(LeadSeeder::class);
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
            ['lead_max_matches', '30', 'leads', 'integer'],
            ['debug_logging_enabled', '0', 'general', 'boolean'],
            ['new_venues_email_password', '0', 'general', 'boolean'],
            ['admin_email', 'admin@partyhelp.com.au', 'general', 'string'],
            ['min_credit_balance', '100', 'credits', 'float'],
        ];

        foreach ($settings as [$key, $value, $group, $type]) {
            SystemSetting::create(compact('key', 'value', 'group', 'type'));
        }
    }
}
