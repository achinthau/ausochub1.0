<?php

namespace App\Filament\Widgets;

use App\Models\AuIvrCall;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use Illuminate\Support\Facades\DB;

class IvrSummaryStats extends BaseWidget
{
    protected function getCards(): array
    {
        $startDate = request()->query('startDate', now()->subDays(30)->toDateString());
        $endDate = request()->query('endDate', now()->toDateString());

        $stats = AuIvrCall::query()
            ->whereBetween('date', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->leftJoin('cdr', 'au_ivr_calls.uniqueid', '=', 'cdr.uniqueid')
            ->selectRaw('count(au_ivr_calls.id) as total_calls')
            ->selectRaw('count(CASE WHEN cdr.disposition = "ANSWERED" THEN 1 END) as answered_calls')
            ->selectRaw('count(CASE WHEN cdr.disposition != "ANSWERED" OR cdr.disposition IS NULL THEN 1 END) as missed_calls')
            ->first();

        $totalCalls = $stats->total_calls ?? 0;
        $answeredCalls = $stats->answered_calls ?? 0;
        $missedCalls = $stats->missed_calls ?? 0;
        $answerRate = $totalCalls > 0 ? round(($answeredCalls / $totalCalls) * 100, 2) : 0;

        return [
            Card::make('Total IVR Calls', $totalCalls)
                ->description('Total calls identified in IVR')
                ->descriptionIcon('heroicon-s-phone')
                ->color('primary'),
            Card::make('Answered calls', $answeredCalls)
                ->description('Calls that reached final stage')
                ->descriptionIcon('heroicon-s-check-circle')
                ->color('success'),
            Card::make('Missed / Dropped', $missedCalls)
                ->description('Calls hung up or failed')
                ->descriptionIcon('heroicon-s-x-circle')
                ->color('danger'),
            Card::make('Answer Rate', $answerRate . '%')
                ->description('Overall IVR success rate')
                ->descriptionIcon('heroicon-s-chart-bar')
                ->color($answerRate > 80 ? 'success' : ($answerRate > 50 ? 'warning' : 'danger')),
        ];
    }
}
