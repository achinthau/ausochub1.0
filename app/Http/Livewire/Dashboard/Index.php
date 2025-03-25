<?php

namespace App\Http\Livewire\Dashboard;

use App\Models\AgentBreakSummary;
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

    public $isVisible =true;
    public $messagesCount ;

    protected $listeners = ['hideBreak' => 'hideBreak', 'showBreak' => 'showBreak'];

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



        $this->skills = Auth::user()->skills ?  Auth::user()->skills->skill_ids : [];
        // $this->totalBreakTime = AgentBreakSummary::whereBetween('breaktime', [Carbon::now()->startOfDay(), Carbon::now()->endOfDay()])->where('agentid', Auth::user()->agent_id)->selectRaw('SEC_TO_TIME(SUM(TIMESTAMPDIFF(SECOND, breaktime, unbreaktime))) AS today_total_break')->first()->today_total_break;
        $currentSkills = Auth::user()->currentQueues()->active()->get();

        foreach ($currentSkills as $key => $value) {
            $this->selectedSkills[$value["skill"]] = $value["skill"];
        }


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
    $this->messagesCount = count($messagesCountIds) - 1 ;



        return view('livewire.dashboard.index');
    }

    public function updatedSelectedSkills($value, $name)
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
        ];
        ApiManager::updateSkill($data);

        return redirect(route('dashboard.index'));
    }

    
}
