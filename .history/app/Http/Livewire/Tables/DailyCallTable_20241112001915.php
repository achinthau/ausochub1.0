<?php

namespace App\Http\Livewire\Tables;

use App\Models\DailyCallSummary;
use Mediconesystems\LivewireDatatables\DateColumn;
use Mediconesystems\LivewireDatatables\NumberColumn;
use Mediconesystems\LivewireDatatables\Column;
use Mediconesystems\LivewireDatatables\Http\Livewire\LivewireDatatable;

class DailyCallTable extends LivewireDatatable
{
    public $model = DailyCallSummary::class;

    public function builder()
    {
        // Return a query for fetching data from the daily_queue_summeries table
        return DailyCallSummary::query();
    }

    public function columns()
    {
        return [
            Column::name('id')
                ->label('ID')
                ->sortable()
                ->searchable(),

            DateColumn::name('date')
                ->label('Date')
                ->sortable()
                ->filterable(),

            NumberColumn::name('inbound')
                ->label('Inbound Calls')
                ->sortable()
                ,

            NumberColumn::name('outbound')
                ->label('Outbound Calls')
                ->sortable()
                ,

            NumberColumn::name('queued')
                ->label('Queued Calls')
                ->sortable()
                ,

            NumberColumn::name('abandent')
                ->label('Abandoned Calls')
                ->sortable()
                ,

            NumberColumn::name('answered')
                ->label('Answered Calls')
                ->sortable()
                ,
        ];
    }
}
