<?php

namespace App\Http\Livewire\Dashboard;

use App\Models\AgentBreakSummary;
use App\Models\Campaign;
use App\Models\CampaignMetric;
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

    protected $listeners = ['hideBreak' => 'hideBreak', 'showBreak' => 'showBreak', 'setOutbound' => 'setOutbound', 'setInbound' => 'setInbound'];

    public function mount()
    {
        $this->user = User::where('id', Auth::id())->with([
            'agent' => function ($q) {
                $q->miscallStatus();
            },
            'agent.todayQueues' => function ($q) {
                $q->answered()->today();
            }
        ])->first();

        $userId = Auth::user()->id;
        $this->boundType = Redis::get("user:{$userId}:bound_type");
        if (!$this->boundType) {
            $this->boundType = 'inbound';
            Redis::set("user:{$userId}:bound_type", $this->boundType);
        }


        if ($this->boundType == "dialer") {
            // $this->skills = Auth::user()->skills ? Auth::user()->skills->dialer_skill_ids : [];
            $skills = Auth::user()->skills ? Auth::user()->skills->dialer_skill_ids : [];

            $validCampaigns = Campaign::whereIn('status', [1])
                ->pluck('name')
                ->toArray();

            $this->skills = collect($skills)
                ->filter(fn($skillName) => in_array($skillName, $validCampaigns))
                ->toArray();
        } else {
            $this->skills = Auth::user()->skills ? Auth::user()->skills->skill_ids : [];
        }
        // $this->setBound();

        // $this->totalBreakTime = AgentBreakSummary::whereBetween('breaktime', [Carbon::now()->startOfDay(), Carbon::now()->endOfDay()])->where('agentid', Auth::user()->agent_id)->selectRaw('SEC_TO_TIME(SUM(TIMESTAMPDIFF(SECOND, breaktime, unbreaktime))) AS today_total_break')->first()->today_total_break;
        $currentSkills = Auth::user()->currentQueues()->active()->get();

        foreach ($currentSkills as $key => $value) {
            $this->selectedSkills[$value["skill"]] = $value["skill"];
        }

        $userId = Auth::id();

        $this->campaigns = CampaignMetric::with('types')
            ->whereRaw('FIND_IN_SET(?, assigned_users)', [$userId])
            ->get();


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

        Log::info('$isVisible:', [$this->isVisible]);







        $loggedUserId = Auth::id();
        $redisKey = "highlighted_users:$loggedUserId";

        Redis::select(5);


        $messagesCountIds = Redis::get($redisKey);
        $messagesCountIds = $messagesCountIds ? json_decode($messagesCountIds, true) : [];
        // $this->messagesCount = count($messagesCountIds) - 1 ;
        $this->messagesCount = count($messagesCountIds);



        return view('livewire.dashboard.index');
    }

    public function updatedSelectedSkills($value, $name)
    {

        // dd($value,$name);
        // dd(Auth::user()->currentQueues()->active()->get());
        // $currentSkills = Auth::user()->currentQueues()->active()->get();
        // $skills = Auth::user()->currentQueues()->active()->get()->pluck('skill')->unique();


        // dd($skills);

        //      $currentSkills = Auth::user()->currentQueues()->active()->get()->pluck('skill')->unique();
        // foreach ($currentSkills as $skill) { 
        //     dd($skill,$value,$name);
        // }

        $userId = Auth::user()->id;
        $boundType = Redis::get("user:{$userId}:bound_type");
        if ($boundType && $boundType == 'dialer') 
            {
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
                'contents' => $name
            ],
            [
                'name' => 'action',
                'contents' => $value ? 'add' : 'remove'
            ],
            [
                'name' => 'agentid',
                'contents' => Auth::user()->agent_id
            ],
            [
                'name' => 'crm_token',
                'contents' => $value ? session()->getId() : null
            ],
           
            [
                'name' => 'dialer',
                'contents' => '2'
            ],
        ];
        ApiManager::updateSkill($data);
        }
        else
        {

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
                'contents' => $name
            ],
            [
                'name' => 'action',
                'contents' => $value ? 'add' : 'remove'
            ],
            [
                'name' => 'agentid',
                'contents' => Auth::user()->agent_id
            ],
            [
                'name' => 'crm_token',
                'contents' => $value ? session()->getId() : null
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
    }


}
