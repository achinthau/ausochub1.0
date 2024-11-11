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
        // Specify the table alias explicitly in the query builder
        return ModelsDailyCallSummary::query()->from('ac_daily_call_summaries as daily_call_summaries');
    }

    public function columns()
    {
        return [
            // ID column with link functionality (optional, for debugging purposes)
            Column::name('daily_call_summaries.id')
                ->label('ID')
                ->linkTo('daily_call_summaries', 6)
                ->sortable(),

            // Date column for daily summary
            DateColumn::name('daily_call_summaries.date')
                ->label('Date')
                ->sortable()
                ->filterable(),

            // Inbound column (number of inbound calls)
            NumberColumn::name('daily_call_summaries.inbound')
                ->label('Inbound Calls')
                ->sortable()
                ->filterable(),

            // Outbound column (number of outbound calls)
            NumberColumn::name('daily_call_summaries.outbound')
                ->label('Outbound Calls')
                ->sortable()
                ->filterable(),

            // Queued column (number of queued calls)
            NumberColumn::name('daily_call_summaries.queued')
                ->label('Queued Calls')
                ->sortable()
                ->filterable(),

            // Abandoned column (number of abandoned calls)
            NumberColumn::name('daily_call_summaries.abandent')
                ->label('Abandoned Calls')
                ->sortable()
                ->filterable(),

            // Answered column (number of answered calls)
            NumberColumn::name('daily_call_summaries.answered')
                ->label('Answered Calls')
                ->sortable()
                ->filterable(),
        ];
    }
}
