<?php

namespace App\Console\Commands;

use App\Repositories\ApiManager;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AgentQueueAutoLogout extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:agent-queue-autologout';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if (config('auso.auto_logout_queue')) {
            $minAgo = Carbon::now()->subMinutes(config('auso.auto_logout_mins'))->timestamp;
            $sessions = DB::table('sessions')
                ->where('last_activity', '<', $minAgo)
                ->get();

            $sessionStrings = implode(',', $sessions->pluck('id')->toArray());


            if ($sessions->count() > 0) {
                $data = [
                    [
                        'name' => 'logout_sessions',
                        'contents' => $sessionStrings
                    ]
                ];
                ApiManager::autoLogoutSession($data);
            }





            return Command::SUCCESS;
        }
        print_r('auto_logout_queue false');

        return Command::INVALID;
    }
}
