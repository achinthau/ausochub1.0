<?php

namespace App\Http\Livewire\Dashboard;

use App\Models\AgentBreakSummary;
use App\Models\AgentBreakSummaryReport;
use App\Models\BreakType;
use App\Repositories\ApiManager;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class SelectBound extends Component
{
    public $boundType = "In Bound";
    public $isOutbound = false;
    public $isAcw = false;
    public $time = 0;
    public $pauseTime = false;
    public $timeout;

    public $breakTypes;
    public $breakType = 3;
    public $description;

    public $isVisible;

    protected $listeners = ['changeBound' => 'changeTheBound', 'setAcw' => 'setAcw', 'updateTime' => 'updateTime'];

    protected $rules = [
        'breakType' => 'required|exists:break_types,id',
        'description' => 'required_if:breakType,==,4',
    ];

    public function mount()
    {
        $this->timeout = env('ACW_TIMEOUT', 20);

        $this->breakTypes = BreakType::all();

        if (Cache::get('setBreak') === 'true') {
            $this->isVisible = false;
        } else {
            $this->isVisible = true;
        }

        $userId = auth()->id();
        $cachedState = Cache::get("acw_state_{$userId}");

        if ($cachedState) {
            $this->pauseTime = $cachedState['pauseTime'];
            $this->time = $cachedState['time'];
            $this->isAcw = $cachedState['isAcw'];
        }
    }


    public function refreshComponent()
    {
        if ($this->isOutbound == true) {
            $this->boundType = "Out Bound";
        } else {
            $this->boundType = "In Bound";
        }

        // $cacheKey = 'bound_type';
        // Cache::put($cacheKey, $this->boundType, now()->addMinutes(10));
    }

    public function setAcw()
    {
        if ($this->timeout > 0) {

            if ($this->isAcw == false && $this->time == 0 && $this->pauseTime == false) {
                $this->isAcw = true;
                $this->startAcw();
            } elseif ($this->isAcw == true && $this->time > 0 && $this->time < ($this->timeout + 1) && $this->pauseTime == false) {
                $this->pauseTime = true;
            } elseif ($this->isAcw == true && $this->pauseTime == true) {
                $this->pauseTime = false;
                $this->time = 0;
                $this->isAcw = false;
                $this->endAcw();
                // return redirect()->to(url()->previous());
            }
        } elseif ($this->timeout == 0) {

            if ($this->isAcw == false && $this->time == 0 && $this->pauseTime == false) {
                $this->isAcw = true;
                $this->pauseTime = true;
                $this->startAcw();
            } elseif ($this->isAcw == true && $this->time > 0 && $this->pauseTime == true) {
                $this->pauseTime = false;
                $this->time = 0;
                $this->isAcw = false;
                $this->endAcw();
                // return redirect()->to(url()->previous());
            }
        }
    }

    public function startAcw()
    {
        $this->emitTo('dashboard.index','hideBreak');
        // $this->validate();
        $user = Auth::user();

        $agentBreakSummary = new AgentBreakSummary;
        $agentBreakSummary->agentid = $user->agent_id;
        $agentBreakSummary->status = 1;
        $agentBreakSummary->breaktime = Carbon::now();
        $agentBreakSummary->date = Carbon::now()->format('Y-m-d');
        $breakType = $this->breakTypes->where('id', $this->breakType)->first();
        $agentBreakSummary->desc = $this->breakType == 4 ? "Other : " . $this->description : $breakType->title;
        $agentBreakSummary->save();

        $user->break_started_at = Carbon::now();
        $user->agent_break_id = $agentBreakSummary->id;
        $user->agent_break_type = $breakType->title;
        $user->save();

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
                'name' => 'action',
                'contents' => 'break'
            ],
            [
                'name' => 'agentid',
                'contents' => Auth::user()->agent_id
            ],
            [
                'name' => 'comment',
                'contents' => $agentBreakSummary->desc
            ],
            [
                'name' => 'breakType',
                'contents' => $breakType->title
            ]
        ];

        $userId = auth()->id();
        Cache::put("acw_state_{$userId}", [
            'pauseTime' => $this->pauseTime,
            'time' => $this->time,
            'isAcw' => $this->isAcw,
        ]);

        
        ApiManager::startBreak($data);
    }

    public function endAcw()
    {
        $user = Auth::user();

        // if ($user->agent_break_id) {
        $agentBreakSummary = AgentBreakSummary::find($user->agent_break_id);
        if ($agentBreakSummary) {
            $agentBreakSummary->unbreaktime = Carbon::now();
            $agentBreakSummary->status = 0;
            $agentBreakSummary->save();
            // }
        }
        else
        {
            $agentBreakSummary = AgentBreakSummaryReport::find($user->agent_break_id);
            if ($agentBreakSummary) {
                $agentBreakSummary->unbreaktime = Carbon::now();
                $agentBreakSummary->status = 0;
                $agentBreakSummary->save();
            }
        }


        $user->break_started_at = null;
        $user->agent_break_id = null;
        $user->agent_break_type = null;
        $user->save();


        $breakType = $this->breakTypes->where('id', $this->breakType)->first();
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
                'name' => 'action',
                'contents' => 'unbreak'
            ],
            [
                'name' => 'agentid',
                'contents' => Auth::user()->agent_id
            ],
            [
                'name' => 'comment',
                'contents' => $agentBreakSummary ? $agentBreakSummary->desc  : 'Invalid Rec'
            ],
            [
                'name' => 'breakType',
                'contents' => 'ACW'
            ]
        ];

        $userId = auth()->id();
        Cache::forget("acw_state_{$userId}");

        $this->emit('showBreak');
        ApiManager::startBreak($data);
    }

    public function updateTime()
    {
        if ($this->timeout > 0) {

            if ($this->isAcw) {
                $this->time++;
            }
            if ($this->time == $this->timeout && $this->isAcw == true && $this->pauseTime == false) {
                $this->pauseTime = false;
                $this->time = 0;
                $this->isAcw = false;
                $this->endAcw();
                // return redirect()->to(url()->previous());
            }
        } elseif ($this->timeout == 0) {
            if ($this->isAcw) {
                $this->time++;
            }
        }
        $userId = auth()->id();
        Cache::put("acw_state_{$userId}", [
            'pauseTime' => $this->pauseTime,
            'time' => $this->time,
            'isAcw' => $this->isAcw,
        ]);
    }

    public function setVisibility()
    {
        $this->isVisible = true;
    }

    public function setInvisibility()
    {
        $this->isVisible = false;
    }


    public function render()
    {
        return view('livewire.dashboard.select-bound');
    }
}
