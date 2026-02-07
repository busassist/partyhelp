<?php

namespace Database\Seeders;

use App\Models\Feature;
use Illuminate\Database\Seeder;

class FeatureSeeder extends Seeder
{
    private const DEFAULT_FEATURES = [
        'av_equipment' => 'AV Equipment',
        'dance_floor' => 'Dance Floor',
        'private_bar' => 'Private Bar',
        'outdoor_access' => 'Outdoor Access',
        'stage' => 'Stage',
        'projector' => 'Projector',
        'sound_system' => 'Sound System',
        'catering' => 'In-house Catering',
    ];

    public function run(): void
    {
        $sortOrder = 0;

        foreach (self::DEFAULT_FEATURES as $key => $label) {
            Feature::updateOrCreate(
                ['key' => $key],
                ['label' => $label, 'sort_order' => $sortOrder++, 'is_active' => true]
            );
        }
    }
}
