<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\CxTicket;
use Illuminate\Support\Facades\DB;

class TechnicianTicketCountBarChart extends ChartWidget
{
    protected static ?string $heading = 'Ticket Counts by Rating';
    protected int | string | array $columnSpan = 1;
    
    protected static ?string $maxHeight = '300px';
    
    protected static ?string $pollingInterval = null;

    protected function getData(): array
    {
        $technicianName = request()->query('technician');
        $startDate = request()->query('startDate', now()->subDays(30)->toDateString());
        $endDate = request()->query('endDate', now()->toDateString());

        // Fetch ratings distribution
        $ratings = CxTicket::query()
            ->when($technicianName, fn($q) => $q->where('technician_name', $technicianName))
            ->whereNotNull('satisfaction_rate')
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->when(auth()->user()->tenant_context, function ($query) {
                 $companyNames = array_filter(array_map('trim', explode(',', auth()->user()->tenant_context)));
                 $query->whereIn('company', $companyNames);
            })
            ->select('satisfaction_rate', DB::raw('count(*) as count'))
            ->groupBy('satisfaction_rate')
            ->orderBy('satisfaction_rate')
            ->get();

        $data = [0, 0, 0, 0, 0]; // 1 to 5 stars
        foreach ($ratings as $rating) {
            if ($rating->satisfaction_rate >= 1 && $rating->satisfaction_rate <= 5) {
                $data[$rating->satisfaction_rate - 1] = $rating->count;
            }
        }

        return [
            'datasets' => [
                [
                    'label' => 'Ticket Counts',
                    'data' => $data,
                    'backgroundColor' => [
                        '#fa0505ff', // Red (1 star)
                        '#f88a0cff', // Orange (2 stars)
                        '#fafa12ff', // Yellow (3 stars)
                        '#09f758ff', // Lime (4 stars)
                        '#156cefff', // Green (5 stars)
                    ],
                    'barThickness' => 50,
                ],
            ],
            'labels' => ['1 Star', '2 Stars', '3 Stars', '4 Stars', '5 Stars'],
        ];
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'stepSize' => 1,
                        'precision' => 0,
                    ],
                    'title' => [
                        'display' => true,
                        'text' => 'Ticket Count',
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
