<?php

namespace App\Http\Livewire\Tables\Settings;

use App\Models\CrmDepartment;
use Mediconesystems\LivewireDatatables\Column;
use Mediconesystems\LivewireDatatables\Http\Livewire\LivewireDatatable;
use Mediconesystems\LivewireDatatables\Action;
use Mediconesystems\LivewireDatatables\Exports\DatatableExport;



class CrmDepartmentTable extends LivewireDatatable
{
    public $hideable = 'select';
    
    public $table = 'crm_departments';
    // public $exportable = true;

    public function builder()
    {
        return CrmDepartment::query();
    }


    public function columns()
    {
        return [
            Column::name('id')->filterable(),
            Column::name('name')->filterable(),
            Column::callback(['id'], function ($id) {
                return view('table-actions-crm-departments', ['id' => $id]);
            })->unsortable(),
               			
        ];
    }

}