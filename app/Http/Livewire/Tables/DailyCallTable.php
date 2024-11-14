<?php

namespace App\Http\Livewire\Tables;

use App\Models\DailyCallSummary;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Mediconesystems\LivewireDatatables\DateColumn;
use Mediconesystems\LivewireDatatables\NumberColumn;
use Mediconesystems\LivewireDatatables\Column;
use Mediconesystems\LivewireDatatables\Exports\DatatableExport;
use Mediconesystems\LivewireDatatables\Http\Livewire\LivewireDatatable;

class DailyCallTable extends LivewireDatatable
{
    public $hideable = 'select';
    public $exportable = true;



    public function builder()
    {
        // Return a query for fetching data from the daily_queue_summeries table
        return DailyCallSummary::query();
    }

    public function columns()
    {
        return [
            Column::name('id')
                ->label('ID')
                ->sortable()
                ->searchable(),

            DateColumn::name('date')
                ->label('Date')
                ->sortable()
                ->filterable(),

            NumberColumn::name('inbound')
                ->label('Inbound Calls')
                ->sortable(),

            NumberColumn::name('outbound')
                ->label('Outbound Calls')
                ->sortable(),

            NumberColumn::name('queued')
                ->label('Queued Calls')
                ->sortable(),

            NumberColumn::name('abandent')
                ->label('Abandoned Calls')
                ->sortable(),

            NumberColumn::name('answered')
                ->label('Answered Calls')
                ->sortable(),
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
        $export->setFilename('daily_call_summary_report_' . Carbon::now()->format('Ymdhis') . '.csv');

        return $export->download();
    }

   /*  public function addSort()
    {
        if (isset($this->sort) && isset($this->freshColumns[$this->sort]) && $this->freshColumns[$this->sort]['name']) {
            if (isset($this->pinnedRecords) && $this->pinnedRecords) {
                $this->query->orderBy(DB::raw('FIELD(id,' . implode(',', $this->pinnedRecords) . ')'), 'DESC');
            }
            // $this->query->orderBy(DB::raw($this->getSortString($this->query->getConnection()->getPDO()->getAttribute(\PDO::ATTR_DRIVER_NAME))), $this->direction ? 'asc' : 'desc');
            $this->query->orderBy(
                DB::raw($this->tablePrefix . $this->getSortString($this->query->getConnection()->getPDO()->getAttribute(\PDO::ATTR_DRIVER_NAME))),
                $this->direction ? 'asc' : 'desc'
            );
        }

        return $this;
    } */
}
