<?php

use App\Events\CallAnswered;
use App\Http\Requests\StoreAnsweredCall;
use App\Models\Agent;
use App\Models\FeedContactValid;
use App\Models\ItemMaster;
use App\Models\Lead;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AsteriskEventController;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use App\Repositories\ApiManager;
use Illuminate\Support\Facades\Auth;



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
            fn(Builder $query) => $query
                ->where('descr', 'like', "%{$request->search}%")
                ->orWhere('barcode', 'like', "%{$request->search}%")
        )
        ->when(
            $request->exists('selected'),
            fn(Builder $query) => $query->whereIn('id', $request->selected),
            fn(Builder $query) => $query->limit(10)
        )
        ->get();
})->name('api.items.index');

Route::post('/call-answered', function (StoreAnsweredCall $request) {
    Log::info($request);
    // $lead = Lead::where('contact_number', $request['ani'])->first();
    $number = $request['ani'];
    // if (!empty($number) && strlen($number) === 9) {
    //     $number = '0' . $number;
    // }

    // $withZero    = str_starts_with($number, '0') ? $number : '0'.$number;
    // $withoutZero = ltrim($number, '0');
    $lead = Lead::where('contact_number', $number)
        // ->orWhere('contact_number', $withZero)
        ->first();
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

        // Cache::forever('agent_on_call-' . $agent->id . '-' . $request['queuename'], 1);

        $redis = Redis::connection()->client();
        $redis->select(1);
        $redis->set('agent_on_call-' . $agent->id . '-' . $request['queuename'] .'-'. $request['unique_id'],1);

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

            if (env('IS_PUSHER') == true) {
                event(new CallAnswered($lead->id));
            } else
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

        if (env('IS_PUSHER') == true) {
            event(new CallAnswered($lead->id));
        }
        // using socket.io
        else {
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
    // $lead = Lead::where('contact_number', $request['ani'])->first();
    $number = $request['ani'];
    // if (!empty($number) && strlen($number) === 9) {
    //     $number = '0' . $number;
    // }
    $lead = Lead::where('contact_number', $number)
        ->first();
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
    if ($request['type'] != 22) {
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

                if (env('IS_PUSHER') == true) {
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

            if (env('IS_PUSHER') == true) {
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

    $redis = Redis::connection()->client();
    $redis->select(1);
    $uniqueId = $request['unique_id'];

    // $keys = $redis->keys("agent_on_call-*{$uniqueId}*");
    $keys = $redis->keys("*agent_on_call-*{$uniqueId}");

if (!empty($keys)) {
    foreach ($keys as $key) {
        // Optional: log the key before deleting
        $redis->rpush('all_agent_on_call_keys', $key);

        // Delete the key
        $redis->del($key);
    }

    // Optionally mark that deletion happened
    $redis->set('key_deletion_status', 'true');
} else {
    // Mark that no keys were found
    $redis->set('key_deletion_status', 'false');
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

    if ($userId) {
        $user = User::with(['agent', 'agent.extensionDetails', 'currentQueues'])->find($userId);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $currentSkills = $user->currentQueues()->active()->pluck('skill')->unique();

        foreach ($currentSkills as $skill) {
            $data = [
                ['name' => 'extension', 'contents' => optional($user->agent)->extension],
                ['name' => 'type', 'contents' => optional(optional($user->agent)->extensionDetails)->exten_type],
                ['name' => 'agentip', 'contents' => '123.231.121.61'],
                ['name' => 'queue', 'contents' => $skill],
                ['name' => 'action', 'contents' => 'remove'],
                ['name' => 'agentid', 'contents' => $user->agent_id],
                ['name' => 'crm_token', 'contents' => null],
            ];

            ApiManager::updateSkill($data);
        }

        DB::table('sessions')
            ->where('user_id', $userId)
            ->delete();

        \App\Models\AgentLogin::where('user_id', $userId)
            ->latest('login_time')
            ->first()
                ?->update(['logout_time' => now()]);

        Log::info("Socket logout for user {$userId}");

        return response()->noContent();
    }

    return response()->json(['error' => 'User ID required'], 400);
});


Route::post('/get-number', function (Request $request) {
    $validator = Validator::make($request->all(), [
        'phone_number' => 'required',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => 'Validation error',
            'errors' => $validator->errors(),
        ], 422); // HTTP 422 Unprocessable Entity
    } else {
        $phoneNumber = $request->input('phone_number');
        $lead = Lead::where('contact_number', $phoneNumber)->first();

        if (!$lead) {
            $feedContact = FeedContactValid::where('phone', $phoneNumber)->first();

            if ($feedContact) {
                $data = json_decode($feedContact->data, true);

                $contact_number = $data['contact_number'] ?? null;
                $first_name = $data['first_name'] ?? null;
                $last_name = $data['last_name'] ?? null;
                $nic = $data['nic'] ?? null;
                $address_line_1 = $data['address_line_1'] ?? null;
                $address_line_2 = $data['address_line_2'] ?? null;
                $city = $data['city'] ?? null;
                $contact_number_2 = $data['contact_number_2'] ?? null;
                $email = $data['email'] ?? null;


                $lead = new Lead();
                $lead->contact_number = $contact_number;
                $lead->first_name = $first_name;
                $lead->last_name = $last_name;
                $lead->nic = $nic;
                $lead->address_line_1 = $address_line_1;
                $lead->address_line_2 = $address_line_2;
                $lead->city = $city;
                $lead->contact_number_2 = $contact_number_2;
                $lead->email = $email;

                if ($first_name) {
                    $lead->status_id = 2;
                } else {
                    $lead->status_id = 1;
                }
                $lead->save();

            }
        }

    }

    $phoneNumber = $request->input('phone_number');

    return response()->json([
        'success' => true,
        'message' => 'Phone number received successfully.',
        'data' => [
            'phone_number' => $phoneNumber,
        ],
    ], 200); // HTTP 200 OK
});

Route::post('/get-answered-number', function (Request $request) {
    $validator = Validator::make($request->all(), [
        'phone_number' => 'required',
        'extention' => 'required',
        'skill' => 'skill',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => 'Validation error',
            'errors' => $validator->errors(),
        ], 422); // HTTP 422 Unprocessable Entity
    } else {
        $socketPort = env('SOCKET_SERVER_PORT', '3000');
        $fullSocketUrl = "http://127.0.0.1:{$socketPort}/emit";
        $phoneNumber = $request->input('phone_number');
        $extention = $request->input('extention');
        $skill = $request->input('queuename');
        $lead = Lead::where('contact_number', $phoneNumber)->first();
        $agent = Agent::where('extension', $extention)->first();
        $skill = Skill::where('skillname', $skill)->first();



        if (!$lead) {
            if ($agent) {
                $lead = new Lead;
                $lead->contact_number = $phoneNumber;
                $lead->unique_id = $request['unique_id'];
                $lead->agent_id = $agent->id;
                $lead->extension = $request->input('extention');
                $lead->skill_id = $skill ? $skill->skillid : 0;
                $lead->status_id = 1;
                $lead->save();

                if (env('IS_PUSHER') == true) {
                    event(new CallAnswered($lead->id));
                } else {
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
                        Log::info('Socket event sent:', json_decode($response->getBody(), true));
                    } catch (\Exception $e) {
                        Log::error('Failed to send the socket event: ' . $e->getMessage());
                    }
                }

                return $lead;
            }
        } else {
            $lead->agent_id = $agent->id;
            $lead->extension = $request->input('extention');
            $lead->skill_id = $skill ? $skill->skillid : 0;
            $lead->save();

            if (env('IS_PUSHER') == true) {
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
    }

    $phoneNumber = $request->input('phone_number');
    $exten = $request->input('extention');

    return response()->json([
        'success' => true,
        'message' => 'Phone number received successfully.',
        'data' => [
            'phone_number' => $phoneNumber,
            'extention' => $exten,
        ],
    ], 200); // HTTP 200 OK
});


