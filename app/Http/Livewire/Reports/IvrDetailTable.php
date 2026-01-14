<?php

namespace App\Http\Livewire\Reports;

// use Livewire\Component;
use App\Models\AuIvrCall;
use App\Models\Cdr;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Mediconesystems\LivewireDatatables\Column;
use Mediconesystems\LivewireDatatables\DateColumn;
use Mediconesystems\LivewireDatatables\Exports\DatatableExport;
use Mediconesystems\LivewireDatatables\Http\Livewire\LivewireDatatable;
use Mediconesystems\LivewireDatatables\NumberColumn;

class IvrDetailTable extends LivewireDatatable
{

    public $hideable = 'select';
    public $exportable = true;
    public function builder()
    {
        return AuIvrCall::query()
            ->leftJoin('cdr', 'au_ivr_calls.uniqueid', '=', 'cdr.uniqueid');
    }

    public function columns()
    {
        //id									created_at	updated_at
        $dateColum = DateColumn::name('au_ivr_calls.date')->label('Call At')->format('Y-m-d H:i:s')->filterable()->sortBy('au_ivr_calls.date')->defaultSort('desc');
        $dateColum->sortable = true;

        return [
            Column::name('au_ivr_calls.uniqueid')->label('Call #')->filterable()->hide(),
            $dateColum,
            Column::name('au_ivr_calls.ani')->label('Source')->searchable()->filterable(),
            Column::name('au_ivr_calls.dnis')->label('Destination')->searchable()->filterable(),
            Column::name('au_ivr_calls.ivr')->label('IVR')->searchable()->filterable(),
            // Column::name('lastapp')->label('LAPP')->searchable()->filterable(), //for testing purpose only
            NumberColumn::name('cdr.billsec')->label('Bill Sec')->filterable()->hide(),
            Column::raw('SEC_TO_TIME(cdr.billsec)')->label('Bill Sec Duration')->filterable(),
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

    public function download($id)
    {
        // dd($id);
        // $file = Storage::disk('asterisk-media-server')->url("$id.wav");
        /* $file = Storage::temporaryUrl(
            "$id.wav", now()->addMinutes(5)
        );
        dd($file); */
        return Storage::disk('asterisk-media-server')->download("$id.wav");
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
        $export->setFilename('ivr_detail_report_' . Carbon::now()->format('Ymdhis') . '.csv');

        return $export->download();
    }
}
