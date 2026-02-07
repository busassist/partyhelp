<?php

namespace App\Console\Commands;

use App\Models\Media;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class MigrateMediaToSpacesCommand extends Command
{
    protected $signature = 'media:migrate-to-spaces
                            {--dry-run : Show what would be migrated without making changes}';

    protected $description = 'Migrate existing media files from local/public disk to DigitalOcean Spaces';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $sourceDisk = 'public';
        $targetDisk = config('filesystems.media_disk', 'spaces');

        if ($targetDisk === $sourceDisk) {
            $this->warn('MEDIA_DISK is already "public". No migration needed.');

            return self::SUCCESS;
        }

        $media = Media::all();

        if ($media->isEmpty()) {
            $this->info('No media records found. Nothing to migrate.');

            return self::SUCCESS;
        }

        $this->info("Migrating {$media->count()} media files from {$sourceDisk} to {$targetDisk}." . ($dryRun ? ' (dry run)' : ''));

        $migrated = 0;
        $skipped = 0;
        $failed = 0;

        foreach ($media as $item) {
            $path = $item->file_path;

            if (! Storage::disk($sourceDisk)->exists($path)) {
                if (Storage::disk($targetDisk)->exists($path)) {
                    $skipped++;
                    $this->line("  [skip] {$path} (already on target)");
                } else {
                    $failed++;
                    $this->warn("  [fail] {$path} (not found on source)");
                }

                continue;
            }

            if ($dryRun) {
                $this->line("  [would migrate] {$path}");
                $migrated++;
                continue;
            }

            try {
                $contents = Storage::disk($sourceDisk)->get($path);
                Storage::disk($targetDisk)->put($path, $contents, 'public');
                Storage::disk($sourceDisk)->delete($path);
                $migrated++;
                $this->line("  [ok] {$path}");
            } catch (\Throwable $e) {
                $failed++;
                $this->error("  [fail] {$path}: {$e->getMessage()}");
            }
        }

        $this->newLine();
        $this->info("Done. Migrated: {$migrated}, Skipped: {$skipped}, Failed: {$failed}");

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }
}
