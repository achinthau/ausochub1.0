<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use App\Models\Cdr;
use Illuminate\Support\Facades\Auth;

class PerformanceStatsOverview extends BaseWidget
{
    public ?string $startDate = null;
    public ?string $endDate = null;

    protected function getCards(): array
    {
        $user = Auth::user();
        $companyId = $user->tenant_context; // Assuming tenant_context is company ID or similar

        $query = Cdr::query();

        // ---------------------------------------------------------
        // COPYING TENANT SCOPING LOGIC FROM InboundCallsChart
        // ---------------------------------------------------------
        if ($companyId) {
             // Get extensions belonging to the tenant via User model to avoid cross-DB join
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

        // Timeframe: Use properties
        $start = $this->startDate ? \Carbon\Carbon::parse($this->startDate)->startOfDay() : now()->startOfMonth();
        $end = $this->endDate ? \Carbon\Carbon::parse($this->endDate)->endOfDay() : now()->endOfMonth();

        $query->whereBetween('calldate', [$start, $end]);

        $totalCalls = (clone $query)->count();
        $answeredCalls = (clone $query)->where('disposition', 'ANSWERED')->count();
        $missedCalls = $totalCalls - $answeredCalls;

        $abandonmentRate = $totalCalls > 0 ? round(($missedCalls / $totalCalls) * 100, 1) : 0;

        return [
            Card::make('Total Inbound Calls', $totalCalls)
                ->description('Received Calls')
                ->descriptionIcon('heroicon-s-phone-incoming')
                ->color('primary'),

            Card::make('Answered Calls', $answeredCalls)
                ->description('Successful Connections')
                ->descriptionIcon('heroicon-s-check-circle')
                ->color('success'),

            Card::make('Missed / Abandoned', $missedCalls)
                ->description('No Answer / Busy / Failed')
                ->descriptionIcon('heroicon-s-x-circle')
                ->color('danger'),

            Card::make('Abandonment Rate', $abandonmentRate . '%')
                ->description('Percentage of missed calls')
                ->descriptionIcon('heroicon-s-trending-down')
                ->color($abandonmentRate > 20 ? 'danger' : ($abandonmentRate > 10 ? 'warning' : 'success')),
        ];
    }
}
