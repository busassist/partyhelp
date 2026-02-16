<?php

namespace App\Providers;

use App\Filament\Livewire\AreasTable;
use App\Filament\Livewire\PostcodesTable;
use App\Mail\VenuePasswordResetEmail;
use App\Models\ScheduleRunLog;
use App\Models\User;
use App\Services\DebugLogService;
use Filament\Facades\Filament;
use Illuminate\Auth\Events\PasswordResetLinkSent;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Console\Events\ScheduledTaskFailed;
use Illuminate\Console\Events\ScheduledTaskFinished;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Lang;
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
        ResetPassword::toMailUsing(function (object $notifiable, string $token) {
            $expire = (int) config('auth.passwords.'.config('auth.defaults.passwords').'.expire', 60);

            if ($notifiable instanceof User && $notifiable->role === 'venue') {
                $panel = Filament::getPanel('venue');
                $url = $panel->getResetPasswordUrl($token, $notifiable);

                return new VenuePasswordResetEmail(
                    $url,
                    $expire,
                    $notifiable->getEmailForPasswordReset(),
                );
            }

            $url = url(route('password.reset', [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ], false));

            return (new MailMessage)
                ->subject(Lang::get('Reset Password Notification'))
                ->line(Lang::get('You are receiving this email because we received a password reset request for your account.'))
                ->action(Lang::get('Reset Password'), $url)
                ->line(Lang::get('This password reset link will expire in :count minutes.', ['count' => $expire]))
                ->line(Lang::get('If you did not request a password reset, no further action is required.'));
        });

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
