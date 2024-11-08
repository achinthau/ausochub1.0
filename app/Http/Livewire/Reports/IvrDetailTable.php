<?php

namespace App\Http\Livewire\Reports;

// use Livewire\Component;
use App\Models\Cdr;
use Illuminate\Support\Facades\Storage;
use Mediconesystems\LivewireDatatables\Column;
use Mediconesystems\LivewireDatatables\DateColumn;
use Mediconesystems\LivewireDatatables\Http\Livewire\LivewireDatatable;
use Mediconesystems\LivewireDatatables\NumberColumn;

class IvrDetailTable extends LivewireDatatable
{

    public $hideable = 'select';
    public $exportable = true;
    public function builder()
    {
        return Cdr::query()
        ->whereIn('lastapp', ['AGI']);
    }

    public function columns()
    {
        //id									created_at	updated_at
        $dateColum = DateColumn::name('calldate')->label('Call At')->format('Y-m-d H:i:s')->filterable()->sortBy('calldate')->defaultSort('desc');
        $dateColum->sortable = true;

        return [
            Column::name('uniqueid')->label('Call #')->filterable()->hide(),
            $dateColum,
            Column::name('src')->label('Source')->searchable()->filterable(),
            Column::name('dst')->label('Destination')->searchable()->filterable(),
            // Column::name('lastapp')->label('LAPP')->searchable()->filterable(), //for testing purpose only
            NumberColumn::name('billsec')->label('Bill Sec')->filterable()->hide(),
            Column::raw('SEC_TO_TIME(billsec)')->label('Bill Sec Duration')->filterable(),
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
}
