<?php

namespace App\Console\Commands;

use App\Http\Controllers\PartyhelpFormConfigController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

/**
 * Verify venue styles and form config API for the WordPress plugin.
 */
class CheckPartyhelpFormConfigCommand extends Command
{
    protected $signature = 'partyhelp:check-form-config';

    protected $description = 'Check venue_styles table and form config API response for WordPress plugin sync';

    public function handle(): int
    {
        $this->info('Checking Partyhelp form config (WordPress plugin sync)...');

        if (! Schema::hasTable('venue_styles')) {
            $this->error('Table venue_styles does not exist. Run: php artisan migrate');
            return self::FAILURE;
        }

        $count = \App\Models\VenueStyle::where('is_active', true)->count();
        $this->info("Venue styles (active): {$count}");

        if ($count === 0) {
            $this->warn('No active venue styles. Add styles in Admin → Venue Styles or run: php artisan db:seed --class=VenueStyleSeeder');
        }

        $controller = app(PartyhelpFormConfigController::class);
        $response = $controller->index();
        $data = $response->getData(true);

        $venueStyles = $data['venue_styles'] ?? [];
        $this->info('API index() venue_styles count: ' . count($venueStyles));

        if (count($venueStyles) > 0) {
            $this->line('Sample: ' . json_encode($venueStyles[0], JSON_UNESCAPED_SLASHES));
        }

        return self::SUCCESS;
    }
}
