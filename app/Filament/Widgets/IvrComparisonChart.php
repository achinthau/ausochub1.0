<?php

namespace App\Filament\Widgets;

use App\Models\AuIvrCall;
use Filament\Widgets\LineChartWidget;
use Illuminate\Support\Facades\DB;

class IvrComparisonChart extends LineChartWidget
{
    // protected static ?string $heading = 'DNIS Comparison (Total vs Answered vs Missed)';
    protected int | string | array $columnSpan = 'full';
    
    public $startDate;
    public $endDate;
    public $dnis;

    protected static ?string $pollingInterval = null;

    protected function getHeading(): string|null
    {
        return "Performance for DNIS: " . ($this->dnis ?? 'All');
    }

    protected function getData(): array
    {
        $startDate = $this->startDate ?? request()->query('startDate', now()->subDays(30)->toDateString());
        $endDate = $this->endDate ?? request()->query('endDate', now()->toDateString());

        $query = AuIvrCall::query()
            ->whereBetween('au_ivr_calls.date', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->leftJoin('cdr', 'au_ivr_calls.uniqueid', '=', 'cdr.uniqueid')
            ->selectRaw('DATE(au_ivr_calls.date) as date_only')
            ->selectRaw('count(au_ivr_calls.id) as total_calls')
            ->selectRaw('count(CASE WHEN cdr.disposition = "ANSWERED" THEN 1 END) as answered_calls')
            ->selectRaw('count(CASE WHEN cdr.disposition != "ANSWERED" OR cdr.disposition IS NULL THEN 1 END) as missed_calls')
            ->groupBy(DB::raw('DATE(au_ivr_calls.date)'))
            ->orderBy(DB::raw('DATE(au_ivr_calls.date)'));

        if ($this->dnis) {
            $query->where('au_ivr_calls.dnis', $this->dnis);
        }

        $data = $query->get();

        $labels = $data->pluck('date_only')->toArray();
        $totalCalls = $data->pluck('total_calls')->toArray();
        $answeredCalls = $data->pluck('answered_calls')->toArray();
        $missedCalls = $data->pluck('missed_calls')->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Total Calls',
                    'data' => $totalCalls,
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => '#3b82f6',
                    'fill' => false,
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Answered',
                    'data' => $answeredCalls,
                    'borderColor' => '#10b981',
                    'backgroundColor' => '#10b981',
                    'fill' => false,
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Missed',
                    'data' => $missedCalls,
                    'borderColor' => '#ef4444',
                    'backgroundColor' => '#ef4444',
                    'fill' => false,
                    'tension' => 0.4,
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
                            'size' => 12,
                            'weight' => 'normal',
                        ],
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                ],
            ],
        ];
    }
}
