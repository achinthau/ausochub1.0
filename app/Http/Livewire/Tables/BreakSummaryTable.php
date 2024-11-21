<?php

namespace App\Http\Livewire\Tables;

use App\Models\Agent;
use App\Models\AgentBreakSummary;
use Carbon\Carbon;
use Mediconesystems\LivewireDatatables\BooleanColumn;
use Mediconesystems\LivewireDatatables\Column;
use Mediconesystems\LivewireDatatables\DateColumn;
use Mediconesystems\LivewireDatatables\Exports\DatatableExport;
use Mediconesystems\LivewireDatatables\Http\Livewire\LivewireDatatable;

class BreakSummaryTable extends LivewireDatatable
{
    public $hideable = 'select';
    public $exportable = true;

    public function builder()
    {
        return AgentBreakSummary::join('au_user', 'au_user.id', 'au_agentbreak_summery.agentid')->orderBy('breaktime', 'DESC');
    }

    public function columns()
    {
        return [
            Column::name('agent.username')->filterable(Agent::all()->pluck('username')->toArray()),
            BooleanColumn::name('agent.status')->filterable()->hide(),
            DateColumn::name('breaktime')->filterable(),
            DateColumn::name('unbreaktime')->filterable(),
            Column::raw(" CONCAT(
        FLOOR(TIME_TO_SEC(TIMEDIFF( `unbreaktime`,`breaktime`)) / 3600), ':',
        FLOOR((TIME_TO_SEC(TIMEDIFF( `unbreaktime`,`breaktime`)) % 3600) / 60), ':',
        TIME_TO_SEC(TIMEDIFF( `unbreaktime`,`breaktime`)) % 60, ''
    )  AS duration")->label('Duration'),
            Column::name('desc')->filterable(),
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
        $export->setFilename('agent_break_summary_' . Carbon::now()->format('Ymdhis') . '.csv');

        return $export->download();
    }
}
