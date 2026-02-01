<?php

namespace App\Http\Livewire\Reports;

use Livewire\Component;

class DailyCallSummaryHourlyAnalytics extends Component
{
    public $date;

    public function mount($date)
    {
        $this->date = $date;
    }

    public function getHourlyDataProperty()
    {
        $date = $this->date;

        $inbound = \Illuminate\Support\Facades\DB::connection('mysql-old')
            ->table('callcount')
            ->where('direction', 'in')
            ->where('date', 'like', "$date%")
            ->selectRaw('HOUR(date) as hour, COUNT(*) as count')
            ->groupBy('hour')
            ->pluck('count', 'hour');

        $outbound = \Illuminate\Support\Facades\DB::connection('mysql-old')
            ->table('callcount')
            ->where('direction', 'out')
            ->where('date', 'like', "$date%")
            ->selectRaw('HOUR(date) as hour, COUNT(*) as count')
            ->groupBy('hour')
            ->pluck('count', 'hour');

        $queued = \Illuminate\Support\Facades\DB::connection('mysql-old')
            ->table('queuecount')
            ->where('status', '1')
            ->where('date', 'like', "$date%")
            ->selectRaw('HOUR(date) as hour, COUNT(*) as count')
            ->groupBy('hour')
            ->pluck('count', 'hour');

        $answered = \Illuminate\Support\Facades\DB::connection('mysql-old')
            ->table('queuecount')
            ->where('status', '2')
            ->where('date', 'like', "$date%")
            ->selectRaw('HOUR(date) as hour, COUNT(*) as count')
            ->groupBy('hour')
            ->pluck('count', 'hour');

        $data = [];
        for ($i = 0; $i < 24; $i++) {
            $in = $inbound->get($i, 0);
            $out = $outbound->get($i, 0);
            $q = $queued->get($i, 0);
            $ans = $answered->get($i, 0);
            $abd = $q - $ans;

            $data[] = [
                'hour' => sprintf('%02d:00', $i),
                'inbound' => $in,
                'outbound' => $out,
                'queued' => $q,
                'answered' => $ans,
                'abandoned' => $abd < 0 ? 0 : $abd,
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
                    'data' => array_column($hourlyData, 'queued'),
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
            ]
        ];
    }

    public function render()
    {
        return view('livewire.reports.daily-call-summary-hourly-analytics');
    }
}
