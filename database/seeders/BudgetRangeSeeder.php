<?php

namespace Database\Seeders;

use App\Models\BudgetRange;
use Illuminate\Database\Seeder;

class BudgetRangeSeeder extends Seeder
{
    public function run(): void
    {
        $ranges = [
            ['label' => '$1,500 - $3,000', 'min_value' => 1500, 'max_value' => 3000, 'sort_order' => 1],
            ['label' => '$3,000 - $5,000', 'min_value' => 3000, 'max_value' => 5000, 'sort_order' => 2],
            ['label' => '$5,000 - $10,000', 'min_value' => 5000, 'max_value' => 10000, 'sort_order' => 3],
            ['label' => '$10,000 - $15,000', 'min_value' => 10000, 'max_value' => 15000, 'sort_order' => 4],
            ['label' => '$15,000 - $25,000', 'min_value' => 15000, 'max_value' => 25000, 'sort_order' => 5],
            ['label' => '$25,000+', 'min_value' => 25000, 'max_value' => 999999, 'sort_order' => 6],
        ];

        foreach ($ranges as $range) {
            BudgetRange::updateOrCreate(
                ['label' => $range['label']],
                array_merge($range, ['is_active' => true])
            );
        }
    }
}
