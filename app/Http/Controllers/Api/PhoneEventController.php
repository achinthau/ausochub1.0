<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use App\Models\PhoneEvent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Receives every phone event fired by the browser softphone (auso-phone / SIP.js).
 *
 * All events share one handler; the event type comes from the route
 * (/phone/events/incoming, /phone/events/hangup, ...) or the `event` body field
 * on the generic endpoint. See Phone/docs/spec.txt §3.
 */
class PhoneEventController extends Controller
{
    public const EVENTS = [
        'incoming',              // inbound call ringing
        'dialing',               // outbound call placed
        'ringing',               // outbound call ringing
        'answered',              // call answered by either party
        'hold',
        'unhold',
        'mute',
        'unmute',
        'transfer_started',
        'transfer_completed',
        'transfer_failed',
        'hangup',
        // Registration
        'registered',
        'unregistered',
        'registration_failed',
        // WebSocket / SIP transport connection
        'connecting',
        'connected',
        'disconnected',
    ];

    /**
     * Handle a phone event.
     *
     * @param  string  $event  Route-provided event type (from defaults).
     */
    public function handle(Request $request, string $event = ''): JsonResponse
    {
        $event = $event ?: (string) $request->input('event', '');

        if (! in_array($event, self::EVENTS, true)) {
            return response()->json([
                'ok'      => false,
                'message' => "Unsupported phone event '{$event}'.",
                'events'  => self::EVENTS,
            ], 422);
        }

        $data = $request->validate([
            'call_id'     => ['nullable', 'string', 'max:191'],
            'direction'   => ['nullable', 'string', 'max:16'],
            'cli'         => ['nullable', 'string', 'max:32'],
            'extension'   => ['nullable', 'string', 'max:16'],
            'duration'    => ['nullable', 'numeric', 'min:0'],
            'target'      => ['nullable', 'string', 'max:32'],
            'occurred_at' => ['nullable', 'date'],
        ]);

        $payload = $request->except('event');

        $record = PhoneEvent::create([
            'event_type'  => $event,
            'call_id'     => $data['call_id'] ?? null,
            'direction'   => $data['direction'] ?? null,
            'cli'         => $data['cli'] ?? null,
            'extension'   => $data['extension'] ?? null,
            'user_id'     => $request->user()?->id,
            'payload'     => $payload,
            'occurred_at' => $data['occurred_at'] ?? now(),
        ]);

        Log::info("Phone event [{$event}]", $payload);

        $this->forward($event, $data, $payload);

        return response()->json([
            'ok'          => true,
            'event'       => $event,
            'event_id'    => $record->id,
            'received_at' => now()->toISOString(),
        ], 201);
    }

    /**
     * Generic receiver — the phone may POST /phone/events with `event` in the body.
     */
    public function store(Request $request): JsonResponse
    {
        return $this->handle($request);
    }

    /**
     * Map a browser-softphone event onto the existing call-server endpoints so
     * the CRM behaves exactly as it does when the same call is handled by a
     * desk softphone (MicroSIP/Zoiper) via the call server.
     *
     * The call-server endpoints (/api/call-dialed, /api/call-answered,
     * /api/get-number, /api/get-answered-number, /api/get-missed-call-number,
     * /api/call-disconnected) are NOT modified here — we only invoke them with
     * the parameters they require.
     */
    private function forward(string $event, array $data, array $payload): void
    {
        $call      = is_array($payload['call'] ?? null) ? $payload['call'] : [];
        $callId    = $data['call_id']    ?? $call['call_id']         ?? null;
        $cli       = $data['cli']        ?? $call['cli']             ?? $call['remote_identity'] ?? null;
        $direction = $data['direction']  ?? $call['direction']       ?? null;
        $extension = $data['extension']  ?? $this->userExtension();
        $duration  = $data['duration']   ?? $call['duration']        ?? 0;
        $answered  = ! empty($call['answered_at']);

        $queue = $this->queueFor($extension);

        switch ($event) {
            case 'dialing':
                // Outbound call placed — same hook the call server fires.
                if (! $cli || ! $extension) {
                    return;
                }
                $this->post('/api/call-dialed', [
                    'ani'       => $cli,
                    'dnis'      => $cli,
                    'agent'     => $extension,
                    'unique_id' => $callId ?: 'wr-'.uniqid(),
                    'queuename' => $queue,
                    'type'      => 1,
                ]);
                break;

            case 'answered':
                if (! $cli || ! $extension) {
                    return;
                }
                if ($direction === 'inbound') {
                    $this->post('/api/get-answered-number', [
                        'phone_number' => $cli,
                        'extention'    => $extension,
                        'unique_id'    => $callId ?: 'wr-'.uniqid(),
                        'queuename'    => $queue,
                    ]);
                } else {
                    $this->post('/api/call-answered', [
                        'ani'       => $cli,
                        'dnis'      => $cli,
                        'agent'     => $extension,
                        'unique_id' => $callId ?: 'wr-'.uniqid(),
                        'queuename' => $queue,
                    ]);
                }
                break;

            case 'incoming':
                // Inbound leg arrives — screen-pop / lead bootstrap as the
                // call server does for desk phones.
                if (! $cli) {
                    return;
                }
                $this->post('/api/get-number', ['phone_number' => $cli]);
                break;

            case 'hangup':
                if (! $callId) {
                    return;
                }
                if ($direction === 'inbound' && ! $answered && (int) $duration === 0 && $extension) {
                    $this->post('/api/get-missed-call-number', [
                        'customer'  => $cli,
                        'dstnumber' => $extension,
                    ]);
                }
                $this->post('/api/call-disconnected', [
                    'unique_id' => $callId,
                    'queuename' => $queue,
                ]);
                break;
        }
    }

    private function userExtension(): ?string
    {
        $user = auth()->user();

        return $user?->extension ?? $user?->agent?->extension ?? null;
    }

    /**
     * Resolve the agent's current active queue/skill. Used to keep the
     * queue-scoped counters consistent with what the call server would send.
     */
    private function queueFor(?string $extension): ?string
    {
        $skill = auth()->user()?->currentQueues()->active()->pluck('skill')->unique()->first();

        if ($skill) {
            return (string) $skill;
        }

        if ($extension) {
            $agentSkill = Agent::where('extension', $extension)->first()
                ?->currentActiveQueues()->pluck('skill')->first();

            if ($agentSkill) {
                return (string) $agentSkill;
            }
        }

        return null;
    }

    /**
     * Call an existing (unchanged) call-server endpoint with the parameters it
     * requires. Failures are logged but never break the phone event response.
     */
    private function post(string $endpoint, array $params): void
    {
        try {
            $response = Http::timeout(5)
                ->acceptJson()
                ->post(rtrim(url('/'), '/').$endpoint, $params);

            if (! $response->successful()) {
                Log::warning("Forwarded phone event to {$endpoint} returned {$response->status()}", $params);
            }
        } catch (\Throwable $e) {
            Log::warning("Could not forward phone event to {$endpoint}: {$e->getMessage()}");
        }
    }
}