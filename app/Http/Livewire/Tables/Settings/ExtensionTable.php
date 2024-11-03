<?php

namespace App\Http\Livewire\Tables\Settings;

use App\Models\Extension;
use Mediconesystems\LivewireDatatables\Column;
use Mediconesystems\LivewireDatatables\Http\Livewire\LivewireDatatable;

class ExtensionTable extends LivewireDatatable
{
    public $hideable = 'select';
    public $exportable = true;

    public function builder()
    {
        return Extension::query();
    }

    public function columns()
    {
        return [
            Column::name('extension')->filterable(),
            Column::name('exten_type')->filterable(['sip','iax']),
            Column::name('password')->filterable(),
            Column::name('context')->filterable(), 
            Column::callback(['id'], function ($id) {
                return view('table-actions-extension', ['id' => $id]);
            })->unsortable()   			
        ];
    }
}