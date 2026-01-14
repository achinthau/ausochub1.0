<?php

namespace App\Filament\Widgets;

use App\Models\AuIvrCall;
use Filament\Tables;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class IvrComparisonTable extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    public $startDate;
    public $endDate;
    public $dnis;

    protected static ?string $pollingInterval = null;

    protected function getTableHeading(): string|null
    {
        return "DNIS: " . ($this->dnis ?? 'All');
    }

    public function getTableRecordKey($record): string
    {
        return $record->date_only;
    }

    protected function getTableQuery(): Builder
    {
        $startDate = $this->startDate ?? request()->query('startDate', now()->subDays(30)->toDateString());
        $endDate = $this->endDate ?? request()->query('endDate', now()->toDateString());

        $query = AuIvrCall::query()
            ->whereBetween('au_ivr_calls.date', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->selectRaw('DATE(au_ivr_calls.date) as date_only')
            ->selectRaw('count(au_ivr_calls.id) as total_calls')
            ->selectRaw('count(CASE WHEN cdr.disposition = "ANSWERED" THEN 1 END) as answered_calls')
            ->selectRaw('count(CASE WHEN cdr.disposition != "ANSWERED" OR cdr.disposition IS NULL THEN 1 END) as missed_calls')
            ->leftJoin('cdr', 'au_ivr_calls.uniqueid', '=', 'cdr.uniqueid')
            ->groupBy(DB::raw('DATE(au_ivr_calls.date)'))
            ->orderBy(DB::raw('DATE(au_ivr_calls.date)'), 'desc');

        if ($this->dnis) {
            $query->where('au_ivr_calls.dnis', $this->dnis);
        }

        return $query;
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('date_only')
                ->label('Date')
                ->sortable(),
            Tables\Columns\TextColumn::make('total_calls')
                ->label('Total Calls')
                ->sortable(),
            Tables\Columns\TextColumn::make('answered_calls')
                ->label('Answered')
                ->sortable()
                ->color('success'),
            Tables\Columns\TextColumn::make('missed_calls')
                ->label('Missed')
                ->sortable()
                ->color('danger'),
            Tables\Columns\TextColumn::make('success_rate')
                ->label('Success Rate')
                ->getStateUsing(fn ($record) => $record->total_calls > 0 ? round(($record->answered_calls / $record->total_calls) * 100, 2) . '%' : '0%')
                ->sortable(),
        ];
    }
}
