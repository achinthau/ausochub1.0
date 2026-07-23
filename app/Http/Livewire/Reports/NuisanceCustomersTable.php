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

class NuisanceCustomersTable extends LivewireDatatable
{
    public $hideable = 'select';
    public $exportable = true;

    public function builder()
    {
        return Cdr::query()
            ->select('cdr.*')
            ->selectRaw("CASE WHEN cdr.lastapp = 'Queue' THEN au_queuecount_report.agent WHEN cdr.lastapp = 'Dial' THEN au_callcount_report.ani ELSE '' END as extension")
            ->leftJoin('au_queuecount_report', function ($join) {
                $join->on('cdr.uniqueid', '=', 'au_queuecount_report.uniqueid')
                    ->where('au_queuecount_report.customer_reaction', '=', 2);
            })
            ->leftJoin('au_callcount_report', function ($join) {
                $join->on('cdr.uniqueid', '=', 'au_callcount_report.uniqueid')
                    ->where('au_callcount_report.customer_reaction', '=', 2)
                    ->where('au_callcount_report.direction', '=', 'out');
            })
            ->whereIn('lastapp', ['Dial', 'Queue'])
            ->where(function ($query) {
                $query->where(function ($queueQuery) {
                    $queueQuery->where('lastapp', 'Queue')
                        ->whereNotNull('au_queuecount_report.uniqueid');
                })->orWhere(function ($dialQuery) {
                    $dialQuery->where('lastapp', 'Dial')
                        ->whereNotNull('au_callcount_report.uniqueid');
                });
            })
            ->distinct();

    }

    public function columns()
    {
        $dateColumn = DateColumn::name('calldate')
            ->label('Call Date')
            ->format('Y-m-d H:i:s')
            ->filterable()
            ->sortBy('cdr.calldate')
            ->defaultSort('desc');

        $dateColumn->sortable = true;

        return [
            Column::name('cdr.id')->label('ID')->filterable()->hide(),
            $dateColumn,
            Column::name('cdr.src')->label('Source')->searchable()->filterable(),
            Column::name('cdr.dst')->label('Destination')->searchable()->filterable(),
            Column::name('cdr.dcontext')->label('DContext')->filterable(),
            // Column::name('channel')->label('Channel')->filterable(),
            // Column::name('duration')->label('Duration')->filterable(),
            Column::name('cdr.billsec')->label('Bill Sec')->filterable(),
            // Column::name('disposition')->label('Disposition')->filterable(),
            // Column::name('au_queuecount_report.agent')->label('Extension')->filterable(),
            // Column::name('au_callcount_report.ani')->label('Extension')->filterable(),
            Column::name('extension')->label('Extension')->sortable()->searchable(),

            Column::callback(['cdr.id', 'cdr.uniqueid'], function ($id, $uniqueid) {
                return view('table-actions-v2', ['id' => $id, 'uniqueid' => $uniqueid]);
            })->unsortable()->excludeFromExport(),
        ];
    }

    public function download($id)
    {
        return Storage::disk('asterisk-media-server')->download("$id.wav");
    }

    public function export(string $filename = 'DatatableExport.xlsx')
    {
        $this->forgetComputed();

        $export = new DatatableExport($this->getExportResultsSet());
        $export->setFilename('nuisance_customer_' . Carbon::now()->format('Ymdhis') . '.csv');

        return $export->download();
    }
}
