<?php

namespace App\Filament\Widgets;

use Filament\Widgets\LineChartWidget;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DailyCallSummaryHourlyChart extends LineChartWidget
{
    protected static ?string $heading = 'Hourly Call Trends';
    protected static ?string $maxHeight = '300px';

    public $date;

    protected function getData(): array
    {
        $date = $this->date;

        $inbound = DB::connection('mysql-old')
            ->table('callcount')
            ->where('direction', 'in')
            ->where('date', 'like', "$date%")
            ->selectRaw('HOUR(date) as hour, COUNT(*) as count')
            ->groupBy('hour')
            ->pluck('count', 'hour');

        $outbound = DB::connection('mysql-old')
            ->table('callcount')
            ->where('direction', 'out')
            ->where('date', 'like', "$date%")
            ->selectRaw('HOUR(date) as hour, COUNT(*) as count')
            ->groupBy('hour')
            ->pluck('count', 'hour');

        $queued = DB::connection('mysql-old')
            ->table('queuecount')
            ->where('status', '1')
            ->where('date', 'like', "$date%")
            ->selectRaw('HOUR(date) as hour, COUNT(*) as count')
            ->groupBy('hour')
            ->pluck('count', 'hour');

        $answered = DB::connection('mysql-old')
            ->table('queuecount')
            ->where('status', '2')
            ->where('date', 'like', "$date%")
            ->selectRaw('HOUR(date) as hour, COUNT(*) as count')
            ->groupBy('hour')
            ->pluck('count', 'hour');

        $inboundData = [];
        $outboundData = [];
        $queuedData = [];
        $answeredData = [];
        $abandonedData = [];
        $labels = [];

        for ($i = 0; $i < 24; $i++) {
            $in = $inbound->get($i, 0);
            $out = $outbound->get($i, 0);
            $q = $queued->get($i, 0);
            $ans = $answered->get($i, 0);
            $abd = $q - $ans;

            $inboundData[] = $in;
            $outboundData[] = $out;
            $queuedData[] = $q;
            $answeredData[] = $ans;
            $abandonedData[] = $abd < 0 ? 0 : $abd;
            $labels[] = sprintf('%02d:00', $i);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Inbound',
                    'data' => $inboundData,
                    'borderColor' => '#3b82f6', // blue
                    'fill' => false,
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Outbound',
                    'data' => $outboundData,
                    'borderColor' => '#10b981', // green
                    'fill' => false,
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Queued',
                    'data' => $queuedData,
                    'borderColor' => '#f59e0b', // amber
                    'fill' => false,
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Answered',
                    'data' => $answeredData,
                    'borderColor' => '#8b5cf6', // purple
                    'fill' => false,
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Abandoned',
                    'data' => $abandonedData,
                    'borderColor' => '#ef4444', // red
                    'fill' => false,
                    'tension' => 0.4,
                ],
            ],
            'labels' => $labels,
        ];
    }
}
