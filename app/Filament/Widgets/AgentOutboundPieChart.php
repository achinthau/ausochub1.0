<?php

namespace App\Filament\Widgets;

use Filament\Widgets\PieChartWidget;
use Illuminate\Support\Carbon;

class AgentOutboundPieChart extends PieChartWidget
{
    protected static ?string $heading = 'Outbound Performance';

    public $agentId = null;
    public ?string $startDate = null;
    public ?string $endDate = null;
    public ?string $campaignId = null;

    protected function getData(): array
    {
        if (!$this->agentId) {
            return [];
        }

        $user = \App\Models\User::where('agent_id', $this->agentId)->first();

        if (!$user) {
             return [];
        }

        // Date Range
        $start = $this->startDate ? Carbon::parse($this->startDate)->startOfDay() : now()->startOfDay();
        $end = $this->endDate ? Carbon::parse($this->endDate)->endOfDay() : now()->endOfDay();

        // 1. Get Agent's Total Dials (All attempts)
        $agentQuery = \App\Models\FeedContactAttempt::query()
            ->whereBetween('created_at', [$start, $end])
            ->where('updated_by', $user->id);
            
        if (!empty($this->campaignId)) {
            $agentQuery->where('campaign_id', $this->campaignId);
        }

        $agentDials = $agentQuery->count();

        // 2. Get Company Total Dials (All Agents)
        $companyId = $user->tenant_context;
        $companyUserIds = \App\Models\User::where('tenant_context', $companyId)->pluck('id')->toArray();
        
        $companyQuery = \App\Models\FeedContactAttempt::query()
            ->whereBetween('created_at', [$start, $end])
            ->whereIn('updated_by', $companyUserIds);
            
        if (!empty($this->campaignId)) {
            $companyQuery->where('campaign_id', $this->campaignId);
        }

        $companyTotal = $companyQuery->count();
        
        // Calculate Remainder (Company Total - Agent Dials)
        $remainder = max(0, $companyTotal - $agentDials);
        
        // Percentages
        $agentPct = $companyTotal > 0 ? round(($agentDials / $companyTotal) * 100) : 0;
        $remainderPct = $companyTotal > 0 ? (100 - $agentPct) : 0;

        return [
            'datasets' => [
                [
                    'label' => 'Outbound Dials',
                    'data' => [$agentDials, $remainder],
                    'backgroundColor' => ['#4ade80', '#e5e7eb'], // Green (Agent), Gray (Others)
                ],
            ],
            'labels' => [
                "Dials: {$agentDials} ({$agentPct}%)", 
                "Total Dials: {$companyTotal}"
            ],
        ];
    }
}
