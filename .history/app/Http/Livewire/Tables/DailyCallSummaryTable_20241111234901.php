<?php

namespace App\Http\Livewire\Tables;

use App\Models\DailyCallSummary as ModelsDailyCallSummary;
use Mediconesystems\LivewireDatatables\DateColumn;
use Mediconesystems\LivewireDatatables\NumberColumn;
use Mediconesystems\LivewireDatatables\Column;
use Mediconesystems\LivewireDatatables\Http\Livewire\LivewireDatatable;

class DailyCallSummaryTable extends LivewireDatatable
{
    public $hideable = 'select';
    public $exportable = true;

    public function builder()
    {
        // Set the alias once to avoid duplication
        return ModelsDailyCallSummary::query()->from('ac_daily_call_summaries as daily_call_summaries');
    }

    public function columns()
    {
        return [
            Column::name('daily_call_summaries.id')
                ->label('ID')
                ->linkTo('daily_call_summaries', 6)
                ->sortable(),

            DateColumn::name('daily_call_summaries.date')
                ->label('Date')
                ->sortable()
                ->filterable(),

            NumberColumn::name('daily_call_summaries.inbound')
                ->label('Inbound Calls')
                ->sortable()
                ->filterable(),

            NumberColumn::name('daily_call_summaries.outbound')
                ->label('Outbound Calls')
                ->sortable()
                ->filterable(),

            NumberColumn::name('daily_call_summaries.queued')
                ->label('Queued Calls')
                ->sortable()
                ->filterable(),

            NumberColumn::name('daily_call_summaries.abandent')
                ->label('Abandoned Calls')
                ->sortable()
                ->filterable(),

            NumberColumn::name('daily_call_summaries.answered')
                ->label('Answered Calls')
                ->sortable()
                ->filterable(),
        ];
    }
}
