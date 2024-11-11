<?php

namespace App\Http\Livewire\Tables;

use App\Models\DailyCallSummary as ModelsDailyCallSummary;
use Mediconesystems\LivewireDatatables\DateColumn;
use Mediconesystems\LivewireDatatables\NumberColumn;
use Mediconesystems\LivewireDatatables\Http\Livewire\LivewireDatatable;

class DailyCallSummaryTable extends LivewireDatatable
{
    public $model = ModelsDailyCallSummary::class;
    public $exportable = true;

    public function builder()
    {
        return ModelsDailyCallSummary::query();
    }

    public function columns()
    {
        return [
            // ID column
            NumberColumn::name('ac_daily_call_summaries.id')
                ->label('ID')
                ->sortable(),

            // Date column for daily summary
            DateColumn::name('ac_daily_call_summaries.date')
                ->label('Date')
                ->sortable()
                ->filterable(),

            // Inbound column
            NumberColumn::name('ac_daily_call_summaries.inbound')
                ->label('Inbound Calls')
                ->sortable(),

            // Outbound column
            NumberColumn::name('ac_daily_call_summaries.outbound')
                ->label('Outbound Calls')
                ->sortable(),

            // Queued column
            NumberColumn::name('ac_daily_call_summaries.queued')
                ->label('Queued Calls')
                ->sortable(),

            // Abandoned column
            NumberColumn::name('ac_daily_call_summaries.abandent')
                ->label('Abandoned Calls')
                ->sortable(),

            // Answered column
            NumberColumn::name('ac_daily_call_summaries.answered')
                ->label('Answered Calls')
                ->sortable(),
        ];
    }
}
