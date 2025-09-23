<?php

namespace App\Http\Livewire\Dashboard\Admin\Partials;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;


use App\Models\Agent;
use App\Repositories\ApiManager;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class UserSection extends Component
{


    public function render()
    {
        $users = Agent::with(['currentActiveQueues', 'extensionDetails', 'user'])->get();
        $raisedHands = [];

        $loggedUserIds = DB::table('sessions')
        ->whereNotNull('user_id')
        ->pluck('user_id')
        ->unique()
        ->toArray();
        
        $inboundUsers = [];
    $dialerUsers = [];

         // Select Redis DB 1

    foreach ($users as $agent) {
        $userId = optional($agent->user)->id;
        if ($userId) {
            // Redis::select(0);
            $key = "hand_raised:{$userId}";
            if (Redis::get($key)) {
                $raisedHands[$userId] = true;
            }
        }

        if ($agent->user) {
            // Redis::select(0);
            $key = "user:{$userId}:bound_type";
            $callDirection = Redis::get($key);
            

            if ($callDirection == 'inbound') {
                $inboundUsers[] = $agent->user->id;
                // dd('inbound');
            } elseif ($callDirection == 'dialer') {
                $dialerUsers[] = $agent->user->id;
                // dd('dialer');
            }

            
        }


    }
    // dd($inboundUsers);

        return view(
            'livewire.dashboard.admin.partials.user-section',
            ['users' => $users,'loggedUserIds' => $loggedUserIds,'inboundUsers' => $inboundUsers,
        'dialerUsers' => $dialerUsers, 'raisedHands' => $raisedHands,]
        );
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
        Log::info('Calling ApiManager::listenCall with data: ', $data);
        ApiManager::listentCall($data);
    }
}