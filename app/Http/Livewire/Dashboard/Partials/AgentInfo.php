<?php

namespace App\Http\Livewire\Dashboard\Partials;

use App\Models\AgentBreakSummary;
use App\Models\Cdr;
use App\Models\QueueCount;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class AgentInfo extends Component
{
    public $userInfoModal = false;
    public $skills;
    public $totalBreakTime;
    public $user;
    public $queueWiseData;
    public $missed;
    public $mergedData;
    public $dialerQueueWiseData;

    protected $listeners = ['showAgentInfoModal' => 'showAgentInfoModal'];




    public function showAgentInfoModal($user)
    {
        $this->userInfoModal = true;

        // If $user is an integer, retrieve the User model
        $this->user = is_numeric($user) ? User::find($user) : $user;

        // dd($this->user->name);

        if (!$this->user) {
            // Handle case where user is not found
            $this->skills = [];
            $this->totalBreakTime = null;
            return;
        }


        $this->skills = $this->user->skills ? $this->user->skills->skills : [];

        $this->skills = str_replace(",", " |   ", $this->skills);
        // dd($this->skills);


        $this->totalBreakTime = AgentBreakSummary::whereBetween('breaktime', [Carbon::now()->startOfDay(), Carbon::now()->endOfDay()])
            ->where('agentid', $this->user->agent_id)
            ->selectRaw('SEC_TO_TIME(SUM(TIMESTAMPDIFF(SECOND, breaktime, unbreaktime))) AS today_total_break')
            ->first()
            ->today_total_break;

        $extension = $this->user->extension;

        $this->queueWiseData = QueueCount::select('queuename', DB::raw('COUNT(*) as call_count'))
            ->where('agent', $extension)
            ->groupBy('queuename')
            ->get()
            ->keyBy('queuename');  

        
        $this->missed = Cdr::select('queuecount.queuename', DB::raw('COUNT(*) as missed_count'))
            ->join('queuecount', 'cdr.uniqueid', '=', 'queuecount.uniqueid')
            ->whereIn('cdr.disposition', ['NO ANSWER', 'BUSY'])
            ->whereRaw("SUBSTRING_INDEX(SUBSTRING_INDEX(cdr.channel, '/', -1), '-', 1) = ?", [$extension])
            ->groupBy('queuecount.queuename')
            ->get()
            ->keyBy('queuename');

        $this->mergedData = $this->queueWiseData->map(function ($queue) {
            $missedCount = $this->missed->get($queue->queuename) ? $this->missed->get($queue->queuename)->missed_count : 0;
            $queue->missed_count = $missedCount; 
            return $queue;
        });



        // dd($this->queueWiseData);

        $this->dialerQueueWiseData = DB::connection('mysql-old')
            ->table('callcount')
            ->whereNotNull('app')
            ->where('callcount', $extension)
            ->select(
                'app as queuename',
                DB::raw('COUNT(*) as total_queue_count'),
                DB::raw("
            SUM(CASE WHEN status = 2 THEN 1 ELSE 0 END) as answered_count,
            SUM(CASE WHEN status = 3 THEN 1 ELSE 0 END) as busy_count,
            SUM(CASE WHEN status = 4 THEN 1 ELSE 0 END) as no_answer_count,
            SUM(CASE WHEN status = 5 THEN 1 ELSE 0 END) as unreachable_count,
            SUM(CASE WHEN status = 6 THEN 1 ELSE 0 END) as cancel_count
        ")
            )
            ->groupBy('app')
            ->get();
    }


    public function render()
    {


        return view('livewire.dashboard.partials.agent-info');
    }
}
