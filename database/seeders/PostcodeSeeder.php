<?php

namespace Database\Seeders;

use App\Models\Postcode;
use Illuminate\Database\Seeder;

class PostcodeSeeder extends Seeder
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

    public function run(): void
    {
        $sortOrder = 0;

        foreach (self::MELBOURNE_SUBURBS as [$suburb, $postcode]) {
            Postcode::updateOrCreate(
                ['suburb' => $suburb, 'postcode' => $postcode],
                ['state' => 'VIC', 'sort_order' => $sortOrder++]
            );
        }
    }
}
