<?php

namespace App\Http\Livewire\Tables;

use App\Models\DailyCallSummary;
use Mediconesystems\LivewireDatatables\DateColumn;
use Mediconesystems\LivewireDatatables\NumberColumn;
use Mediconesystems\LivewireDatatables\Column;
use Mediconesystems\LivewireDatatables\Http\Livewire\LivewireDatatable;

class DailyCallSummaryTable extends LivewireDatatable
{
    public $model = DailyCallSummary::class;

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
                ->filterable(),

            NumberColumn::name('outbound')
                ->label('Outbound Calls')
                ->sortable()
                ->filterable(),

            NumberColumn::name('queued')
                ->label('Queued Calls')
                ->sortable()
                ->filterable(),

            NumberColumn::name('abandent')
                ->label('Abandoned Calls')
                ->sortable()
                ->filterable(),

            NumberColumn::name('answered')
                ->label('Answered Calls')
                ->sortable()
                ->filterable(),

            Column::callback(['id'], function ($id) {
                return view('components.action-buttons', ['id' => $id]);
            })->label('Actions')
        ];
    }
}
