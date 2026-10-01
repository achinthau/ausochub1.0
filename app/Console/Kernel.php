<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();

        $schedule->command('daily:call-summary')->dailyAt('01:30');

        // for callback notifications
        $schedule->command('callbacks:check')->everyMinute();

        // keep-alive fallback for the continuous dialer services (they are
        // primarily run as daemons; the scheduled tick only fires when the
        // daemon is down because both honour the same Redis lock)
        $schedule->command('dialer:numbers:dispatch', ['--once' => true])->everyMinute()->withoutOverlapping();
        $schedule->command('dialer:counts:sync', ['--once' => true])->everyMinute()->withoutOverlapping();

        //logout users
        $schedule->command('users:auto-logout')
        ->everyMinute()
        ->withoutOverlapping()
        // ->onOneServer()
        ->appendOutputTo(storage_path('logs/auto-logout.log'));
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
