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

class UnsatisfiedCustomersTable extends LivewireDatatable
{
    public $hideable = 'select';
    public $exportable = true;

    public function builder()
    {
        // return Cdr::query()
        //     ->where(function ($query) {
        //         $query->whereIn('uniqueid', function ($q) {
        //             $q->select('uniqueid')
        //               ->from('au_callcount_report')
        //               ->where('customer_reaction', 1);
        //         })
        //         ->orWhereIn('uniqueid', function ($q) {
        //             $q->select('uniqueid')
        //               ->from('au_queuecount_report')
        //               ->where('customer_reaction', 1);
        //         });
        //     });

        return Cdr::query()
    ->leftJoin('au_queuecount_report', function ($join) {
        $join->on('cdr.uniqueid', '=', 'au_queuecount_report.uniqueid')
             ->where('au_queuecount_report.customer_reaction', '=', 1);
    })
    ->leftJoin('au_callcount_report', function ($join) {
        $join->on('cdr.uniqueid', '=', 'au_callcount_report.uniqueid')
             ->where('au_callcount_report.customer_reaction', '=', 1)
             ->where('au_callcount_report.direction', '=', 'out');
    })
    ->where(function ($query) {
        $query->whereNotNull('au_queuecount_report.uniqueid')
              ->orWhereNotNull('au_callcount_report.uniqueid');
    });
    }

    public function columns()
    {
        return [
            Column::name('id')->label('ID')->filterable(),
            DateColumn::name('calldate')
                ->label('Call Date')
                ->format('Y-m-d H:i:s')
                ->filterable()
                ->defaultSort('desc'),
            Column::name('src')->label('Source')->filterable()->searchable(),
            // Column::name('dst')->label('Destination')->filterable()->searchable(),
            Column::name('dcontext')->label('DContext')->filterable(),
            // Column::name('channel')->label('Channel')->filterable(),
            // NumberColumn::name('duration')->label('Duration')->filterable(),
            NumberColumn::name('billsec')->label('BillSec')->filterable(),
            // Column::name('disposition')->label('Disposition')->filterable(),
            Column::callback(['lastapp', 'au_queuecount_report.agent', 'au_callcount_report.ani'], function ($lastapp, $agent, $ani) {
    return $lastapp == 'Queue' ? $agent : ('Dial' ? $ani :'');
})->label('Extension')->sortable()->searchable(),

            Column::callback(['id', 'uniqueid'], function ($id, $uniqueid) {
                return view('table-actions-v2', ['id' => $id, 'uniqueid' => $uniqueid]);
            })->unsortable()->excludeFromExport(),
        ];
    }

    public function download($id)
    {
        return Storage::disk('asterisk-media-server')->download("$id.wav");
    }

    public function export(string $filename = 'unsatisfied_customer.xlsx')
    {
        $this->forgetComputed();

        $export = new DatatableExport($this->getExportResultsSet());
        $export->setFilename('unsatisfied_customer_' . Carbon::now()->format('Ymdhis') . '.csv');

        return $export->download();
    }
}
