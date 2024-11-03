<?php

namespace App\Http\Livewire\Tables;

use App\Models\Cdr;
use Illuminate\Support\Facades\Storage;
use Mediconesystems\LivewireDatatables\Column;
use Mediconesystems\LivewireDatatables\DateColumn;
use Mediconesystems\LivewireDatatables\Http\Livewire\LivewireDatatable;
use Mediconesystems\LivewireDatatables\NumberColumn;

class CallDetailTable extends LivewireDatatable
{

    public $hideable = 'select';
    public $exportable = true;
    
    public function builder()
    {
        return Cdr::orderBy('calldate','DESC');
    }

    public function columns()
    {
        return [
            DateColumn::name('calldate')->filterable(),
            Column::name('src')->label('From')->filterable(),
            Column::raw("IF(dstchannel LIKE 'SIP%',REGEXP_SUBSTR(dstchannel, '(?<=\/).*(?=-)'),dstchannel)")->label('Extension')->filterable(),
            Column::name('disposition')->label('Status')->filterable($this->statues),
            NumberColumn::name('duration')->label('Duration')->filterable(),
            /* Column::callback('uniqueid', function ($uniqueid) {
                return view('table-actions-play', ['id' => $uniqueid]);
            })->unsortable()->excludeFromExport() */
        ];
    }


    public function getStatuesProperty()
    {
        return array_unique(Cdr::all()->pluck('disposition')->toArray());
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
}
