<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MessengerMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MessengerWebhookController extends Controller
{
    // ─── Webhook Verification ─────────────────────────────────────────────────

    public function verify(Request $request)
    {
        $mode = $request->query('hub_mode');
        $token = $request->query('hub_verify_token');
        $challenge = $request->query('hub_challenge');

        if ($mode === 'subscribe' && $token === env('FACEBOOK_VERIFY_TOKEN')) {
            return response($challenge, 200)->header('Content-Type', 'text/plain');
        }

        return response('Unauthorized Token Mismatch', 403);
    }

    // ─── Incoming Webhook ─────────────────────────────────────────────────────

    public function handleMessage(Request $request)
    {
        $payload = $request->all();

        if (isset($payload['object']) && $payload['object'] === 'page') {
            foreach ($payload['entry'] as $entry) {
                if (isset($entry['messaging'])) {
                    foreach ($entry['messaging'] as $event) {

                        if (isset($event['message']['text'])) {
                            $senderId = $event['sender']['id'];
                            $messageText = $event['message']['text'];
                            $messageId = $event['message']['mid'] ?? null;
                            $timestamp = isset($event['timestamp'])
                                ? \Carbon\Carbon::createFromTimestampMs($event['timestamp'])
                                : now();

                            // Look up any previously saved / manually-set name from DB.
                            $senderName = MessengerMessage::where('sender_id', $senderId)
                                ->whereNotNull('sender_name')
                                ->where('sender_name', '!=', 'Facebook User')
                                ->orderByDesc('sent_at')
                                ->value('sender_name');

                            // If no known name yet, try fetching from Graph API (fast 3s timeout).
                            // Failure is silent — agents can still set names manually via the UI (✏️).
                            if (!$senderName) {
                                $senderName = $this->tryFetchSenderName($senderId);
                            }

                            // Log::info("Messenger: message from {$senderName} ({$senderId}): {$messageText}");
//                             Log::info("Messenger: message from {$request}");

                            // Store in DB — avoid duplicates via message_id
                            if (!$messageId || !MessengerMessage::where('message_id', $messageId)->exists()) {
                                MessengerMessage::create([
                                    'sender_id' => $senderId,
                                    'sender_name' => $senderName ?: null,
                                    'message_text' => $messageText,
                                    'message_id' => $messageId,
                                    'from_me' => false,
                                    'is_read' => false,
                                    'sent_at' => $timestamp,
                                ]);

                                // If we just resolved a real name, back-fill all previous messages
                                if ($senderName && $senderName !== 'Facebook User') {
                                    MessengerMessage::where('sender_id', $senderId)
                                        ->where(function ($q) {
                                            $q->whereNull('sender_name')
                                                ->orWhere('sender_name', 'Facebook User');
                                        })
                                        ->update(['sender_name' => $senderName]);
                                }
                            } elseif ($senderName && $senderName !== 'Facebook User') {
                                // Duplicate message, but we now have a real name — back-fill it
                                MessengerMessage::where('sender_id', $senderId)
                                    ->where(function ($q) {
                                        $q->whereNull('sender_name')
                                            ->orWhere('sender_name', 'Facebook User');
                                    })
                                    ->update(['sender_name' => $senderName]);
                            }
                        }
                    }
                }
            }

            return response('EVENT_RECEIVED', 200);
        }

        return response('Not Found', 404);
    }

    // ─── Send Reply (called from Livewire console) ────────────────────────────

    public function reply(Request $request)
    {
        $request->validate([
            'recipient_id' => 'required|string',
            'message' => 'required|string|max:2000',
        ]);

        $recipientId = $request->input('recipient_id');
        $messageText = $request->input('message');

        $success = $this->sendReply($recipientId, $messageText);

        if ($success) {
            // Persist outgoing message so the chat console shows it
            MessengerMessage::create([
                'sender_id' => $recipientId,
                'message_text' => $messageText,
                'from_me' => true,
                'is_read' => true,
                'sent_at' => now(),
            ]);

            return response()->json(['status' => 'sent']);
        }

        return response()->json(['status' => 'error', 'message' => 'Failed to send via Meta API'], 500);
    }


    // ─── Fetch sender name from Graph API (non-blocking, short timeout) ──────────

    private function tryFetchSenderName(string $senderId): ?string
    {
        $token = env('FACEBOOK_PAGE_ACCESS_TOKEN');
        if (!$token) {
            return null;
        }

        try {
            $response = Http::timeout(3)
                ->get("https://graph.facebook.com/v19.0/{$senderId}", [
                    'fields' => 'name,first_name,last_name',
                    'access_token' => $token,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $name = $data['name']
                    ?? trim(($data['first_name'] ?? '') . ' ' . ($data['last_name'] ?? ''))
                    ?: null;

                if ($name) {
//                     Log::info("Messenger: resolved name '{$name}' for PSID {$senderId}");
                }
                return $name ?: null;
            }

            // Log the exact error so developers know what to fix
            $err = $response->json('error', []);
//             Log::warning("Messenger: Graph API name lookup failed for {$senderId}", [
//                 'http_status' => $response->status(),
//                 'error_code' => $err['code'] ?? null,
//                 'error_sub' => $err['error_subcode'] ?? null,
//                 'error_msg' => $err['message'] ?? $response->body(),
//             ]);
        } catch (\Exception $e) {
//             Log::warning("Messenger: Graph API timeout/exception for {$senderId}: " . $e->getMessage());
        }

        return null;
    }

    // ─── Diagnostic: test Meta API & token health ─────────────────────────────

    public function testMetaApi(Request $request): \Illuminate\Http\JsonResponse
    {
        $token = env('FACEBOOK_PAGE_ACCESS_TOKEN', '');
        $psid = $request->query('psid', '27313143914971210');
        $out = [];

        // 1. Check /me (page details)
        try {
            $meResp = Http::timeout(5)->get('https://graph.facebook.com/v19.0/me', [
                'fields' => 'id,name',
                'access_token' => $token,
            ]);
            $out['page'] = [
                'status' => $meResp->status(),
                'body' => $meResp->json(),
            ];
        } catch (\Exception $e) {
            $out['page'] = ['error' => $e->getMessage()];
        }

        // 2. Check PSID profile
        try {
            $profileResp = Http::timeout(5)->get("https://graph.facebook.com/v19.0/{$psid}", [
                'fields' => 'name,first_name,last_name',
                'access_token' => $token,
            ]);
            $out['profile'] = [
                'psid' => $psid,
                'status' => $profileResp->status(),
                'body' => $profileResp->json(),
            ];
        } catch (\Exception $e) {
            $out['profile'] = ['psid' => $psid, 'error' => $e->getMessage()];
        }

        // 3. Token debug
        try {
            $debugResp = Http::timeout(5)->get('https://graph.facebook.com/debug_token', [
                'input_token' => $token,
                'access_token' => $token,
            ]);
            $out['token_debug'] = $debugResp->json();
        } catch (\Exception $e) {
            $out['token_debug'] = ['error' => $e->getMessage()];
        }

        return response()->json($out, 200, [], JSON_PRETTY_PRINT);
    }

    private function sendReply(string $recipientId, string $messageText): bool
    {
        $url = 'https://graph.facebook.com/v19.0/me/messages';

        $response = Http::withToken(env('FACEBOOK_PAGE_ACCESS_TOKEN'))
            ->post($url, [
                'recipient' => ['id' => $recipientId],
                'message' => ['text' => $messageText],
                'messaging_type' => 'RESPONSE',
            ]);

        if ($response->failed()) {
//             Log::error('Meta Outbound API Error: ' . $response->body());
            return false;
        }

        return true;
    }
}
