<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class FilesystemServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->app->booted(function (): void {
            $this->validateSpacesCredentials();
        });
    }

    /**
     * Ensure DigitalOcean Spaces credentials are set when the spaces disk is used.
     * Prevents AWS SDK from falling back to EC2 instance profile (fails on DigitalOcean).
     * Skipped for console commands so migrate, config:clear, etc. work without credentials.
     */
    private function validateSpacesCredentials(): void
    {
        if ($this->app->runningInConsole()) {
            return;
        }

        $mediaDisk = config('filesystems.media_disk', 'spaces');
        if ($mediaDisk !== 'spaces') {
            return;
        }

        $key = config('filesystems.disks.spaces.key');
        $secret = config('filesystems.disks.spaces.secret');

        if (empty($key) || empty($secret)) {
            \Illuminate\Support\Facades\Log::warning(
                'DigitalOcean Spaces credentials missing. Add DO_SPACES_KEY and DO_SPACES_SECRET to .env. '
                . 'Media uploads and proxied image serving will fail until configured.'
            );
        }
    }
}
