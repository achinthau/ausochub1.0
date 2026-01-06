<?php

namespace App\Filament\Widgets;

use Filament\Widgets\PieChartWidget;
use App\Models\Cdr;
use Illuminate\Support\Carbon;

class AgentInboundPieChart extends PieChartWidget
{
    protected static ?string $heading = 'Inbound Performance';

    public ?string $extension = null;
    public ?string $startDate = null;
    public ?string $endDate = null;

    protected function getData(): array
    {
        if (!$this->extension) {
            return [];
        }

        $query = Cdr::query();
        
        // Date Filter
        $start = $this->startDate ? Carbon::parse($this->startDate)->startOfDay() : now()->startOfDay();
        $end = $this->endDate ? Carbon::parse($this->endDate)->endOfDay() : now()->endOfDay();

        $query->whereBetween('calldate', [$start, $end]);
        
        // Filter by Extension (Inbound)
        $query->where('dstchannel', 'LIKE', "%/{$this->extension}-%");

        $answered = (clone $query)->where('disposition', 'ANSWERED')->count();
        $total = $query->count();
        $missed = $total - $answered;

        $answeredPct = $total > 0 ? round(($answered / $total) * 100) : 0;
        $missedPct = $total > 0 ? round(($missed / $total) * 100) : 0;

        return [
            'datasets' => [
                [
                    'label' => 'Inbound Calls',
                    'data' => [$answered, $missed],
                    'backgroundColor' => ['#4ade80', '#f87171'], // Green, Red
                ],
            ],
            'labels' => [
                "Answered: {$answered} ({$answeredPct}%)", 
                "Missed: {$missed} ({$missedPct}%)"
            ],
        ];
    }
}
