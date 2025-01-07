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
            event(new CallAnswered($lead->id));
            return $lead;
        }
    } else {
        $agent = Agent::where('extension', $request['agent'])->first();
        $lead->agent_id = $agent->id;
        $lead->extension = $request['agent'];
        $lead->skill_id = $skill ? $skill->skillid : 0;
        $lead->save();
        event(new CallAnswered($lead->id));
        return $lead;
    }
});

Route::post('/call-dialed', function (StoreAnsweredCall $request) {
    Log::info($request);
    $lead = Lead::where('contact_number', $request['ani'])->first();
    $agent = Agent::where('extension', $request['agent'])->first();
    $skill = Skill::where('skillname', $request['queuename'])->first();
    // Cache::forever('agent-in-call-' . $agent->id, 1);
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
            event(new CallAnswered($lead->id));
            return $lead;
        }
    } else {
        $agent = Agent::where('extension', $request['agent'])->first();
        $lead->agent_id = $agent->id;
        $lead->extension = $request['agent'];
        $lead->skill_id = $skill ? $skill->skillid : 0;
        $lead->save();
        event(new CallAnswered($lead->id));
        return $lead;
    }
});


Route::post('/call-disconnected', function (Request $request) {
	Log::info('call-disconntected-line');    
	Log::info($request);
    if (Cache::has('call-' . $request['unique_id'])) {
        // Cache::forget('agent-in-call-' . Cache::get('call-' . $request['unique_id']));   
        Cache::forget('call-' . $request['unique_id']);

        Cache::decrement('current-call-count');
        // Cache::decrement($request['queuename'] . "-current-call-count");
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
