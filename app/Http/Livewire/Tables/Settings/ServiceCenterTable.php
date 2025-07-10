<?php

namespace App\Http\Livewire\Tables\Settings;

use App\Models\CxTicketServCenter;
use Mediconesystems\LivewireDatatables\Column;
use Mediconesystems\LivewireDatatables\Http\Livewire\LivewireDatatable;
use Mediconesystems\LivewireDatatables\Action;
use Mediconesystems\LivewireDatatables\Exports\DatatableExport;



class ServiceCenterTable extends LivewireDatatable
{
    public $hideable = 'select';
    
    public $table = 'crm_departments';
    // public $exportable = true;

    public function builder()
    {
        return CxTicketServCenter::query();
    }


    public function columns()
    {
        return [
            Column::name('id')->filterable(),
            Column::name('name')->filterable(),
            Column::callback(['id'], function ($id) {
                return view('table-actions-serv-centers', ['id' => $id]);
            })->unsortable() ,
              			
        ];
    }

}