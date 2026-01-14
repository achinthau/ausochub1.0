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


        Log::info("User {$user->id} logged out and softphone disconnected");

        // dd('Logout listener works');

    }
}
