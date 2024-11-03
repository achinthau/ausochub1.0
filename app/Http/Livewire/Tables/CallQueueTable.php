<?php

namespace App\Http\Livewire\Tables;

use App\Models\QueueCount;
use App\Models\Agent;
use Mediconesystems\LivewireDatatables\Column;
use Mediconesystems\LivewireDatatables\DateColumn;
use Mediconesystems\LivewireDatatables\Http\Livewire\LivewireDatatable;
use Illuminate\Support\Facades\DB;

class CallQueueTable extends LivewireDatatable
{
    public $hideable = 'select';
    public $exportable = true;

    public function builder()
    {
        return QueueCount::latestRecord()->withStatusZero()
        ->join('au_user', 'queuecount.agent', '=', 'au_user.extension')
        ->with('agent');
        // dd($query);
    }

    public function columns()
    {
        return [
            DateColumn::name('queuecount.date')->label('Date')
            ->sortBy('DATE_FORMAT(queuecount.date, "%m%d%Y")')
                ->sortable()
                ->filterable(),
            Column::name('queuecount.queuename')->label('Queue Name')
            ->filterable(),
            Column::name('queuecount.uniqueid')->label('Unique ID')
            ->filterable(),
            Column::name('queuecount.ani')->label('Contact Number')
            ->filterable(),
            Column::callback(['queuecount.status'], function ($status) {
                return $status == 2 ? 'Answered' : 'Abandoned';
            })->label('Call Status'),
            Column::name('agent.fname')->label('Agent')
            ->filterable(),
        ];
    }

}