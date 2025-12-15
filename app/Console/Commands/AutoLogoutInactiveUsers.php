<?php

namespace App\Console\Commands;

use App\Models\AgentLogin;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class AutoLogoutInactiveUsers extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'users:auto-logout';

    /**
     * The console command description.
     */
    protected $description = 'Auto logout users inactive for more than configured minutes';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $minutes = config('auso.auto_logout_mins', 45);

        $cutoff = Carbon::now()->subMinutes($minutes)->timestamp;

        $sessions = DB::table('sessions')
            ->whereNotNull('user_id')
            ->where('last_activity', '<', $cutoff)
            ->get();

        if ($sessions->isEmpty()) {
            $this->info('No inactive users found.');
            return Command::SUCCESS;
        }

        foreach ($sessions as $session) {

            $userId = $session->user_id;

            Log::info('Inactive session detected', [
                'session_id'    => $session->id,
                'user_id'       => $userId,
                'last_activity' => $session->last_activity,
                'inactive_sec'  => now()->timestamp - $session->last_activity,
            ]);

            $user = User::find($userId);

            if (!$user) {
                // Remove orphan session
                DB::table('sessions')->where('id', $session->id)->delete();
                continue;
            }

            AgentLogin::where('user_id', $userId)
                ->whereNull('logout_time')
                ->latest('login_time')
                ->first()
                // ?->update(['logout_time' => now()]);
                ?->update(['logout_time' => Carbon::createFromTimestamp($session->last_activity)]);

            Redis::del("softphone_registered:{$userId}");
            Redis::del("user:{$userId}:bound_type");

            DB::table('sessions')->where('id', $session->id)->delete();

            Log::info("Auto-logged out user {$userId}");
        }

        $this->info("Auto logout completed for {$sessions->count()} users.");

        return Command::SUCCESS;
    }
}
