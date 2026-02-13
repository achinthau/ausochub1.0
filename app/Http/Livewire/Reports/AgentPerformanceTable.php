<?php

namespace App\Http\Livewire\Reports;

use App\Models\QueueEvent;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Mediconesystems\LivewireDatatables\Column;
use Mediconesystems\LivewireDatatables\DateColumn;
use Mediconesystems\LivewireDatatables\Exports\DatatableExport;
use Mediconesystems\LivewireDatatables\Http\Livewire\LivewireDatatable;
use Mediconesystems\LivewireDatatables\NumberColumn;

class AgentPerformanceTable extends LivewireDatatable
{
    public $hideable = 'select';
    public $exportable = true;
    public $activeDatetimeFilters = [];

    public function builder()
    {
       
            return QueueEvent::query()
            ->where('event_name', 'AgentComplete');
            

    }

    public function columns()
    {
        $dateColumn = DateColumn::name('created_at')
            ->label('Call Date')
            ->format('Y-m-d H:i:s')
            ->filterable()
            ->sortBy('created_at')
            ->defaultSort('desc');

        $dateColumn->sortable = true;

        return [
            Column::name('id')->label('ID')->filterable()->hide(),
            $dateColumn,
            Column::name('contact')->label('Contact')->searchable()->filterable(),
            Column::name('queue_name')->label('Queue Name')->searchable()->filterable(),
            Column::name('extension')->label('Extension')->filterable(),
            Column::name('hold_time')->label('Hold Time')->filterable(),
            Column::name('ring_time')->label('Ring Time')->filterable(),
            Column::name('talk_time')->label('Talk Time')->filterable(),
            Column::name('disconnected_by')->label('Disconnected By')->filterable(),
            
        ];
    }


    public function doDatetimeFilterStart($index, $start)
    {
        $this->activeDatetimeFilters[$index]['start'] = $start;
        $this->activeDateFilters[$index]['start'] = $start ? str_replace('T', ' ', $start) : null;
        $this->page = 1;
        $this->setSessionStoredFilters();
    }

    public function doDatetimeFilterEnd($index, $end)
    {
        $this->activeDatetimeFilters[$index]['end'] = $end;
        $this->activeDateFilters[$index]['end'] = $end ? str_replace('T', ' ', $end) : null;
        $this->page = 1;
        $this->setSessionStoredFilters();
    }

    public function export(string $filename = 'DatatableExport.xlsx')
    {
        $this->forgetComputed();

        $export = new DatatableExport($this->getExportResultsSet());
        $export->setFilename('agent_performance_' . Carbon::now()->format('Ymdhis') . '.csv');

        return $export->download();
    }
}
