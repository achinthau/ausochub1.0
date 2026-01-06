<?php

namespace App\Filament\Widgets;

use Filament\Widgets\PieChartWidget;
use App\Models\Cdr;
use Illuminate\Support\Facades\Auth;

class GlobalDispositionPieChart extends PieChartWidget
{
    protected static ?string $heading = 'Call Disposition (Inbound)';
    protected static ?int $sort = 3;
    protected static ?string $maxHeight = '300px';
    
    public ?string $startDate = null;
    public ?string $endDate = null;

    protected function getData(): array
    {
        $user = Auth::user();
        $companyId = $user->tenant_context;

        $query = Cdr::query();

        // ---------------------------------------------------------
        // TENANT SCOPING
        // ---------------------------------------------------------
        if ($companyId) {
             $agentIds = \App\Models\User::where('tenant_context', $companyId)->pluck('agent_id')->filter()->toArray();
             
             $extensions = [];
             if (!empty($agentIds)) {
                 $extensions = \App\Models\Agent::whereIn('id', $agentIds)->pluck('extension')->toArray();
             }

             if (empty($extensions)) {
                 $query->whereRaw('0 = 1');
             } else {
                 $query->where(function ($q) use ($extensions) {
                     foreach ($extensions as $extension) {
                         $q->orWhere('dstchannel', 'LIKE', "%/{$extension}-%");
                     }
                 });
             }
        }

        // ---------------------------------------------------------
        // DATE FILTERING
        // ---------------------------------------------------------
        $start = $this->startDate ? \Carbon\Carbon::parse($this->startDate)->startOfDay() : now()->startOfMonth();
        $end = $this->endDate ? \Carbon\Carbon::parse($this->endDate)->endOfDay() : now()->endOfMonth();

        $query->whereBetween('calldate', [$start, $end]);

        // ---------------------------------------------------------
        // AGGREGATION
        // ---------------------------------------------------------
        $answered = (clone $query)->where('disposition', 'ANSWERED')->count();
        $total = $query->count();
        $missed = $total - $answered;

        $answeredPct = $total > 0 ? round(($answered / $total) * 100, 1) : 0;
        $missedPct = $total > 0 ? round(($missed / $total) * 100, 1) : 0;

        return [
            'datasets' => [
                [
                    'label' => 'Call Disposition',
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
