<?php

namespace App\Http\Livewire\Tables;

use App\Models\QueueCount;
use App\Models\Agent;
use Mediconesystems\LivewireDatatables\Column;
use Mediconesystems\LivewireDatatables\DateColumn;
use Mediconesystems\LivewireDatatables\Http\Livewire\LivewireDatatable;
use Illuminate\Support\Facades\DB;

class AgentCallSummaryTable extends LivewireDatatable
{
    public $hideable = 'select';
    public $exportable = true;

    public function builder()
    {
        return QueueCount::select([
            'au_user.fname as agent_name',
            'queuecount.queuename as queue_name',
            'queuecount.ani as contact_number',
            'queuecount.date as timestamp',
            // \DB::raw('SEC_TO_TIME(TIMESTAMPDIFF(SECOND, queuecount.date, MIN(disconnected.date))) AS duration') // Adding 'AS duration'
        ])
        ->join('queuecount as disconnected', 'queuecount.uniqueid', '=', 'disconnected.uniqueid')
        ->join('au_user', 'queuecount.agent', '=', 'au_user.extension')
        ->where('queuecount.status', 2)
        ->where('disconnected.status', 0)
        ->groupBy('queuecount.uniqueid', 'queuecount.queuename', 'queuecount.agent', 'queuecount.date');
        // ->orderBy('queuecount.date', 'desc');

    }

    public function columns()
    {
        return [
            DateColumn::name('date')->label('Date')
                // ->defaultSort('desc')
                ->sortBy('DATE_FORMAT(queuecount.date, "%m%d%Y")')
                ->sortable()
                ->filterable(),

            Column::name('au_user.fname')->label('Agent')
                ->sortable()
                ->filterable(),

            Column::name('queuename')->label('Queue Name')
                ->filterable(),

            Column::name('ani')->label('Contact Number')
                ->filterable(),

            Column::raw('SEC_TO_TIME(TIMESTAMPDIFF(SECOND, queuecount.date, MIN(disconnected.date))) AS duration')->label('Duration')
                ->sortable(),

            Column::raw("TIMESTAMPDIFF(SECOND, queuecount.date, MIN(disconnected.date)) AS total_seconds")
                ->label('Total Seconds'),
        ];
    }

    public function doDatetimeFilterStart($index, $start)
    {

        $this->activeDateFilters[$index]['start'] = $start == "" ? $start : $start . " 00:00:00";
        $this->page = 1;
        $this->setSessionStoredFilters();
    }

    public function doDatetimeFilterEnd($index, $end)
    {
        $this->activeDateFilters[$index]['end'] = $end == "" ? $end : $end . " 23:59:59";;
        $this->page = 1;
        $this->setSessionStoredFilters();
    }
    
}
