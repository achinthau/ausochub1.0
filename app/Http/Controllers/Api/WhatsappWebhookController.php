<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WhatsappWebhookController extends Controller
{
    public function handle(Request $request)
    {
        Log::info('WhatsApp Webhook Received', $request->all());

        $event = $request->input('event');
        $data = $request->input('data');

        if ($event === 'message_received') {
            try {
                $socketPort = env('SOCKET_SERVER_PORT', '3000');
                $url = "http://127.0.0.1:{$socketPort}/emit";
                
                \Illuminate\Support\Facades\Http::post($url, [
                    'event' => 'whatsapp.message',
                    'data' => $data
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to emit WhatsApp socket event: ' . $e->getMessage());
            }
        }

        return response()->json(['status' => 'success']);
    }
}
