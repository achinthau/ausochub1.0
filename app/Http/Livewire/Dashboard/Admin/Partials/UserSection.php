<?php

namespace App\Http\Livewire\Dashboard\Admin\Partials;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;


use App\Models\Agent;
use App\Repositories\ApiManager;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Throwable;

class UserSection extends Component
{

    public $dialerCallCount = [];
    public $boundType;

    public function render()
    {
//         \Log::info('Render started: Fetching agents with relations');

        $users = Agent::with(['currentActiveQueues', 'extensionDetails', 'user'])->get();
//         \Log::info('Agents loaded', ['agent_count' => $users->count()]);

        $raisedHands = [];

        $loggedUserIds = DB::table('sessions')
            ->whereNotNull('user_id')
            ->pluck('user_id')
            ->unique()
            ->toArray();

//         \Log::info('Logged-in users fetched', ['ids' => $loggedUserIds]);

        $inboundUsers = [];
        $dialerUsers = [];
        $dialerCallCounts = [];
        $extensionStatuses = [];

        foreach ($users as $agent) {
            $userId = optional($agent->user)->id;

            if ($userId) {
                $key = "hand_raised:{$userId}";
                if (Redis::get($key)) {
                    $raisedHands[$userId] = true;
//                     \Log::info('Hand raised detected', ['user_id' => $userId]);
                }
            }

            if (in_array($userId, $loggedUserIds)) {
                $key = "user:{$userId}:bound_type";
                $callDirection = Redis::get($key);

//                 \Log::info('User bound type found', ['user_id' => $userId, 'bound_type' => $callDirection]);

                if ($callDirection == 'inbound') {
                    $inboundUsers[] = $userId;
                } elseif ($callDirection == 'dialer') {
                    $dialerUsers[] = $userId;

                    // Outbound agents publish their live status per extension.
                    $status = $this->extensionStatus(optional($agent->user)->extension);

                    if ($status !== null) {
                        $extensionStatuses[$userId] = $status;
                    }
                }
            }

            $extension = optional($agent->user)->extension;

            if ($extension) {
                $count = DB::connection('mysql-old')
                    ->table('callcount')
                    ->whereNotNull('app')
                    ->where('callcount', $extension)
                    ->count();

                $dialerCallCounts[$userId] = $count;
//                 \Log::info('Dialer call count fetched', ['user_id' => $userId, 'extension' => $extension, 'count' => $count]);
            } else {
                $dialerCallCounts[$userId] = 0;
//                 \Log::info('No extension found, setting dialer count to 0', ['user_id' => $userId]);
            }
        }

        $this->dialerCallCounts = $dialerCallCounts;

//         \Log::info('Final data prepared', [
//             'inbound_users' => $inboundUsers,
//             'dialer_users' => $dialerUsers,
//             'raised_hands' => $raisedHands,
//         ]);

//         \Log::info('Render completed, returning view');

        return view(
            'livewire.dashboard.admin.partials.user-section',
            [
                'users' => $users,
                'loggedUserIds' => $loggedUserIds,
                'inboundUsers' => $inboundUsers,
                'dialerUsers' => $dialerUsers,
                'raisedHands' => $raisedHands,
                'extensionStatuses' => $extensionStatuses,
            ]
        );
    }

    /**
     * Read the live call status the dialer publishes for an extension.
     *
     * The key is an unprefixed hash in Redis DB 9.
     *
     * @return array{status: string, direction: string}|null
     */
    protected function extensionStatus(?string $extension): ?array
    {
        $extension = trim((string) $extension);

        if ($extension === '') {
            return null;
        }

        try {
            $values = Redis::connection('call_links')->hgetall("auso:extension:{$extension}");
        } catch (Throwable $e) {
            Log::warning('dashboard.extension_status.redis_lookup_failed', [
                'extension' => $extension,
                'error' => $e->getMessage(),
            ]);

            return null;
        }

        if (!is_array($values) || $values === []) {
            return null;
        }

        $status = strtoupper(trim((string) ($values['status'] ?? '')));

        // "IDEL" means idle: no status indicator.
        if ($status === '' || $status === 'IDEL') {
            return null;
        }

        return [
            'status' => $status,
            'direction' => strtoupper(trim((string) ($values['direction'] ?? ''))),
        ];
    }



    public function listenCall($extension, $extenType, $action)
    {
        $data = [
            [
                'name' => 'agent',
                'contents' => $extension
            ],
            [
                'name' => 'extensionType',
                'contents' => $extenType
            ],
            [
                'name' => 'supervisor',
                'contents' => Auth::user()->extension ?? '999'
            ],
            [
                'name' => 'action',
                'contents' => $action
            ],

        ];
//         Log::info('Calling ApiManager::listenCall with data: ', $data);
        ApiManager::listentCall($data);
    }
}
