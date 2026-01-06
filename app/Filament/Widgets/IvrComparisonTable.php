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

    protected static ?string $pollingInterval = null;

    public function getTableRecordKey($record): string
    {
        return $record->dnis;
    }

    protected function getTableQuery(): Builder
    {
        $startDate = $this->startDate ?? request()->query('startDate', now()->subDays(30)->toDateString());
        $endDate = $this->endDate ?? request()->query('endDate', now()->toDateString());

        return AuIvrCall::query()
            ->whereBetween('date', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->select('dnis')
            ->selectRaw('count(au_ivr_calls.id) as total_calls')
            ->selectRaw('count(CASE WHEN cdr.disposition = "ANSWERED" THEN 1 END) as answered_calls')
            ->selectRaw('count(CASE WHEN cdr.disposition != "ANSWERED" OR cdr.disposition IS NULL THEN 1 END) as missed_calls')
            ->leftJoin('cdr', 'au_ivr_calls.uniqueid', '=', 'cdr.uniqueid')
            ->groupBy('dnis');
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('dnis')
                ->label('DNIS')
                ->searchable()
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
