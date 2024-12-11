<?php

namespace App\Listeners;

use App\Models\AgentLogin;
use App\Models\AgentLoginLogoutDetail;
use Illuminate\Auth\Events\Logout;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class RecordLogoutTime
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
    public function handle(Logout $event)
    {
        AgentLogin::where('user_id', $event->user->id)
            ->latest('login_time')
            ->first()
            ->update(['logout_time' => now()]);
    }
}
