<?php

namespace App\Http\Livewire\Dashboard;

use App\Models\AgentBreakSummary;
use App\Models\Campaign;
use App\Models\CampaignMetric;
use App\Models\FeedContactValid;
use App\Models\FeedContactValidReport;
use App\Models\Skill;
use App\Models\User;
use App\Repositories\ApiManager;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Livewire\Component;

class Index extends Component
{
    public $user;
    public $skills;
    public $totalBreakTime;
    public $queueWiseData;
    public $selectedSkills = [];

    public $isVisible = true;
    public $messagesCount;
    public $boundType;
    public $dialerCallCounts;
    public $validCampaignTypes;
    public $hasStartedCampaign = false;
    public $dialedCallsToday = 0;
    public $answeredCallsToday = 0;

    protected $listeners = ['hideBreak' => 'hideBreak', 'showBreak' => 'showBreak', 'setOutbound' => 'setOutbound', 'setInbound' => 'setInbound'];

    public function mount()
    {
//         \Log::info('Mount started');
//         \Log::info('Loading user with agent relationships');

        $this->user = User::where('id', Auth::id())->with([
            'agent' => function ($q) {
                $q->miscallStatus();
            },
            'agent.todayQueues' => function ($q) {
                $q->answered()->today();
            }
        ])->first();

//         \Log::info('User loaded', ['user_id' => $this->user?->id]);

        $userId = Auth::user()->id;

//         \Log::info('Loading bound type from Redis', ['user_id' => $userId]);

        $this->boundType = Redis::get("user:{$userId}:bound_type");
        if (!$this->boundType) {
            $this->boundType = 'inbound';
            Redis::set("user:{$userId}:bound_type", $this->boundType);
        }

//         \Log::info('Bound type set', ['boundType' => $this->boundType]);


//         \Log::info('Loading skills based on boundType', ['boundType' => $this->boundType]);

        if ($this->boundType == "dialer") {
            // $this->skills = Auth::user()->skills ? Auth::user()->skills->dialer_skill_ids : [];
            $skills = Auth::user()->skills ? Auth::user()->skills->dialer_skill_ids : [];
            // dd($skills);
            // dd(Auth::user()->skills->dialer_skill_ids);

            $validCampaigns = Campaign::whereIn('status', [1])
                ->pluck('type','name')
                ->toArray();
            
                $this->validCampaignTypes = $validCampaigns;
            
//             \Log::info('Valid campaigns fetched', ['campaigns' => $validCampaigns]);

            $this->skills = collect($skills)
    ->filter(fn($skillName) => array_key_exists($skillName, $validCampaigns))
    ->toArray();


            // dd($this->skills);
            // dd($validCampaigns);

//             \Log::info('Filtered dialer skills', ['final_skills' => $this->skills]);
        } else {
            $this->skills = Auth::user()->skills ? Auth::user()->skills->skill_ids : [];

//             \Log::info('Inbound skills loaded', ['skills' => $this->skills]);
        }
        // $this->setBound();

        // $this->totalBreakTime = AgentBreakSummary::whereBetween('breaktime', [Carbon::now()->startOfDay(), Carbon::now()->endOfDay()])->where('agentid', Auth::user()->agent_id)->selectRaw('SEC_TO_TIME(SUM(TIMESTAMPDIFF(SECOND, breaktime, unbreaktime))) AS today_total_break')->first()->today_total_break;
        
//         \Log::info('Loading selected skills from current queues');

        $currentSkills = Auth::user()->currentQueues()->active()->get();

        foreach ($currentSkills as $key => $value) {
            $this->selectedSkills[$value["skill"]] = $value["skill"];
        }

//         \Log::info('Selected skills loaded', ['selectedSkills' => $this->selectedSkills]);

        $userId = Auth::id();

//         \Log::info('Loading campaigns assigned to user', ['user_id' => $userId]);

        $this->campaigns = CampaignMetric::with('types')
            ->whereRaw('FIND_IN_SET(?, assigned_users)', [$userId])
            ->get();

//         \Log::info('Campaigns loaded', ['campaigns_count' => $this->campaigns->count()]);
//         \Log::info('Mount completed');


    }

    public function hideBreak()
    {
        $this->isVisible = false;
        $this->dispatchBrowserEvent('updateButton', ['isVisible' => $this->isVisible]);

        // dd($this->isVisible);
    }

    public function showBreak()
    {
        $this->isVisible = true;
        $this->dispatchBrowserEvent('updateButton', ['isVisible' => $this->isVisible]);

        // dd($this->isVisible);
    }


    public function render()
    {

        $this->queueWiseData = DB::connection('mysql-old')->select("SELECT 
        a.queuename,
            SUM(IF(a.`status`=1,1,0)) as total_queue_count ,
            SUM(IF(a.`status`=0,1,0)) as total_disconnection_count ,
            SUM(IF(a.`status`=2,1,0)) as total_answered_count ,
            SUM(IF(a.`status`=0,1,0))-SUM(IF(a.`status`=2,1,0)) as abandoned_queue_count,
            SUM(IF(a.`status`=1,1,0))-SUM(IF(a.`status`=0,1,0)) as agent_conntected_count,
            SUM(IF(a.uniqueid NOT IN (SELECT DISTINCT  uniqueid  FROM queuecount aa WHERE aa.date > CURDATE() and aa.`status` IN (2,0)),1,0)) as queue_wating_count#
        FROM queuecount a 
        WHERE  a.date > CURDATE() 
        GROUP BY a.queuename;
        ");

        $this->user = User::where('id', Auth::id())->with([
            'agent' => function ($q) {
                $q->miscallStatus();
            },
            'agent.todayQueues' => function ($q) {
                $q->answered()->today();
            }
        ])->first();

        $this->totalBreakTime = AgentBreakSummary::whereBetween('breaktime', [Carbon::now()->startOfDay(), Carbon::now()->endOfDay()])->where('agentid', Auth::user()->agent_id)->selectRaw('SEC_TO_TIME(SUM(TIMESTAMPDIFF(SECOND, breaktime, unbreaktime))) AS today_total_break')->first()->today_total_break;

//         Log::info('$isVisible:', [$this->isVisible]);
        $extension = $this->user->extension;

        $this->dialerCallCounts = DB::connection('mysql-old')
            ->table('callcount')
            ->whereNotNull('app')
            ->where('callcount', $extension)
            ->count();

        $loggedUserId = Auth::id();
        $redisKey = "highlighted_users:$loggedUserId";

        Redis::select(5);


        $messagesCountIds = Redis::get($redisKey);
        $messagesCountIds = $messagesCountIds ? json_decode($messagesCountIds, true) : [];
        // $this->messagesCount = count($messagesCountIds) - 1 ;
        $this->messagesCount = count($messagesCountIds);

        $this->loadStartedCampaignStatistics();

        return view('livewire.dashboard.index');
    }

    public function loadStartedCampaignStatistics()
    {
        $this->hasStartedCampaign = false;
        $this->dialedCallsToday = 0;
        $this->answeredCallsToday = 0;

        if ($this->boundType != 'dialer') {
            return;
        }

        $startedCampaignNames = collect($this->selectedSkills)
            ->filter(fn($value) => !empty($value))
            ->values()
            ->toArray();

        if (empty($startedCampaignNames)) {
            return;
        }

        $startedCampaigns = Campaign::whereIn('name', $startedCampaignNames)
            ->where('status', 1)
            ->get();

        if ($startedCampaigns->isEmpty()) {
            return;
        }

        $feedIds = $startedCampaigns->flatMap->feed_ids->unique()->values()->toArray();

        if (empty($feedIds)) {
            return;
        }

        $this->hasStartedCampaign = true;

        $userId = Auth::id();

        $this->dialedCallsToday = FeedContactValid::whereIn('feed_id', $feedIds)
            ->where('updated_by', $userId)
            ->whereNotNull('status')
            ->whereDate('attempted_at', today())
            ->count();

        $this->answeredCallsToday = FeedContactValidReport::whereIn('feed_id', $feedIds)
            ->where('updated_by', $userId)
            ->whereIn('status', [1, 41])
            ->whereDate('attempted_at', today())
            ->count();

        $this->dialedCallsToday = $this->dialedCallsToday + $this->answeredCallsToday ;
    }

    public function updatedSelectedSkills($type, $value)
    {
        // $user = Auth::user()->load([
        //     'agent',
        //     'agent.extensionDetails',
        //     'currentQueues'
        // ]);

        // $currentSkills = $user->currentQueues()->active()->pluck('skill')->unique();

        // dd($value,$name);
        // dd($currentSkills);
        // dd($value,$type);


        $userId = Auth::user()->id;
        // $boundType = Redis::get("user:{$userId}:bound_type");
        $boundType = $this->boundType;
        if ($boundType && $boundType == 'dialer') {
            $user = Auth::user()->load([
                'agent',
                'agent.extensionDetails',
                'currentQueues'
            ]);
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

            $this->emitTo('dashboard.partials.dialer.call-panel', 'updateSkill');
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
                    'contents' => $value
                ],
                [
                    'name' => 'action',
                    'contents' => $type ? 'add' : 'remove'
                ],
                [
                    'name' => 'agentid',
                    'contents' => Auth::user()->agent_id
                ],
                [
                    'name' => 'crm_token',
                    'contents' => $type ? session()->getId() : null
                ],

                [
                    'name' => 'dialer',
                    'contents' => '2'
                ],
            ];
            ApiManager::updateSkill($data);
        } else {

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
                    'contents' => $value
                ],
                [
                    'name' => 'action',
                    'contents' => $type ? 'add' : 'remove'
                ],
                [
                    'name' => 'agentid',
                    'contents' => Auth::user()->agent_id
                ],
                [
                    'name' => 'crm_token',
                    'contents' => $type ? session()->getId() : null
                ],
                [
                    'name' => 'dialer',
                    'contents' => '0'
                ],
            ];
            ApiManager::updateSkill($data);
        }

        return redirect(route('dashboard.index'));
    }

    public function setOutbound()
    {
        $this->boundType = 'dialer';
        $userId = Auth::user()->id;
        Redis::set("user:{$userId}:bound_type", $this->boundType);
        $this->setBound();

        // $user = Auth::user()->load([
        //     'agent',
        //     'agent.extensionDetails',
        //     'currentQueues'
        // ]);

        // $currentSkills = $user->currentQueues()->active()->pluck('skill')->unique();

        // $this->skills = Auth::user()->skills ? Auth::user()->skills->dialer_skill_ids : [];

        // foreach ($currentSkills as $skill) {
        //     $data = [
        //         ['name' => 'extension', 'contents' => optional($user->agent)->extension],
        //         ['name' => 'type', 'contents' => optional(optional($user->agent)->extensionDetails)->exten_type],
        //         ['name' => 'agentip', 'contents' => '123.231.121.61'],
        //         ['name' => 'queue', 'contents' => $skill],
        //         ['name' => 'action', 'contents' => 'remove'],
        //         ['name' => 'agentid', 'contents' => $user->agent_id],
        //         ['name' => 'crm_token', 'contents' => null],
        //     ];

        //     ApiManager::updateSkill($data);
        // }
        // return redirect()->route('dialer.admin.dashboard');
        // dd('gh');
    }

    public function setInbound()
    {
        $this->boundType = 'inbound';
        $userId = Auth::user()->id;
        Redis::set("user:{$userId}:bound_type", $this->boundType);
        $this->setBound();
    }

    public function setBound()
    {
        $user = Auth::user()->load([
            'agent',
            'agent.extensionDetails',
            'currentQueues'
        ]);

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

        if ($this->boundType == "dialer") {
            $skills = Auth::user()->skills ? Auth::user()->skills->dialer_skill_ids : [];

            $validCampaigns = Campaign::whereIn('status', [1])
                ->pluck('name')
                ->toArray();

            $this->skills = collect($skills)
                ->filter(fn($skillName) => in_array($skillName, $validCampaigns))
                ->toArray();
            // dd($validCampaigns);
            // dd($skills);
        } else {
            $this->skills = Auth::user()->skills ? Auth::user()->skills->skill_ids : [];
        }

        $this->selectedSkills = [];
    }


}
