<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CallbackCustomer;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Auth;

class CheckCallbackNotifications extends Command
{
    protected $signature = 'callbacks:check';
    protected $description = 'Send notification exactly at scheduled callback time';

    public function handle()
    {
        $now = Carbon::now()->startOfMinute();
        $start = $now;
        $end = $now->copy()->addMinute()->subSecond();

        \Log::info("CheckCallbackNotifications running between {$start} and {$end}");

        $callbacks = CallbackCustomer::whereBetween('callback_at', [$start, $end])
            ->get();

        if ($callbacks->isEmpty()) {
            \Log::info("No callbacks scheduled for this time.");
        } else {
            $callbacks->each(function ($callback) {
                \Log::info("Sending notification for callback id {$callback->id}, lead #{$callback->lead_id}");
                $this->sendSocketNotification($callback);
                \Log::info("Notification sent for callback id {$callback->id}");
            });
        }

        return 0;
    }



    protected function sendSocketNotification($callback)
    {
        $message = "🔔 Callback Time Now for Lead #{$callback->lead_id}";

        try {
            $callbackAt = \Carbon\Carbon::parse($callback->callback_at);

            $response = Http::post(env('SOCKET_SERVER_URL') . '/emit-custom', [
                'event' => 'callback_notify',
                'agent_id' => $callback->agent_id,
                'payload' => [
                    'lead_id' => $callback->lead_id,
                    'callback_at' => $callbackAt->toDateTimeString(),
                    'message' => $message,
                    'type' => 'now',
                ]
            ]);


             // Get the logged-in user's ID

            // 🔁 Switch to Redis DB 6
            Redis::connection()->select(6);

            // 🟢 Store only isReminder = true for that user
            Redis::set("isReminder:{$callback->agent_id}", json_encode([
                'isReminder' => true,
            ]));

            \Log::info("Socket emit response status: {$response->status()}");
            \Log::info("Socket emit response body: {$response->body()}");

        } catch (\Exception $e) {
            \Log::error('Socket emit failed: ' . $e->getMessage());
        }
    }


}
