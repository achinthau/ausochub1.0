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
            ->where(function ($query) {
                $query->whereIn('uniqueid', function ($q) {
                    $q->select('uniqueid')
                      ->from('au_callcount_report')
                      ->where('customer_reaction', 2);
                })
                ->orWhereIn('uniqueid', function ($q) {
                    $q->select('uniqueid')
                      ->from('au_queuecount_report')
                      ->where('customer_reaction', 2);
                });
            });
    }

    public function columns()
    {
        $dateColumn = DateColumn::name('calldate')
            ->label('Call Date')
            ->format('Y-m-d H:i:s')
            ->filterable()
            ->sortBy('calldate')
            ->defaultSort('desc');

        $dateColumn->sortable = true;

        return [
            Column::name('id')->label('ID')->filterable()->hide(),
            $dateColumn,
            Column::name('src')->label('Source')->searchable()->filterable(),
            Column::name('dst')->label('Destination')->searchable()->filterable(),
            Column::name('dcontext')->label('DContext')->filterable(),
            Column::name('channel')->label('Channel')->filterable(),
            Column::name('duration')->label('Duration')->filterable(),
            Column::name('billsec')->label('Bill Sec')->filterable(),
            Column::name('disposition')->label('Disposition')->filterable(),

            Column::callback(['id', 'uniqueid'], function ($id, $uniqueid) {
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
