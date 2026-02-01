<?php

namespace App\Http\Livewire\Reports;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DailyQueueSummaryHourlyAnalytics extends Component
{
    public $date;
    public $queue;

    public function mount($date, $queue)
    {
        $this->date = $date;
        $this->queue = $queue;
    }

    public function getHourlyDataProperty()
    {
        $date = $this->date;
        $queue = $this->queue;

        $results = DB::connection('mysql-old')
            ->table('queuecount')
            ->where('date', 'like', "$date%")
            ->where('queuename', $queue)
            ->selectRaw('HOUR(date) as hour')
            ->selectRaw('SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) as total_calls')
            ->selectRaw('SUM(CASE WHEN agent IS NOT NULL THEN 1 ELSE 0 END) as total_answered')
            ->selectRaw('COUNT(DISTINCT agent) as agent_count')
            ->groupBy('hour')
            ->get()
            ->keyBy('hour');

        $data = [];
        for ($i = 0; $i < 24; $i++) {
            $row = $results->get($i);
            $calls = $row ? $row->total_calls : 0;
            $answered = $row ? $row->total_answered : 0;
            $agents = $row ? $row->agent_count : 0;
            $abandoned = $calls - $answered;

            $data[] = [
                'hour' => sprintf('%02d:00', $i),
                'calls' => $calls,
                'answered' => $answered,
                'abandoned' => $abandoned < 0 ? 0 : $abandoned,
                'agents' => $agents,
            ];
        }

        return $data;
    }

    public function getChartDataProperty()
    {
        $hourlyData = $this->hourlyData;
        
        return [
            'labels' => array_column($hourlyData, 'hour'),
            'datasets' => [
                [
                    'label' => 'Inbound',
                    'data' => array_column($hourlyData, 'inbound'),
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Outbound',
                    'data' => array_column($hourlyData, 'outbound'),
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Queued',
                    'data' => array_column($hourlyData, 'calls'),
                    'borderColor' => '#f59e0b',
                    'backgroundColor' => 'rgba(245, 158, 11, 0.1)',
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Answered',
                    'data' => array_column($hourlyData, 'answered'),
                    'borderColor' => '#8b5cf6',
                    'backgroundColor' => 'rgba(139, 92, 246, 0.1)',
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Abandoned',
                    'data' => array_column($hourlyData, 'abandoned'),
                    'borderColor' => '#ef4444',
                    'backgroundColor' => 'rgba(239, 68, 68, 0.1)',
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Agents',
                    'data' => array_column($hourlyData, 'agents'),
                    'borderColor' => '#6b7280',
                    'backgroundColor' => 'rgba(107, 114, 128, 0.1)',
                    'tension' => 0.4,
                ],
            ]
        ];
    }

    public function render()
    {
        return view('livewire.reports.daily-queue-summary-hourly-analytics');
    }
}
