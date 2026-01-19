<?php

namespace App\Listeners;

use App\Models\AgentLogin;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class RecordLogoutTime
{
    /**
     * Handle the event.
     */
    public function handle(Logout $event)
    {
        // 1️⃣ Update agent logout time
        AgentLogin::where('user_id', $event->user->id)
            ->latest('login_time')
            ->first()
            ?->update(['logout_time' => now()]);

        $user = $event->user;

        if (!$user) {
            return;
        }

        // 2️⃣ Load extension relationship
        $user->load('extensionData');

        if ($user->extensionData) {

            $exten = $user->extension;
            $type = strtolower(config('auso.phone_type'));

                if (str_contains($type, 'microsip')) {
                    $type = 'microsip';
                } else  {
                    $type = 'zoiper';
                }

            // 3️⃣ Disconnect softphone
            try {
                if ($type == 'microsip') {
                    Http::timeout(3)->post(
                        'http://127.0.0.1:5001/remove/microsip'
                        // ['exten' => $exten]
                    );
                    dd('microsip logout called');
                }

                if ($type == 'zoiper') {
                    Http::timeout(3)->post(
                        'http://127.0.0.1:5001/remove/zoiper5'
                        // ['exten' => $exten]
                    );
                }
            } catch (\Throwable $e) {
                Log::error('Softphone disconnect failed: ' . $e->getMessage());
            }
        }

        // 4️⃣ Clear Redis / Cache
        // Cache::forget('softphone_' . $user->id);
        // Redis::del("user:{$user->id}:bound_type");
        Redis::del("softphone_registered:$user->id");

        // Remove all agent_on_call cache entries for this agent
        // Keys may be created by external systems with different prefixes
        if ($user->agent_id) {
            $redis = Redis::connection()->client();
            
            // Temporarily disable Laravel's prefix to search all keys
            $currentPrefix = $redis->getOption(\Redis::OPT_PREFIX);
            $redis->setOption(\Redis::OPT_PREFIX, '');
            
            $redis->select(1); // Same database as used in api.php
            
            // Pattern to match (without any prefix)
            $pattern = "*agent_on_call-{$user->agent_id}-*";
            
            // Lua script to find and delete keys matching the pattern
            $luaScript = <<<'LUA'
                local keys = redis.call('keys', ARGV[1])
                local deleted = 0
                if #keys > 0 then
                    deleted = redis.call('del', unpack(keys))
                end
                return deleted
LUA;
            
            try {
                $deletedCount = $redis->eval($luaScript, [$pattern], 0);
                Log::info('Logout - Deleted agent_on_call Redis keys:', [
                    'user_id' => $user->id,
                    'agent_id' => $user->agent_id,
                    'deleted_count' => $deletedCount
                ]);
            } catch (\Exception $e) {
                Log::error('Logout - Failed to delete agent_on_call keys:', [
                    'user_id' => $user->id,
                    'agent_id' => $user->agent_id,
                    'error' => $e->getMessage()
                ]);
            } finally {
                // Restore Laravel's prefix
                $redis->setOption(\Redis::OPT_PREFIX, $currentPrefix);
            }
        }


        Log::info("User {$user->id} logged out and softphone disconnected");

        // dd('Logout listener works');

    }
}
