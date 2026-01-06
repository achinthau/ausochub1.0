<?php

namespace App\Filament\Widgets;

use App\Models\AuIvrCall;
use Filament\Widgets\BarChartWidget;
use Illuminate\Support\Facades\DB;

class IvrComparisonChart extends BarChartWidget
{
    // protected static ?string $heading = 'DNIS Comparison (Total vs Answered vs Missed)';
    protected int | string | array $columnSpan = 'full';
    
    public $startDate;
    public $endDate;

    protected static ?string $pollingInterval = null;

    protected function getData(): array
    {
        $startDate = $this->startDate ?? request()->query('startDate', now()->subDays(30)->toDateString());
        $endDate = $this->endDate ?? request()->query('endDate', now()->toDateString());

        $data = AuIvrCall::query()
            ->whereBetween('date', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->leftJoin('cdr', 'au_ivr_calls.uniqueid', '=', 'cdr.uniqueid')
            ->select('dnis')
            ->selectRaw('count(au_ivr_calls.id) as total_calls')
            ->selectRaw('count(CASE WHEN cdr.disposition = "ANSWERED" THEN 1 END) as answered_calls')
            ->selectRaw('count(CASE WHEN cdr.disposition != "ANSWERED" OR cdr.disposition IS NULL THEN 1 END) as missed_calls')
            ->groupBy('dnis')
            ->get();

        $labels = $data->pluck('dnis')->toArray();
        $totalCalls = $data->pluck('total_calls')->toArray();
        $answeredCalls = $data->pluck('answered_calls')->toArray();
        $missedCalls = $data->pluck('missed_calls')->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Total Calls',
                    'data' => $totalCalls,
                    'backgroundColor' => '#3b82f6',
                ],
                [
                    'label' => 'Answered',
                    'data' => $answeredCalls,
                    'backgroundColor' => '#10b981',
                ],
                [
                    'label' => 'Missed',
                    'data' => $missedCalls,
                    'backgroundColor' => '#ef4444',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'x' => [
                    'ticks' => [
                        'font' => [
                            'size' => 14,
                            'weight' => 'bold',
                        ],
                    ],
                ],
            ],
        ];
    }
}
