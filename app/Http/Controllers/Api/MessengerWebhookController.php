<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MessengerWebhookController extends Controller
{
    public function verify(Request $request)
    {
        $mode = $request->query('hub_mode');
        $token = $request->query('hub_verify_token');
        $challenge = $request->query('hub_challenge');

        // Check if the token sent by Meta matches the password in your .env
        if ($mode === 'subscribe' && $token === env('FACEBOOK_VERIFY_TOKEN')) {
            // Return the exact challenge string Facebook sent back to them
            return response($challenge, 200)->header('Content-Type', 'text/plain');
        }

        return response('Unauthorized Token Mismatch', 403);
    }

    public function handleMessage(Request $request)
    {
        $payload = $request->all();

        // Check if the webhook event comes from a page interaction
        if (isset($payload['object']) && $payload['object'] === 'page') {
            foreach ($payload['entry'] as $entry) {
                if (isset($entry['messaging'])) {
                    foreach ($entry['messaging'] as $event) {

                        // Check if it's a standard text message event
                        if (isset($event['message']['text'])) {
                            $senderId = $event['sender']['id'];   // Unique user ID to reply to
                            $messageText = $event['message']['text'];

                            // Log it so you can see it in storage/logs/laravel.log
                            Log::info("Received message from {$senderId}: {$messageText}");

                            // Send an instant automatic reply back!
                            $this->sendReply($senderId, "Hello! Your message: '" . $messageText . "' was processed by Laravel.");
                        }
                    }
                }
            }
            // Meta requires a clean 200 OK response to know you received the event
            return response('EVENT_RECEIVED', 200);
        }

        return response('Not Found', 404);
    }

    /**
     * ➕ STEP 3B: TALK BACK TO META USING THE SEND API
     */
    private function sendReply($recipientId, $messageText)
    {
        $url = "https://graph.facebook.com/v19.0/me/messages";

        // Send a secure POST request back to Meta using HTTP Client
        $response = Http::withToken(env('FACEBOOK_PAGE_ACCESS_TOKEN'))
            ->post($url, [
                'recipient' => ['id' => $recipientId],
                'message' => ['text' => $messageText],
                'messaging_type' => 'RESPONSE'
            ]);

        if ($response->failed()) {
            Log::error('Meta Outbound API Error: ' . $response->body());
        }
    }
}
