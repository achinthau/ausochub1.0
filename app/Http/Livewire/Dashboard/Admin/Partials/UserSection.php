<?php

namespace App\Http\Livewire\Dashboard\Admin\Partials;
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

        Redis::select(1); // Select Redis DB 1

    foreach ($users as $agent) {
        $userId = optional($agent->user)->id;
        if ($userId) {
            $key = "hand_raised:{$userId}";
            if (Redis::get($key)) {
                $raisedHands[$userId] = true;
            }
        }
    }

        return view(
            'livewire.dashboard.admin.partials.user-section',
            ['users' => $users, 'raisedHands' => $raisedHands,]
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