<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;

class LogUserLogin
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        // update last login
        $event->user->update([
            'last_login_at' => now(),
            'last_login_ip' => request()->ip()
        ]);

        $event->user->logins()->create([
            // 'guard'        => $event->guard,
            'ip_address'   => request()->ip(),
            'user_agent'   => request()->userAgent(),
            'logged_in_at' => now(),
        ]);
    }
}
