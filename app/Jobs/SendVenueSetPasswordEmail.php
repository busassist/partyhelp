<?php

namespace App\Jobs;

use App\Mail\VenueSetPasswordEmail;
use App\Models\Venue;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;

class SendVenueSetPasswordEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Venue $venue,
    ) {}

    public function handle(): void
    {
        $user = $this->venue->user;
        if (! $user || ! $user->email) {
            return;
        }

        $token = Password::broker()->createToken($user);
        $url = url('/venue/set-password?' . http_build_query([
            'token' => $token,
            'email' => $user->email,
        ]));

        Mail::mailer('sendgrid')
            ->to($user->email)
            ->send(new VenueSetPasswordEmail($this->venue, $url));
    }
}
