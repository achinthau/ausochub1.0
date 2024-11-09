<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Repositories\ApiManager;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Auth;

class AutoLoginAgentQueue
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
     * @param  object  $event
     * @return void
     */
    public function handle(Login $event)
    {
        if (config('auso.allow_skill_change') == false && $event->user->user_type_id == 4) {
            # code...

            $skills = $event->user->skills;
            if (count($skills->toArray())) {
                //$skillNames = $skills->pluck('skill')->toArray();

                $data = [
                    [
                        'name' => 'extension',
                        'contents' => Auth::user()->agent->extension
                    ],
                    [
                        'name' => 'type',
                        // 'contents' => 'SIP'
                        'contents' => Auth::user()->agent->extensionDetails->exten_type
                    ],
                    [
                        'name' => 'agentip',
                        'contents' => '123.231.121.61'
                    ],
                    [
                        'name' => 'queue',
                        //'contents' => implode(",", $skillNames)
                        'contents' => $event->user->skills->skills
                    ],
                    [
                        'name' => 'action',
                        'contents' => 'add'
                    ],
                    [
                        'name' => 'agentid',
                        'contents' => Auth::user()->agent_id
                    ],
                    [
                        'name' => 'crm_token',
                        'contents' =>  session()->getId()
                    ],
                ];
                ApiManager::updateSkill($data);
            }
        }
    }
}
