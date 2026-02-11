<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

class ProcessQueueCommand extends Command
{
    public const LOCK_KEY = 'partyhelp:queue_processor';

    public const EXIT_ALREADY_RUNNING = 2;

    protected $signature = 'queue:process
                            {--max-time=240 : Max seconds to run the worker (scheduler uses 240, button uses 60)}';

    protected $description = 'Process the queue (stop when empty) with a lock so only one processor runs at a time.';

    public function handle(): int
    {
        $maxTime = (int) $this->option('max-time');
        $lock = Cache::lock(self::LOCK_KEY, 300);

        if (! $lock->get()) {
            $this->warn('Queue processor is already running.');

            return self::EXIT_ALREADY_RUNNING;
        }

        try {
            Artisan::call('queue:work', [
                '--stop-when-empty' => true,
                '--max-time' => $maxTime,
            ]);

            return self::SUCCESS;
        } finally {
            $lock->release();
        }
    }
}
