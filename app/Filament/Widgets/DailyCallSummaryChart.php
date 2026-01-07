<?php

namespace App\Filament\Widgets;

use App\Models\DailyCallSummary;
use Filament\Widgets\LineChartWidget;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DailyCallSummaryChart extends LineChartWidget
{
    protected static ?string $heading = 'Daily Call Trends';

    protected static ?string $maxHeight = '300px';

    public $startDate;
    public $endDate;

    protected function getData(): array
    {
        $data = DailyCallSummary::query()
            ->whereBetween('date', [$this->startDate, $this->endDate])
            ->orderBy('date')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Inbound',
                    'data' => $data->pluck('inbound')->toArray(),
                    'borderColor' => '#3b82f6', // blue
                    'fill' => false,
                ],
                [
                    'label' => 'Outbound',
                    'data' => $data->pluck('outbound')->toArray(),
                    'borderColor' => '#10b981', // green
                    'fill' => false,
                ],
                [
                    'label' => 'Queued',
                    'data' => $data->pluck('queued')->toArray(),
                    'borderColor' => '#f59e0b', // amber
                    'fill' => false,
                ],
                [
                    'label' => 'Abandoned',
                    'data' => $data->pluck('abandent')->toArray(),
                    'borderColor' => '#ef4444', // red
                    'fill' => false,
                ],
                [
                    'label' => 'Answered',
                    'data' => $data->pluck('answered')->toArray(),
                    'borderColor' => '#8b5cf6', // purple
                    'fill' => false,
                ],
            ],
            'labels' => $data->pluck('date')->map(fn($date) => Carbon::parse($date)->format('M d'))->toArray(),
        ];
    }
}
