<?php

namespace App\Filament\Widgets;

use App\Models\DailyQueueSummary;
use Filament\Widgets\LineChartWidget;
use Carbon\Carbon;

class DailyQueueSummaryChart extends LineChartWidget
{
    protected static ?string $maxHeight = '300px';

    public $startDate;
    public $endDate;
    public $queue;

    protected function getHeading(): ?string
    {
        return "Trends for " . $this->queue;
    }

    protected function getData(): array
    {
        $data = DailyQueueSummary::query()
            ->where('queue', $this->queue)
            ->whereBetween('date', [$this->startDate, $this->endDate])
            ->orderBy('date')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Calls',
                    'data' => $data->pluck('calls')->toArray(),
                    'borderColor' => '#3b82f6', // blue
                    'fill' => false,
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Answered',
                    'data' => $data->pluck('answered')->toArray(),
                    'borderColor' => '#10b981', // green
                    'fill' => false,
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Abandoned',
                    'data' => $data->pluck('abandoned')->toArray(),
                    'borderColor' => '#ef4444', // red
                    'fill' => false,
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Agents',
                    'data' => $data->pluck('agents')->toArray(),
                    'borderColor' => '#8b5cf6', // purple
                    'fill' => false,
                    'tension' => 0.4,
                ],
            ],
            'labels' => $data->pluck('date')->map(fn($date) => Carbon::parse($date)->format('M d'))->toArray(),
        ];
    }
}
