<?php

namespace App\Listeners;

use App\Models\AgentLogin;
use App\Models\AgentLoginLogoutDetail;
use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class RecordLoginTime
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(Login $event)
    {
        AgentLogin::create([
            'user_id' => $event->user->id,
            'login_time' => now(),
        ]);
    }
}
