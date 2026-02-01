<?php

namespace App\Http\Livewire\Reports;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DailyQueueSummaryHourly extends Component
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
            ->table('au_queuecount_report')
            ->leftJoin('au_callcount_report', 'au_queuecount_report.uniqueid', '=', 'au_callcount_report.uniqueid')
            ->where('au_queuecount_report.date', 'like', "$date%")
            ->where('queuename', $queue)
            ->selectRaw('HOUR(au_queuecount_report.date) as hour')
            ->selectRaw('SUM(CASE WHEN direction = \'in\' THEN 1 ELSE 0 END) as total_inbound')
            ->selectRaw('SUM(CASE WHEN direction = \'out\' THEN 1 ELSE 0 END) as total_outbound')
            ->selectRaw('SUM(CASE WHEN au_queuecount_report.status = 1 THEN 1 ELSE 0 END) as total_calls')
            ->selectRaw('SUM(CASE WHEN agent IS NOT NULL THEN 1 ELSE 0 END) as total_answered')
            ->selectRaw('COUNT(DISTINCT agent) as agent_count')
            ->groupBy('hour')
            ->get()
            ->keyBy('hour');

        $data = [];
        for ($i = 0; $i < 24; $i++) {
            $row = $results->get($i);
            $inbound = $row ? $row->total_inbound : 0;
            $outbound = $row ? $row->total_outbound : 0;
            $calls = $row ? $row->total_calls : 0;
            $answered = $row ? $row->total_answered : 0;
            $agents = $row ? $row->agent_count : 0;
            $abandoned = $calls - $answered;

            $data[] = [
                'hour' => sprintf('%02d:00 - %02d:00', $i, $i + 1),
                'inbound' => $inbound,
                'outbound' => $outbound,
                'calls' => $calls,
                'answered' => $answered,
                'abandoned' => $abandoned < 0 ? 0 : $abandoned,
                'agents' => $agents,
            ];
        }

        return $data;
    }

    public function render()
    {
        return view('livewire.reports.daily-queue-summary-hourly', [
            'hourlyData' => $this->hourlyData
        ]);
    }
}
