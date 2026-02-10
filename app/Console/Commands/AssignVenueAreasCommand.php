<?php

namespace App\Console\Commands;

use App\Models\Area;
use App\Models\Postcode;
use App\Models\Venue;
use Illuminate\Console\Command;

class AssignVenueAreasCommand extends Command
{
    protected $signature = 'venues:assign-areas
                            {--dry-run : Show what would be updated without saving}';

    protected $description = 'Set area_id on venues that have none (by matching suburb to area postcodes).';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $venues = Venue::whereNull('area_id')->get();

        if ($venues->isEmpty()) {
            $this->info('All venues already have an area.');

            return self::SUCCESS;
        }

        $defaultAreaId = Area::orderBy('sort_order')->value('id');
        if (! $defaultAreaId) {
            $this->error('No areas found. Run AreaSeeder first: php artisan db:seed --class=AreaSeeder');

            return self::FAILURE;
        }

        $updated = 0;
        foreach ($venues as $venue) {
            $areaId = $this->areaIdForSuburb($venue->suburb) ?? $defaultAreaId;
            if ($areaId) {
                if (! $dryRun) {
                    $venue->update(['area_id' => $areaId]);
                }
                $updated++;
                $areaName = Area::find($areaId)?->name ?? $areaId;
                $this->line("  {$venue->business_name} ({$venue->suburb}) → {$areaName}");
            }
        }

        $this->info($dryRun
            ? "Would assign area to {$updated} venue(s). Run without --dry-run to apply."
            : "Assigned area to {$updated} venue(s).");

        return self::SUCCESS;
    }

    private function areaIdForSuburb(string $suburbName): ?int
    {
        $postcode = Postcode::where('suburb', $suburbName)->first();
        if (! $postcode) {
            return null;
        }

        return $postcode->areas()->first()?->id;
    }
}
