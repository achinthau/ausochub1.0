<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WhatsappWebhookController extends Controller
{
    /**
     * Handle verification handshake from Meta Graph API.
     */
    public function verify(Request $request)
    {
        $mode = $request->query('hub_mode');
        $token = $request->query('hub_verify_token');
        $challenge = $request->query('hub_challenge');

        $verifyToken = config('services.whatsapp.verify_token') ?: env('FACEBOOK_VERIFY_TOKEN');

        if ($mode === 'subscribe' && $token === $verifyToken) {
            return response($challenge, 200)->header('Content-Type', 'text/plain');
        }

        return response('Unauthorized Token Mismatch', 403);
    }

    /**
     * Dispatch incoming payloads to WebJS or Meta API processors based on env configuration.
     */
    public function handle(Request $request)
    {
        $type = config('services.whatsapp.type', 'webjs');

        if ($type === 'meta_api') {
            return $this->handleMetaApi($request);
        }

        return $this->handleWebJs($request);
    }

    /**
     * Process existing WebJS format payloads.
     */
    protected function handleWebJs(Request $request)
    {
//         Log::info('WhatsApp WebJS Webhook Received', $request->all());

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
//                 Log::error('Failed to emit WhatsApp socket event: ' . $e->getMessage());
            }
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * Process WhatsApp Cloud (Meta) API JSON webhook payloads.
     */
    protected function handleMetaApi(Request $request)
    {
//         Log::info('WhatsApp Meta API Webhook Received', $request->all());

        $payload = $request->all();

        if (isset($payload['object']) && $payload['object'] === 'whatsapp_business_account') {
            foreach ($payload['entry'] ?? [] as $entry) {
                foreach ($entry['changes'] ?? [] as $change) {
                    if (($change['field'] ?? '') === 'messages') {
                        $value = $change['value'] ?? [];
                        
                        // Parse contacts map to fetch profile names
                        $contactsMap = [];
                        foreach ($value['contacts'] ?? [] as $contact) {
                            $waId = $contact['wa_id'] ?? null;
                            $name = $contact['profile']['name'] ?? null;
                            if ($waId && $name) {
                                $contactsMap[$waId] = $name;
                            }
                        }

                        // Loop incoming messages
                        foreach ($value['messages'] ?? [] as $msg) {
                            $senderId = $msg['from'] ?? null; // WhatsApp number
                            $messageId = $msg['id'] ?? null;
                            $timestampSec = $msg['timestamp'] ?? time();
                            $sentAt = \Carbon\Carbon::createFromTimestamp($timestampSec);

                            if (!$senderId || !$messageId) {
                                continue;
                            }

                            // Extract message body text
                            $messageText = null;
                            if (($msg['type'] ?? '') === 'text') {
                                $messageText = $msg['text']['body'] ?? '';
                            } elseif (isset($msg['button']['text'])) {
                                $messageText = $msg['button']['text'];
                            } elseif (isset($msg['interactive']['button_reply']['title'])) {
                                $messageText = $msg['interactive']['button_reply']['title'];
                            } else {
                                $messageText = '[Non-text message type: ' . ($msg['type'] ?? 'unknown') . ']';
                            }

                            // Read existing display name from DB if we have one
                            $senderName = \App\Models\WhatsappMetaMessage::where('sender_id', $senderId)
                                ->whereNotNull('sender_name')
                                ->orderByDesc('sent_at')
                                ->value('sender_name');

                            // If not found in DB, use profile name from webhook contact details
                            if (!$senderName) {
                                $senderName = $contactsMap[$senderId] ?? null;
                            }

                            // Store in DB if message_id is unique
                            if (!\App\Models\WhatsappMetaMessage::where('message_id', $messageId)->exists()) {
                                \App\Models\WhatsappMetaMessage::create([
                                    'sender_id' => $senderId,
                                    'sender_name' => $senderName,
                                    'message_text' => $messageText,
                                    'message_id' => $messageId,
                                    'from_me' => false,
                                    'is_read' => false,
                                    'sent_at' => $sentAt,
                                ]);

                                // Back-fill sender name to previous records for this user if we just found it
                                if ($senderName) {
                                    \App\Models\WhatsappMetaMessage::where('sender_id', $senderId)
                                        ->whereNull('sender_name')
                                        ->update(['sender_name' => $senderName]);
                                }

                                // Emit socket event for real-time frontend refresh
                                try {
                                    $socketPort = env('SOCKET_SERVER_PORT', '3000');
                                    $url = "http://127.0.0.1:{$socketPort}/emit";
                                    
                                    \Illuminate\Support\Facades\Http::post($url, [
                                        'event' => 'whatsapp.message',
                                        'data' => [
                                            'chat' => ['id' => $senderId],
                                            'body' => $messageText,
                                            'fromMe' => false,
                                            'lead' => [
                                                'chat' => ['id' => $senderId],
                                                'body' => $messageText,
                                                'fromMe' => false
                                            ]
                                        ]
                                    ]);
                                } catch (\Exception $e) {
//                                     Log::error('Failed to emit WhatsApp Meta socket event: ' . $e->getMessage());
                                }
                            }
                        }
                    }
                }
            }
            return response('EVENT_RECEIVED', 200);
        }

        return response('Not Found', 404);
    }
}
