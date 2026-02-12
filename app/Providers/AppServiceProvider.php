<?php

namespace App\Providers;

use App\Filament\Livewire\AreasTable;
use App\Filament\Livewire\PostcodesTable;
use App\Models\ScheduleRunLog;
use App\Models\User;
use App\Services\DebugLogService;
use Illuminate\Auth\Events\PasswordResetLinkSent;
use Illuminate\Console\Events\ScheduledTaskFailed;
use Illuminate\Console\Events\ScheduledTaskFinished;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Livewire::component('areas-table', AreasTable::class);
        Livewire::component('postcodes-table', PostcodesTable::class);

        $this->app['events']->listen(ScheduledTaskFinished::class, function (ScheduledTaskFinished $event): void {
            $task = $event->task;
            ScheduleRunLog::create([
                'task_key' => $task->mutexName(),
                'task_display_name' => $task->getSummaryForDisplay(),
                'status' => 'finished',
                'message' => null,
                'ran_at' => now(),
            ]);
        });

        $this->app['events']->listen(ScheduledTaskFailed::class, function (ScheduledTaskFailed $event): void {
            $task = $event->task;
            ScheduleRunLog::create([
                'task_key' => $task->mutexName(),
                'task_display_name' => $task->getSummaryForDisplay(),
                'status' => 'failed',
                'message' => $event->exception->getMessage(),
                'ran_at' => now(),
            ]);
        });

        $this->app['events']->listen(PasswordResetLinkSent::class, function (PasswordResetLinkSent $event): void {
            $user = $event->user;
            $context = [
                'to' => $user->getEmailForPasswordReset(),
            ];
            if ($user instanceof User) {
                $context['role'] = $user->role;
            }
            DebugLogService::logEmailSent('password_reset', $context);
        });
    }
}
