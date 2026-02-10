<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\Postcode;
use Illuminate\Database\Seeder;

class AreaSeeder extends Seeder
{
    /** Area name => list of suburb names (must match PostcodeSeeder suburbs). */
    private const AREA_SUBURBS = [
        'CBD' => ['Melbourne', 'Docklands', 'East Melbourne'],
        'INNER SOUTH' => ['South Melbourne', 'Port Melbourne', 'Albert Park'],
        'INNER SOUTH EAST' => ['South Yarra', 'St Kilda', 'Prahran', 'Windsor', 'Elwood', 'Balaclava'],
        'INNER EAST' => ['Richmond', 'Hawthorn', 'Kew', 'Abbotsford', 'Cremorne', 'Armadale', 'Camberwell'],
        'INNER NORTH' => ['Carlton', 'Collingwood', 'Fitzroy', 'Brunswick', 'North Melbourne', 'Clifton Hill'],
        'INNER WEST' => ['Footscray', 'Kensington', 'Williamstown', 'Brighton'],
    ];

    public function run(): void
    {
        $sortOrder = 0;

        foreach (self::AREA_SUBURBS as $areaName => $suburbNames) {
            $area = Area::updateOrCreate(
                ['name' => $areaName],
                ['sort_order' => $sortOrder++]
            );

            $postcodes = Postcode::whereIn('suburb', $suburbNames)->pluck('id');
            $area->postcodes()->sync($postcodes);
        }
    }
}
