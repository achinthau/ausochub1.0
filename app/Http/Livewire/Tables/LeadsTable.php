<?php

namespace App\Http\Livewire\Tables;

use App\Models\Lead;
use App\Models\LeadStatus;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Mediconesystems\LivewireDatatables\Http\Livewire\LivewireDatatable;
use Mediconesystems\LivewireDatatables\Column;
use Mediconesystems\LivewireDatatables\DateColumn;
use Mediconesystems\LivewireDatatables\Exports\DatatableExport;

class LeadsTable extends LivewireDatatable
{
    public $hideable = 'select';
    public $exportable = true;

    public function builder()
    {

        $this->exportable = Gate::allows('can-export-ticket');
        return Lead::with('status')->relavant();
    }

    public function columns()
    {
        return [
            // Test Nipuna 
            Column::name('id')->linkTo('leads', 6),
            Column::name('contact_number')->searchable()->filterable(),
            DateColumn::name('created_at')->label('called at'),
            Column::name('first_name')->searchable()->filterable(),
            Column::name('last_name')->searchable()->filterable(),
            // Column::name('status.title')->filterable(),
            Column::name('nic')->filterable(),
            Column::name('email')->filterable(),
            Column::name('address_line_1')->label('House or Apartment #')->filterable()->hide(),
            Column::name('address_line_2')->label('Street')->filterable()->hide(),
            Column::name('city')->filterable(),
            Column::name('contact_number_2')->filterable(),


        ];
    }

    public function addSort()
    {
        if (isset($this->sort) && isset($this->freshColumns[$this->sort]) && $this->freshColumns[$this->sort]['name']) {
            if (isset($this->pinnedRecords) && $this->pinnedRecords) {
                $this->query->orderBy(DB::raw('FIELD(id,' . implode(',', $this->pinnedRecords) . ')'), 'DESC');
            }
            // dd((string)DB::raw($this->getSortString($this->query->getConnection()->getPDO()->getAttribute(\PDO::ATTR_DRIVER_NAME))));
            if (str_contains((string)DB::raw($this->getSortString($this->query->getConnection()->getPDO()->getAttribute(\PDO::ATTR_DRIVER_NAME))), ".")) {
                // $this->query->orderBy($this->tablePrefix.DB::raw($this->getSortString($this->query->getConnection()->getPDO()->getAttribute(\PDO::ATTR_DRIVER_NAME))), $this->direction ? 'asc' : 'desc');
            } else {


                $this->query->orderBy(DB::raw($this->getSortString($this->query->getConnection()->getPDO()->getAttribute(\PDO::ATTR_DRIVER_NAME))), $this->direction ? 'asc' : 'desc');
            }
        }

        return $this;
    }

    public function export(string $filename = 'DatatableExport.xlsx')
    {
        $this->forgetComputed();

        $export = new DatatableExport($this->getExportResultsSet());
        $export->setFilename('leads_' . Carbon::now()->format('Ymdhis') . '.csv');

        return $export->download();
    }
}
