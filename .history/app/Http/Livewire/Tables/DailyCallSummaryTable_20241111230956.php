<?php

namespace App\Http\Livewire\Tables;

use App\DailyCallSummary;
use App\Models\DailyCallSummary as ModelsDailyCallSummary;
use Mediconesystems\LivewireDatatables\Action;
use Mediconesystems\LivewireDatatables\Column;
use Mediconesystems\LivewireDatatables\DateColumn;
use Mediconesystems\LivewireDatatables\NumberColumn;
use Mediconesystems\LivewireDatatables\Http\Livewire\LivewireDatatable;

class DailyCallSummaryTable extends LivewireDatatable
{
    public $model = ModelsDailyCallSummary::class;
    public $exportable = true;

    public function builder()
    {
        // Return a query to fetch data from the daily_call_summaries table
        return ModelsDailyCallSummary::query();  // Consistent aliasing
        // ->orderBy('daily_call_summaries.date', 'desc');
    }


    public function columns()
    {
        return [
            // ID column (optional, for debugging purposes)
            NumberColumn::name('id')
                ->label('ID')
                ->sortable(),
                // ->filterable(),

            // Date column for daily summary
            DateColumn::name('date')
                ->label('Date')
                ->sortable()
                ->filterable(),

            // Inbound column (number of inbound calls)
            NumberColumn::name('inbound')
                ->label('Inbound Calls')
                ->sortable(),
                // ->filterable(),

            // Outbound column (number of outbound calls)
            NumberColumn::name('outbound')
                ->label('Outbound Calls')
                ->sortable(),
                // ->filterable(),

            // Queued column (number of queued calls)
            NumberColumn::name('queued')
                ->label('Queued Calls')
                ->sortable(),
                // ->filterable(),

            // Abandoned column (number of abandoned calls)
            NumberColumn::name('abandent')
                ->label('Abandoned Calls')
                ->sortable(),
                // ->filterable(),

            // Answered column (number of answered calls)
            NumberColumn::name('answered')
                ->label('Answered Calls')
                ->sortable()
                // ->filterable(),

            
        ];
    }
}