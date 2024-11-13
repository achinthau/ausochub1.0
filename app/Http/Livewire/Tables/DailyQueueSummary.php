<?php

namespace App\Http\Livewire\Tables;

use App\Models\DailyQueueSummary as ModelsDailyQueueSummary;
use App\Models\DailyQueueSummery;  // Correct model used
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Mediconesystems\LivewireDatatables\Action;
use Mediconesystems\LivewireDatatables\Column;
use Mediconesystems\LivewireDatatables\DateColumn;
use Mediconesystems\LivewireDatatables\Exports\DatatableExport;
use Mediconesystems\LivewireDatatables\NumberColumn;
use Mediconesystems\LivewireDatatables\Http\Livewire\LivewireDatatable;

class DailyQueueSummary extends LivewireDatatable
{
    public $exportable = true;

    public function builder()
    {
        // Return a query for fetching data from the daily_queue_summeries table
        return ModelsDailyQueueSummary::query();
    }

    public function columns()
    {
        return [
            // ID column (optional, for debugging purposes)
            NumberColumn::name('id')
                ->label('ID'),
            // Customize the link as needed

            // Date column for daily summary
            DateColumn::name('date')
                ->label('Date')
                ->sortable()
                ->filterable(),

            // Queue column
            Column::name('queue')
                ->label('Queue')
                ->searchable(),
            // ->filterable(),

            // Calls column (number of calls)
            NumberColumn::name('calls')
                ->label('Calls')
                ->sortable(),
            // ->filterable(),

            // Answered column (number of answered calls)
            NumberColumn::name('answered')
                ->label('Answered')
                ->sortable(),
            // ->filterable(),

            // Abandoned column (number of abandoned calls)
            NumberColumn::name('abandoned')
                ->label('Abandoned')
                ->sortable(),
            // ->filterable(),

            // Agents column (number of agents in the queue)
            NumberColumn::name('agents')
                ->label('Agents')
                ->sortable(),
            // ->filterable(),

            // Separate summary column for "Total Calls" (no duplication, summary applied here)
            // NumberColumn::name('calls')
            //     ->label('Total Calls')
            //     ->enableSummary(),  // This enables a summary for the "calls" column (sum)

            // Optional: Add a custom label or computed column (e.g., call efficiency)

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
        $export->setFilename('daily_queue_summary_report_' . Carbon::now()->format('Ymdhis') . '.csv');

        return $export->download();
    }


    public function addSort()
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
    }
}
