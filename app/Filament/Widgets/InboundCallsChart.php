<?php

namespace App\Filament\Widgets;

use Filament\Widgets\LineChartWidget;
use App\Models\Cdr;
use Illuminate\Support\Facades\Auth;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class InboundCallsChart extends LineChartWidget
{
    protected static ?string $heading = 'Inbound Calls';
    protected static ?string $maxHeight = '300px';
    
    public ?string $startDate = null;
    public ?string $endDate = null;

    protected function getData(): array
    {
        $user = Auth::user();
        $companyId = $user->tenant_context; // Assuming tenant_context is company ID or similar

        $query = Cdr::query();

        // Filter by Tenant Context (Company)
        // Adjust logic based on how Cdr links to Company/Tenant.
        // Assuming Cdr -> Agent (extension) -> User (tenant_context)
        // OR Cdr has 'accountcode' matching tenant.
        // If tenant_context is a Company ID, we need to find calls for that company.
        
        // Since Cdr has direct 'extension' linkage to Agent, we can scope by agents of the tenant.
        if ($companyId) {
             // Get extensions belonging to the tenant via User model to avoid cross-DB join
             // Users have agent_id. We get agent IDs from users, then extensions from agents.
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

        // Inbound usually matches 'did' or direction. Cdr table structure varies.
        // Assuming simple COUNT for now.
        // Using Flowframe\Trend if available, or manual grouping.
        // Filament v2 uses Flowframe Trend by default in examples.
        
        $start = $this->startDate ? \Carbon\Carbon::parse($this->startDate)->startOfDay() : now()->startOfMonth();
        $end = $this->endDate ? \Carbon\Carbon::parse($this->endDate)->endOfDay() : now()->endOfMonth();

        $data = Trend::model(Cdr::class)
            // Re-apply scope to Trend query
            ->query($query)
            ->dateColumn('calldate')
            ->between(
                $start,
                $end,
            )
            ->perDay()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Inbound Calls',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $data->map(fn (TrendValue $value) => $value->date),
        ];
    }
}
