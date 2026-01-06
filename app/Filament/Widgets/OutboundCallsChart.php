<?php

namespace App\Filament\Widgets;

use Filament\Widgets\BarChartWidget;
use App\Models\Cdr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OutboundCallsChart extends BarChartWidget
{
    protected static ?string $heading = 'Outbound Calls (By Campaign)';
    
    public ?string $startDate = null;
    public ?string $endDate = null;

    protected function getData(): array
    {
        $user = Auth::user();
        $companyId = $user->tenant_context;

        // Group by Campaign.
        // Assuming 'accountcode' or similar field holds Campaign ID or Name.
        // OR using a join if Campaign is separate.
        // For now, let's assume 'accountcode' is the campaign identifier or grouping key.
        
        $query = Cdr::query();
        
        if ($companyId) {
             $agentIds = \App\Models\User::where('tenant_context', $companyId)->pluck('agent_id')->filter()->toArray();
             
             $extensions = [];
             if (!empty($agentIds)) {
                 $extensions = \App\Models\Agent::whereIn('id', $agentIds)->pluck('extension')->toArray();
             }

             if (empty($extensions)) {
                 $query->whereRaw('0 = 1');
             } else {
                 $query->whereIn('src', $extensions);
             }
        }

        // Filter outbound? Usually internal to external.
        // $query->where('direction', 'outbound'); // If such column exists.


        $start = $this->startDate ? \Carbon\Carbon::parse($this->startDate)->startOfDay() : now()->startOfMonth();
        $end = $this->endDate ? \Carbon\Carbon::parse($this->endDate)->endOfDay() : now()->endOfMonth();

        $query->whereBetween('calldate', [$start, $end]);

        // Group by 'accountcode' (assuming this holds campaign ID/Name)
        $data = $query->select('accountcode', \Illuminate\Support\Facades\DB::raw('count(*) as count'))
            ->groupBy('accountcode')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Calls',
                    'data' => $data->pluck('count'),
                ],
            ],
            'labels' => $data->pluck('accountcode'),
        ];
    }
}
