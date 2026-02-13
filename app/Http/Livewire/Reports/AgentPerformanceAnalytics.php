<?php

namespace App\Http\Livewire\Reports;

use App\Models\Agent;
use App\Models\QueueEvent;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class AgentPerformanceAnalytics extends Component
{
    public $startDate;
    public $endDate;
    public $selectedExtension = '';

    public function mount()
    {
        $this->startDate = Carbon::now()->subDays(7)->format('Y-m-d');
        $this->endDate = Carbon::now()->format('Y-m-d');
    }

    public function getExtensionsProperty()
    {
        return Agent::whereNotNull('extension')
            ->orderBy('extension')
            ->pluck('extension', 'extension');
    }

    public function updated($name)
    {
        $this->emit('refreshCharts', $this->donutData);
    }

    public function getDonutDataProperty()
    {
        $query = QueueEvent::where('event_name', 'AgentComplete')
            ->whereBetween('created_at', [
                Carbon::parse($this->startDate)->startOfDay(),
                Carbon::parse($this->endDate)->endOfDay()
            ]);

        if ($this->selectedExtension) {
            $query->where('extension', $this->selectedExtension);
        }

        $events = $query->get();

        // 1. Talk Time (Minutes): 0-2, 2-5, 5+
        $talk0_2 = 0;
        $talk2_5 = 0;
        $talk5Plus = 0;

        // 2. Ring Time (Seconds): 0-5, 5-10, 10-15 (and 15+)
        $ring0_5 = 0;
        $ring5_10 = 0;
        $ring10_15 = 0;
        $ring15Plus = 0;

        // 3. Hold Time (Seconds): 0-30, 30-120, 120+
        $hold0_30 = 0;
        $hold30_120 = 0;
        $hold120Plus = 0;

        // 4. Disconnected By: Agent, Caller
        $discAgent = 0;
        $discCaller = 0;

        foreach ($events as $event) {
            // Talk Time (incoming is in seconds based on tinker output)
            $talkMin = $event->talk_time / 60;
            if ($talkMin <= 2) $talk0_2++;
            elseif ($talkMin <= 5) $talk2_5++;
            else $talk5Plus++;

            // Ring Time
            if ($event->ring_time <= 5) $ring0_5++;
            elseif ($event->ring_time <= 10) $ring5_10++;
            elseif ($event->ring_time <= 15) $ring10_15++;
            else $ring15Plus++;

            // Hold Time
            if ($event->hold_time <= 30) $hold0_30++;
            elseif ($event->hold_time <= 120) $hold30_120++;
            else $hold120Plus++;

            // Disconnected By
            if ($event->disconnected_by === 'agent') $discAgent++;
            elseif ($event->disconnected_by === 'caller') $discCaller++;
        }

        return [
            'talkTime' => [$talk0_2, $talk2_5, $talk5Plus],
            'ringTime' => [$ring0_5, $ring5_10, $ring10_15, $ring15Plus],
            'holdTime' => [$hold0_30, $hold30_120, $hold120Plus],
            'disconnectedBy' => [$discAgent, $discCaller],
        ];
    }

    public function render()
    {
        return view('livewire.reports.agent-performance-analytics');
    }
}
