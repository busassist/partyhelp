<?php

namespace Database\Seeders;

use App\Models\OccasionType;
use Illuminate\Database\Seeder;

class OccasionTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = config('partyhelp.occasion_types', []);
        $sortOrder = 0;

        foreach ($types as $key => $label) {
            OccasionType::updateOrCreate(
                ['key' => $key],
                ['label' => $label, 'sort_order' => $sortOrder++, 'is_active' => true]
            );
        }
    }
}
