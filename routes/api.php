<?php

use App\Events\CallAnswered;
use App\Http\Requests\StoreAnsweredCall;
use App\Models\Agent;
use App\Models\ItemMaster;
use App\Models\Lead;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AsteriskEventController;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use App\Repositories\ApiManager;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::get('/items', function (Request $request) {
    return ItemMaster::query()
        // ->join('cities','cities.id','hotels.city_id')
        ->selectRaw("id,descr,CONCAT ('Rs. ',ROUND(retail1,2)) as description ")
        ->orderBy('descr')
        ->when(
            $request->search,
            fn (Builder $query) => $query
                ->where('descr', 'like', "%{$request->search}%")
                ->orWhere('barcode', 'like', "%{$request->search}%")
        )
        ->when(
            $request->exists('selected'),
            fn (Builder $query) => $query->whereIn('id', $request->selected),
            fn (Builder $query) => $query->limit(10)
        )
        ->get();
})->name('api.items.index');

Route::post('/call-answered', function (StoreAnsweredCall $request) {
    Log::info($request);
    $lead = Lead::where('contact_number', $request['ani'])->first();
    $agent = Agent::where('extension', $request['agent'])->first();
    $skill = Skill::where('skillname', $request['queuename'])->first();

    // $socketUrl = env('http://localhost'); 
    
    $socketPort = env('SOCKET_SERVER_PORT', '3000');
    $fullSocketUrl = "http://127.0.0.1:{$socketPort}/emit";
    // $fullSocketUrl = "http://127.0.0.1:3000/emit";



    Cache::forever('agent-in-call-' . $agent->id, 1);
    Cache::forever('call-' . $request['unique_id'], $agent->id);

    Cache::add('current-call-count', 0, 99999999);
    if ($skill) {
        Cache::add($request['queuename'] . "-current-call-count", 0, 99999999);
    }

    Cache::increment('current-call-count');
    Cache::increment($request['queuename'] . "-current-call-count");

    


    if (!$lead) {
        if ($agent) {
            $lead = new Lead;
            $lead->contact_number = $request['ani'];
            $lead->unique_id = $request['unique_id'];
            $lead->agent_id = $agent->id;
            $lead->extension = $request['agent'];
            $lead->skill_id = $skill ? $skill->skillid : 0;
            $lead->status_id = 1;
            $lead->save();

            if(env('IS_PUSHER')== true)
            {
                event(new CallAnswered($lead->id));
            }
            else
            // using socket.io
            {
                // try {
                //     $client = new Client();
                //     $response = $client->post($fullSocketUrl, [
                //         'json' => [
                //             'event' => 'call.answered',
                //             'data' => [
                //                 'lead_id' => $lead->id
                //             ]
                //         ]
                //     ]);
    
                //     $responseData = json_decode($response->getBody(), true);
                //     Log::info('Socket event sent:', $responseData);

                // } catch (\Exception $e) {
                //     Log::error('Failed to send the socket event: ' . $e->getMessage());
                // }
                try {
                    $client = new Client();
                    $response = $client->post($fullSocketUrl, [
                        'json' => [
                            'event' => 'call.answered',
                            'data' => [
                                'lead_id' => $lead,
                            ]
                        ]
                    ]);

                    $responseData = json_decode($response->getBody(), true);
                    Log::info('Socket event sent:', $responseData);

                } catch (\Exception $e) {
                    Log::error('Failed to send the socket event: ' . $e->getMessage());
                }
            }
                
            return $lead;
        }
    } else {
        $agent = Agent::where('extension', $request['agent'])->first();
        $lead->agent_id = $agent->id;
        $lead->extension = $request['agent'];
        $lead->skill_id = $skill ? $skill->skillid : 0;
        $lead->save();

        if(env('IS_PUSHER')== true)
            {
                event(new CallAnswered($lead->id));
            }
            // using socket.io
            else
            {
                // try {
                //     $client = new Client();
                //     $response = $client->post($fullSocketUrl, [
                //         'json' => [
                //             'event' => 'call.answered',
                //             'data' => [
                //                 'lead_id' => $lead->id
                //             ]
                //         ]
                //     ]);
    
                //     $responseData = json_decode($response->getBody(), true);
                //     Log::info('Socket event sent:', $responseData);

                // } catch (\Exception $e) {
                //     Log::error('Failed to send the socket event: ' . $e->getMessage());
                // }

                try {
                $client = new Client();
                $response = $client->post($fullSocketUrl, [
                    'json' => [
                        'event' => 'call.answered',
                        'data' => [
                            'lead_id' => $lead
                        ]
                    ]
                ]);

                $responseData = json_decode($response->getBody(), true);
                Log::info('Socket event sent:', $responseData);

            } catch (\Exception $e) {
                Log::error('Failed to send the socket event: ' . $e->getMessage());
            }
            }

        return $lead;
    }
});

// Route::post('/call-dialed', function (StoreAnsweredCall $request) {
//     Log::info($request);
//     $lead = Lead::where('contact_number', $request['ani'])->first();
//     $agent = Agent::where('extension', $request['agent'])->first();
//     $skill = Skill::where('skillname', $request['queuename'])->first();
//     // Cache::forever('agent-in-call-' . $agent->id, 1);
//     Cache::forever('call-' . $request['unique_id'], $agent->id);

//     Cache::add('current-call-count', 0, 99999999);
//     if ($skill) {
//         Cache::add($request['queuename'] . "-current-call-count", 0, 99999999);
//     }

//     Cache::increment('current-call-count');
//     Cache::increment($request['queuename'] . "-current-call-count");


//     if (!$lead) {
//         if ($agent) {
//             $lead = new Lead;
//             $lead->contact_number = $request['ani'];
//             $lead->unique_id = $request['unique_id'];
//             $lead->agent_id = $agent->id;
//             $lead->extension = $request['agent'];
//             $lead->skill_id = $skill ? $skill->skillid : 0;
//             $lead->status_id = 1;
//             $lead->save();
//             event(new CallAnswered($lead->id));
//             return $lead;
//         }
//     } else {
//         $agent = Agent::where('extension', $request['agent'])->first();
//         $lead->agent_id = $agent->id;
//         $lead->extension = $request['agent'];
//         $lead->skill_id = $skill ? $skill->skillid : 0;
//         $lead->save();
//         event(new CallAnswered($lead->id));
//         return $lead;
//     }
// });


Route::post('/call-dialed', function (StoreAnsweredCall $request) {
    Log::info($request);
    $lead = Lead::where('contact_number', $request['ani'])->first();
    $agent = Agent::where('extension', $request['agent'])->first();
    $skill = Skill::where('skillname', $request['queuename'])->first();

    $socketPort = env('SOCKET_SERVER_PORT', '3000');
    $fullSocketUrl = "http://127.0.0.1:{$socketPort}/emit";

    Cache::forever('agent-in-call-' . $agent->id, 1);

    Cache::forever('call-' . $request['unique_id'], $agent->id);
    Cache::add('current-call-count', 0, 99999999);
    if ($skill) {
        Cache::add($request['queuename'] . "-current-call-count", 0, 99999999);
    }

    Cache::increment('current-call-count');
    Cache::increment($request['queuename'] . "-current-call-count");

    if (!$lead) {
        if ($agent) {
            $lead = new Lead;
            $lead->contact_number = $request['ani'];
            $lead->unique_id = $request['unique_id'];
            $lead->agent_id = $agent->id;
            $lead->extension = $request['agent'];
            $lead->skill_id = $skill ? $skill->skillid : 0;
            $lead->status_id = 1;
            $lead->save();

            if(env('IS_PUSHER')== true) {
                event(new CallAnswered($lead->id));
            } else {
                try {
                    $client = new Client();
                    $response = $client->post($fullSocketUrl, [
                        'json' => [
                            'event' => 'call.dialed',
                            'data' => [
                                'lead_id' => $lead
                            ]
                        ]
                    ]);
                    Log::info('Socket event sent:', json_decode($response->getBody(), true));
                } catch (\Exception $e) {
                    Log::error('Failed to send the socket event: ' . $e->getMessage());
                }
            }

            return $lead;
        }
    } else {
        $lead->agent_id = $agent->id;
        $lead->extension = $request['agent'];
        $lead->skill_id = $skill ? $skill->skillid : 0;
        $lead->save();

        if(env('IS_PUSHER')== true) {
            event(new CallAnswered($lead->id));
        } else {
            try {
                $client = new Client();
                $response = $client->post($fullSocketUrl, [
                    'json' => [
                        'event' => 'call.dialed',
                        'data' => [
                            'lead_id' => $lead
                        ]
                    ]
                ]);
                Log::info('Socket event sent:', json_decode($response->getBody(), true));
            } catch (\Exception $e) {
                Log::error('Failed to send the socket event: ' . $e->getMessage());
            }
        }

        return $lead;
    }
});



Route::post('/call-disconnected', function (Request $request) {
	Log::info('call-disconntected-line');    
	Log::info($request);
    if (Cache::has('call-' . $request['unique_id'])) {
        Cache::forget('agent-in-call-' . Cache::get('call-' . $request['unique_id']));   
        Cache::forget('call-' . $request['unique_id']);

        Cache::decrement('current-call-count');
        Cache::decrement($request['queuename'] . "-current-call-count");
    }
});

Route::post('/agent-disconnected', function (Request $request) {
	Log::info('agent-disconntected-line');    
	Log::info($request);
    if (Cache::has('call-' . $request['unique_id'])) {
        Cache::forget('agent-in-call-' . Cache::get('call-' . $request['unique_id']));   
        Cache::forget('call-' . $request['unique_id']);

        // Cache::decrement('current-call-count');
        Cache::decrement($request['queuename'] . "-current-call-count");
    }
});

// Route::post('/asterisk-events', [AsteriskEventController::class, 'handleEvent']);


Route::post('/logout-socket', function (\Illuminate\Http\Request $request) {
    $userId = $request->input('user_id');

    $currentSkills = Auth::user()->currentQueues()->active()->get()->pluck('skill')->unique();
        foreach ($currentSkills as $skill) { 

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
                'contents' => $skill
            ],
            [
                'name' => 'action',
                'contents' => 'remove'
            ],
            [
                'name' => 'agentid',
                'contents' => Auth::user()->agent_id
            ],
            [
                'name' => 'crm_token',
                'contents' => session()->getId() ? session()->getId() : null,
            ],
        ];
        ApiManager::updateSkill($data);
        } 

    if ($userId) {

        

        
        DB::table('sessions')
            ->where('user_id', $userId)
            ->delete();

        
    }
     

    
    \App\Models\AgentLogin::where('user_id', $userId)
        ->latest('login_time')
        ->first()
        ?->update(['logout_time' => now()]);

    Log::info("Socket logout for user {$userId}");

    return response()->noContent();
});