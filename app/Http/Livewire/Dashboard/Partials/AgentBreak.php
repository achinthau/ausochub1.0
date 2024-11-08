<?php

namespace App\Http\Livewire\Dashboard\Partials;

use App\Models\AgentBreakSummary;
use App\Models\BreakType;
use App\Repositories\ApiManager;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class AgentBreak extends Component
{

    public $createUserBreakModal = false;
    public $breakTypes;
    public $description;

    public $breakType = 0;

    protected $listeners = ['showCreateUserBreakModal' => 'showCreateUserBreakModal', 'endBreak' => 'endBreak'];

    protected $rules = [
        'breakType' => 'required|exists:break_types,id',
        'description' => 'required_if:breakType,==,3',
    ];

    public function mount()
    {
        $this->breakTypes = BreakType::all();
    }


    public function render()
    {
        return view('livewire.dashboard.partials.agent-break');
    }

    public function showCreateUserBreakModal()
    {
        $this->createUserBreakModal = true;
    }

    public function save()
    {
        $this->validate();
        $user = Auth::user();

        $agentBreakSummary = new AgentBreakSummary;
        $agentBreakSummary->agentid = $user->agent_id;
        $agentBreakSummary->status = 1;
        $agentBreakSummary->breaktime = Carbon::now();
        $agentBreakSummary->date = Carbon::now()->format('Y-m-d');
        $breakType = $this->breakTypes->where('id', $this->breakType)->first();
        $agentBreakSummary->desc = $this->breakType == 3 ? "Other : " . $this->description : $breakType->title;
        $agentBreakSummary->save();
        
        $user->break_started_at = Carbon::now();
        $user->agent_break_id = $agentBreakSummary->id;
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

        ApiManager::startBreak($data);
        $this->createUserBreakModal = false;
        return redirect(route('dashboard.index'));
    }

    public function endBreak()
    {
        $user = Auth::user();
        $agentBreakSummary = AgentBreakSummary::find($user->agent_break_id);
        $agentBreakSummary->unbreaktime=Carbon::now();
        $agentBreakSummary->status = 0;
        $agentBreakSummary->save();
        
        $user->break_started_at = null;
        $user->agent_break_id = null;
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
                'contents' => $agentBreakSummary->desc
            ],
            [
                'name' => 'breakType',
                'contents' => 'type'
            ]
        ];

        ApiManager::startBreak($data);
        $this->createUserBreakModal = false;

        return redirect(route('dashboard.index'));
    }
}
