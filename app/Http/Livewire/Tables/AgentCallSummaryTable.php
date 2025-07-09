<?php

namespace App\Http\Livewire\Tables;

use App\Models\QueueCount;
use App\Models\Agent;
use App\Models\QueueCountReport;
use Carbon\Carbon;
use Mediconesystems\LivewireDatatables\Column;
use Mediconesystems\LivewireDatatables\DateColumn;
use Mediconesystems\LivewireDatatables\Http\Livewire\LivewireDatatable;
use Illuminate\Support\Facades\DB;
use Mediconesystems\LivewireDatatables\Exports\DatatableExport;

class AgentCallSummaryTable extends LivewireDatatable
{
    public $hideable = 'select';
    public $exportable = true;

    public function builder()
    {
        return QueueCountReport::select([
            'au_user.fname as agent_name',
            'au_queuecount_report.queuename as queue_name',
            'au_queuecount_report.ani as contact_number',
            'au_queuecount_report.date as timestamp',
            // \DB::raw('SEC_TO_TIME(TIMESTAMPDIFF(SECOND, au_queuecount_report.date, MIN(disconnected.date))) AS duration') // Adding 'AS duration'
        ])
            ->join('au_queuecount_report as disconnected', 'au_queuecount_report.uniqueid', '=', 'disconnected.uniqueid')
            ->join('au_user', 'au_queuecount_report.agent', '=', 'au_user.extension')
            ->where('au_queuecount_report.status', 2)
            ->where('disconnected.status', 0)
            ->groupBy('au_queuecount_report.uniqueid', 'au_queuecount_report.queuename', 'au_queuecount_report.agent', 'au_queuecount_report.date');
        // ->orderBy('au_queuecount_report.date', 'desc');

    }

    public function columns()
    {
        return [
            DateColumn::name('date')->label('Date')
                // ->defaultSort('desc')
                // ->sortBy('DATE_FORMAT(au_queuecount_report.date, "%m%d%Y")')
                ->sortable()->defaultSort('desc')
                ->filterable(),

            Column::name('au_user.fname')->label('Agent')
                ->sortable()
                ->filterable(),

            Column::name('queuename')->label('Queue Name')
                ->filterable(),

            Column::name('ani')->label('From')
                ->filterable(),

                Column::name('dnis')->label('To')
                ->filterable(),

            Column::raw('SEC_TO_TIME(TIMESTAMPDIFF(SECOND, au_queuecount_report.date, MIN(disconnected.date))) AS duration')->label('Duration')
                ->sortable(),

            // Column::raw("TIMESTAMPDIFF(SECOND, au_queuecount_report.date, MIN(disconnected.date)) AS total_seconds")
            //     ->label('Total Seconds'),
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


    public function export(string $filename = 'DatatableExport.xlsx')
    {
        $this->forgetComputed();

        $export = new DatatableExport($this->getExportResultsSet());
        $export->setFilename('agent_queue_summary_' . Carbon::now()->format('Ymdhis') . '.csv');

        return $export->download();
    }
}
