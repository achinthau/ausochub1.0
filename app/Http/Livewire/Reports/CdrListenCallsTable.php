<?php

namespace App\Http\Livewire\Reports;

use App\Models\Cdr;
use Illuminate\Support\Facades\Storage;
use Mediconesystems\LivewireDatatables\Column;
use Mediconesystems\LivewireDatatables\DateColumn;
use Mediconesystems\LivewireDatatables\Http\Livewire\LivewireDatatable;
use Mediconesystems\LivewireDatatables\NumberColumn;
use Mediconesystems\LivewireDatatables\TimeColumn;

class CdrListenCallsTable extends LivewireDatatable
{

    public $hideable = 'select';
    public $exportable = true;
    public function builder()
    {
        return Cdr::query()
        ->where(function ($query) {
            $query->where('src', 'like', 'Barge_%')
                  ->orWhere('src', 'like', 'Whisper_%')
                  ->orWhere('src', 'like', 'Listen_%');
        });
    }

    public function columns()
    {
        //id									created_at	updated_at
        $dateColum = DateColumn::name('calldate')
            ->label('Call At')
            ->format('Y-m-d H:i:s')
            ->filterable()
            ->sortBy('calldate')
            ->defaultSort('desc');


        $dateColum->sortable = true;

        return [
            $dateColum,
            Column::callback(['src'], function ($src) {
                return explode('_', $src)[0];
            })->label('Action')->filterable(),
            Column::raw("RIGHT(dst, 3) AS agent_id")->label('Agent')->filterable(),
            Column::raw("IF(channel LIKE 'SIP%',REGEXP_SUBSTR(channel, '(?<=\/).*(?=-)'),channel)")->label('Supervisor')->filterable(),
            Column::callback(['id', 'uniqueid'], function ($id, $uniqueid) {
                return view('table-actions-v2', ['id' => $id, 'uniqueid' => $uniqueid]);
            })->unsortable()->excludeFromExport()
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
