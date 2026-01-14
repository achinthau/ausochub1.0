<?php

namespace App\Filament\Widgets;

use App\Models\AuIvrCall;
use Filament\Widgets\LineChartWidget;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class IvrCallVolumeChart extends LineChartWidget
{
    protected static ?string $heading = 'Daily IVR Call Volume';
    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $startDate = request()->query('startDate', now()->subDays(30)->toDateString());
        $endDate = request()->query('endDate', now()->toDateString());

        $data = AuIvrCall::query()
            ->whereBetween('date', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->select(DB::raw('DATE(date) as day'), DB::raw('count(*) as count'))
            ->groupBy('day')
            ->orderBy('day')
            ->get()
            ->pluck('count', 'day')
            ->toArray();

        // Fill in missing days with 0
        $period = CarbonPeriod::create($startDate, $endDate);
        $labels = [];
        $values = [];

        foreach ($period as $date) {
            $day = $date->toDateString();
            $labels[] = $date->format('M d');
            $values[] = $data[$day] ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Total Calls',
                    'data' => $values,
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => true,
                ],
            ],
            'labels' => $labels,
        ];
    }
}
