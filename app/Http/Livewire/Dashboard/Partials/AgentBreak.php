<?php

namespace App\Http\Livewire\Dashboard\Partials;

use App\Models\AgentBreakSummary;
use App\Models\AgentBreakSummaryReport;
use App\Models\BreakType;
use App\Repositories\ApiManager;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
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
        'description' => 'required_if:breakType,==,4',
    ];

    public function mount()
    {
        $this->breakTypes = BreakType::where('id', '!=', 3)->get();
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
        $agentBreakSummary->desc = $this->breakType == 4 ? "Other : " . $this->description : $breakType->title;
        $agentBreakSummary->save();

        $user->break_started_at = Carbon::now();
        $user->agent_break_id = $agentBreakSummary->id;
        $user->agent_break_type = $breakType->title;
        $user->save();

        // Remove all agent_on_call cache entries for this agent
        // Keys may be created by external systems with different prefixes (ausohub_singer:)
        // or by this app (laravel_database_), so we need to handle both
        
        $redis = Redis::connection()->client();
        
        // Temporarily disable Laravel's prefix to search all keys
        $currentPrefix = $redis->getOption(\Redis::OPT_PREFIX);
        $redis->setOption(\Redis::OPT_PREFIX, '');
        
        $redis->select(1); // Same database as used in api.php
        
        // Pattern to match (without any prefix)
        $pattern = "*agent_on_call-{$user->agent_id}-*";
        
        // Lua script to find and delete keys matching the pattern
        $luaScript = <<<'LUA'
            local keys = redis.call('keys', ARGV[1])
            local deleted = 0
            if #keys > 0 then
                deleted = redis.call('del', unpack(keys))
            end
            return deleted
LUA;
        
        try {
            // eval(script, args, num_keys) - pattern goes in args array
            $deletedCount = $redis->eval($luaScript, [$pattern], 0);
            \Log::info('Agent Break - Deleted Redis keys:', [
                'agent_id' => $user->agent_id,
                'pattern' => $pattern,
                'deleted_count' => $deletedCount
            ]);
        } catch (\Exception $e) {
            \Log::error('Agent Break - Failed to delete keys:', [
                'agent_id' => $user->agent_id,
                'error' => $e->getMessage()
            ]);
        } finally {
            // Restore Laravel's prefix
            $redis->setOption(\Redis::OPT_PREFIX, $currentPrefix);
        }


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
        Cache::put('setBreak', 'true');
        $this->createUserBreakModal = false;
        return redirect(route('dashboard.index'));
    }

    public function endBreak()
    {
        $user = Auth::user();
        $agentBreakSummary = AgentBreakSummary::find($user->agent_break_id);
        if ($agentBreakSummary) {
            $agentBreakSummary->unbreaktime = Carbon::now();
            $agentBreakSummary->status = 0;
            $agentBreakSummary->save();
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
                'contents' => $agentBreakSummary->desc
            ],
            [
                'name' => 'breakType',
                'contents' => 'type'
            ]
        ];

        ApiManager::startBreak($data);
        Cache::forget('setBreak');

        $this->createUserBreakModal = false;

        return redirect(route('dashboard.index'));
    }
}
