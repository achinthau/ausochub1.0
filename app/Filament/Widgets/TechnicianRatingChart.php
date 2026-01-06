<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\CxTicket;
use Illuminate\Support\Facades\DB;

class TechnicianRatingChart extends ChartWidget
{
    protected static ?string $heading = 'Technician Satisfaction Ratings (Percentage)';
    protected int | string | array $columnSpan = 1;
    
    protected static ?string $maxHeight = '300px';
    
    protected static string $view = 'filament.widgets.technician-rating-chart';
    
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

        $totalCount = $ratings->sum('count');
        $data = [0, 0, 0, 0, 0]; // 1 to 5 stars
        $labels = ['1 Star', '2 Stars', '3 Stars', '4 Stars', '5 Stars'];
        
        foreach ($ratings as $rating) {
            if ($rating->satisfaction_rate >= 1 && $rating->satisfaction_rate <= 5) {
                $percentage = $totalCount > 0 ? round(($rating->count / $totalCount) * 100, 1) : 0;
                $data[$rating->satisfaction_rate - 1] = $percentage;
                
                // Update label to include percentage
                $labels[$rating->satisfaction_rate - 1] = $rating->satisfaction_rate . ' Stars (' . $percentage . '%)';
            }
        }
        
        // Ensure labels for empty ratings also show 0% if desired, or keep generic. 
        // Let's iterate to ensure all labels have a percentage even if 0.
        for ($i = 0; $i < 5; $i++) {
            if (strpos($labels[$i], '%') === false) {
                 $labels[$i] = ($i + 1) . ' Stars (0%)';
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
                    'borderWidth' => 0,              // 👈 remove border
                    'borderColor' => 'transparent',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'right',
                    'labels' => [
                        'font' => [
                            'size' => 14,
                            'weight' => 'bold',
                        ],
                        'boxWidth' => 10,
                        'padding' => 10,
                    ],
                ],
            ],
            'cutout' => '70%', // Makes it a donut
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
