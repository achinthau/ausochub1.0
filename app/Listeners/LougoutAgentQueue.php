<?php

namespace App\Listeners;

use App\Events\LogoutCallQueue;
use App\Repositories\ApiManager;
use Illuminate\Auth\Events\Logout;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Auth;

class LougoutAgentQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\LogoutCallQueue  $event
     * @return void
     */
    public function handle(Logout $event)
    {
        $skills = $event->user->currentQueues()->active()->get();
        if (count($skills)) {
            $skillNames = $skills->pluck('skill')->toArray();

        $data = [
            [
                'name' => 'extension',
                'contents' => Auth::user()->agent->extension
            ],
            [
                'name' => 'type',
                // 'contents' => 'SIP'
                 'contents' =>Auth::user()->agent->extensionDetails->exten_type
            ],
            [
                'name' => 'agentip',
                'contents' => '123.231.121.61'
            ],
            [
                'name' => 'queue',
                'contents' => implode(",",$skillNames)
            ],
            [
                'name' => 'action',
                'contents' => 'remove'
            ],
            [
                'name' => 'agentid',
                'contents' => Auth::user()->agent_id
            ]
        ];
        ApiManager::updateSkill($data);
        }
        
        
    }
}
