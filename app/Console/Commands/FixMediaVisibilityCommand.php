<?php

namespace App\Console\Commands;

use App\Models\Media;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class FixMediaVisibilityCommand extends Command
{
    protected $signature = 'media:fix-visibility
                            {--dry-run : Show what would be changed without making changes}';

    protected $description = 'Set public visibility on all media files (fixes Access Denied on Spaces)';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $disk = config('filesystems.media_disk', 'spaces');

        $media = Media::all();

        if ($media->isEmpty()) {
            $this->info('No media records found.');

            return self::SUCCESS;
        }

        $this->info("Setting public visibility on {$media->count()} media files ({$disk})" . ($dryRun ? ' (dry run)' : ''));

        $fixed = 0;
        $failed = 0;

        foreach ($media as $item) {
            $path = $item->file_path;

            if ($dryRun) {
                $this->line("  [would fix] {$path}");
                $fixed++;
                continue;
            }

            try {
                Storage::disk($disk)->setVisibility($path, 'public');
                $fixed++;
                $this->line("  [ok] {$path}");
            } catch (\Throwable $e) {
                $failed++;
                $this->error("  [fail] {$path}: {$e->getMessage()}");
            }
        }

        $this->newLine();
        $this->info("Done. Fixed: {$fixed}, Failed: {$failed}");

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }
}
