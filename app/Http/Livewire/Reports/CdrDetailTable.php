<?php

namespace App\Http\Livewire\Reports;

use App\Models\Cdr;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Mediconesystems\LivewireDatatables\Column;
use Mediconesystems\LivewireDatatables\DateColumn;
use Mediconesystems\LivewireDatatables\Exports\DatatableExport;
use Mediconesystems\LivewireDatatables\Http\Livewire\LivewireDatatable;
use Mediconesystems\LivewireDatatables\NumberColumn;
use Mediconesystems\LivewireDatatables\TimeColumn;

class CdrDetailTable extends LivewireDatatable
{

    public $hideable = 'select';
    public $exportable = true;
    public function builder()
    {
        return Cdr::query()
        // only for get extention
            ->leftJoin('au_queuecount_report', function ($join) {
                $join->on('cdr.uniqueid', '=', 'au_queuecount_report.uniqueid')
                    ->where('au_queuecount_report.status', '=', 2);
            })
        // to filter
            ->whereIn('lastapp', ['Dial', 'Queue'])->whereNotNull('src')->where('src', '<>', '');
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
            Column::name('uniqueid')->label('Call #')->filterable()->hide(),
            $dateColum,
            Column::name('src')->label('Source')->searchable()->filterable(),
            Column::name('dst')->label('Destination')->searchable()->filterable(),
            NumberColumn::name('billsec')->label('Bill Sec')->filterable()->hide(),
            Column::raw('SEC_TO_TIME(billsec)')->label('Bill Sec Duration')->filterable(),
            Column::name('disposition')->label('Disposition')->filterable($this->dispositions),
            // Column::name('au_queuecount_report.agent')->label('Extension')->filterable(),
            Column::callback(['lastapp', 'channel', 'dstchannel'], function ($lastapp, $channel, $dstchannel) {
                $raw = $lastapp === 'Queue' ? $dstchannel : ($lastapp === 'Dial' ? $channel : null);

                if ($raw && preg_match('/\/(\d+)-/', $raw, $matches)) {
                    return $matches[1]; 
                }

                return;
            })->label('Extension')->filterable(),

            Column::callback(['lastapp'], function ($lastapp) {
                return $lastapp == 'Dial' ? 'Out' : 'In';
            })->label('Direction')->filterable(['Dial' => 'Out', 'Queue' => 'In']),
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
        $this->activeDateFilters[$index]['end'] = $end == "" ? $end : $end . " 23:59:59";
        ;
        $this->page = 1;
        $this->setSessionStoredFilters();
    }

    public function export(string $filename = 'DatatableExport.xlsx')
    {
        $this->forgetComputed();

        $export = new DatatableExport($this->getExportResultsSet());
        $export->setFilename('cdr_detail_report_' . Carbon::now()->format('Ymdhis') . '.csv');

        return $export->download();
    }
}
