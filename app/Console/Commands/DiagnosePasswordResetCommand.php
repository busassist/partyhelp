<?php

namespace App\Console\Commands;

use App\Models\DebugLogEntry;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DiagnosePasswordResetCommand extends Command
{
    protected $signature = 'password-reset:diagnose {email : The email address that requested the reset}';

    protected $description = 'Check why a password reset email may not have been sent (user, venue access, queue, logs)';

    public function handle(): int
    {
        $email = $this->argument('email');

        $this->info("Diagnosing password reset for: {$email}");
        $this->newLine();

        $user = User::where('email', $email)->with('venue')->first();
        if (! $user) {
            $this->warn('No user found with this email. Reset link is only sent for existing accounts.');
            $this->suggestQueueAndFailedJobs();

            return self::SUCCESS;
        }

        $canAccessVenue = $user->role === 'venue' && $user->venue && $user->venue->status === 'active';

        $this->table(
            ['Field', 'Value'],
            [
                ['User ID', $user->id],
                ['Role', $user->role],
                ['Has venue relation?', $user->venue ? 'yes' : 'no'],
                ['Venue status', $user->venue ? $user->venue->status : 'n/a'],
                ['Can access venue panel?', $canAccessVenue ? 'yes' : 'no'],
                ['Can access admin panel?', $user->role === 'admin' ? 'yes' : 'no'],
            ]
        );

        if ($user->role === 'venue' && ! $canAccessVenue) {
            $this->newLine();
            $this->warn('This venue user cannot access the venue panel (no venue or venue not active).');
            $this->warn('Filament does not send a reset email when the user cannot access the current panel.');
            $this->line('Fix: ensure the venue exists and venue status is "active", then try the reset again.');
        }

        $this->newLine();
        $this->checkDebugLog($email);
        $this->suggestQueueAndFailedJobs();

        return self::SUCCESS;
    }

    private function checkDebugLog(string $email): void
    {
        if (! \App\Services\DebugLogService::isEnabled()) {
            $this->line('Debug logging is disabled. No email_sent entries to show.');
            return;
        }

        $entries = DebugLogEntry::where('type', 'email_sent')
            ->where('payload->email', 'password_reset')
            ->where('payload->to', $email)
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        if ($entries->isEmpty()) {
            $this->line('No password_reset email_sent log found for this address (either not sent or debug was off at the time).');
            return;
        }

        $this->info('Recent password_reset email_sent log entries:');
        foreach ($entries as $e) {
            $this->line('  ID ' . $e->id . ' at ' . $e->created_at?->toDateTimeString() . ' — ' . json_encode($e->payload));
        }
    }

    private function suggestQueueAndFailedJobs(): void
    {
        $this->newLine();
        $this->line('Password reset uses a <comment>queued</comment> notification. If the queue worker is not running, the email will not be sent.');
        $pending = DB::table('jobs')->count();
        $failed = DB::table('failed_jobs')->count();
        $this->line("  Pending jobs in queue: {$pending}");
        $this->line("  Failed jobs: {$failed}");
        if ($failed > 0) {
            $this->warn('  Run: php artisan queue:failed to see recent failures.');
        }
        $this->line('  Run: php artisan queue:work (or ensure a worker is running on Forge) to process the queue.');
    }
}
