<?php

namespace App\Http\Livewire\Tables;

use App\Models\Abandoned;
use App\Models\CallCenter\AbandonedCall;
use Carbon\Carbon;
use Mediconesystems\LivewireDatatables\BooleanColumn;
use Mediconesystems\LivewireDatatables\Column;
use Mediconesystems\LivewireDatatables\DateColumn;
use Mediconesystems\LivewireDatatables\Exports\DatatableExport;
use Mediconesystems\LivewireDatatables\Http\Livewire\LivewireDatatable;
use Mediconesystems\LivewireDatatables\TimeColumn;

class AbandonedCallTable extends LivewireDatatable
{

    public $hideable = 'select';
    public $exportable = true;

    public function builder()
    {
        return AbandonedCall::orderBy('called_at', 'DESC');
    }

    public function columns()
    {
        return [
            Column::name('ani')->label('From')->filterable(),
            Column::name('queuename')->label('Queue')->filterable($this->statues),
            DateColumn::name('called_at')->label('Called At')->filterable()->sortable(),
            BooleanColumn::name('recalled_status')->label('Recalled')->filterable(),
            DateColumn::name('recalled_at')->label('Recalled At')->filterable()->sortable(),
        ];
    }

    public function getStatuesProperty()
    {
        return array_unique(AbandonedCall::all()->pluck('queuename')->toArray());
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
        $export->setFilename('abandoned_call_report_' . Carbon::now()->format('Ymdhis') . '.csv');

        return $export->download();
    }
}
