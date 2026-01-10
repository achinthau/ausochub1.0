<?php

namespace App\Http\Livewire\Reports;

use App\Models\Cdr;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Mediconesystems\LivewireDatatables\Column;
use Mediconesystems\LivewireDatatables\DateColumn;
use Mediconesystems\LivewireDatatables\Http\Livewire\LivewireDatatable;
use Mediconesystems\LivewireDatatables\NumberColumn;
use Illuminate\Support\Facades\DB;
use Mediconesystems\LivewireDatatables\Exports\DatatableExport;

class AgentMissedCallSummaryTable extends LivewireDatatable
{

    public $hideable = 'select';
    public $exportable = true;
    public function builder()
    {
        return Cdr::query()
            ->whereIn('lastapp', ['Queue'])
            ->where('disposition', ['NO ANSWER'])
            ->leftJoin('au_user', DB::raw("REGEXP_SUBSTR(dstchannel, '(?<=/)[0-9]+(?=-)')"), '=', 'au_user.extension');
    }

    public function columns()
    {
        //id									created_at	updated_at
        $dateColum = DateColumn::name('calldate')->label('Call At')->format('Y-m-d H:i:s')->filterable()->sortBy('calldate')->defaultSort('desc');
        $dateColum->sortable = true;

        return [
            Column::name('uniqueid')->label('RecId')->filterable()->hide(),
            $dateColum,
            Column::raw("SUBSTRING_INDEX(lastdata, ',', 1) AS queue_name")
                ->label('Queue Name')
                ->filterable()
                ->searchable(),
            Column::name('src')->label('Caller ID')->searchable()->filterable(),
            Column::raw("REGEXP_SUBSTR(dstchannel, '(?<=/)[0-9]+(?=-)') AS extension")
                ->label('Extension')
                ->filterable(),
            Column::name('au_user.fname')->label('Agent')->filterable()->searchable(),
            // Column::name('lastapp')->label('LAPP')->searchable()->filterable(), //for testing purpose only
            NumberColumn::name('billsec')->label('Bill Sec')->filterable()->hide(),
            Column::raw('SEC_TO_TIME(billsec)')->label('Bill Sec Duration')->filterable(),
            Column::name('disposition')->label('Disposition')->filterable($this->dispositions)->hide(),
            // Column::callback(['lastapp'], function ($lastapp) {
            //     return $lastapp == 'Dial' ? 'Out' : 'In';
            // })->label('Direction')->filterable(['Out', 'In']),
            // Column::callback(['id', 'uniqueid'], function ($id, $uniqueid) {
            //     return view('table-actions-v2', ['id' => $id, 'uniqueid' => $uniqueid]);
            // })->unsortable()->excludeFromExport()
        ];
    }

    public function getDispositionsProperty()
    {
        return Cdr::distinct('disposition')->pluck('disposition');
    }

    // public function download($id)
    // {
    //     // dd($id);
    //     // $file = Storage::disk('asterisk-media-server')->url("$id.wav");
    //     /* $file = Storage::temporaryUrl(
    //         "$id.wav", now()->addMinutes(5)
    //     );
    //     dd($file); */
    //     return Storage::disk('asterisk-media-server')->download("$id.wav");
    // }

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
        $export->setFilename('agent_missed_call-summary_' . Carbon::now()->format('Ymdhis') . '.csv');

        return $export->download();
    }
}
