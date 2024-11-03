<?php

namespace App\Http\Livewire\Tables\Settings;

use App\Models\MohClass;
use Illuminate\Support\Facades\Storage;
use Mediconesystems\LivewireDatatables\Column;
use Mediconesystems\LivewireDatatables\Http\Livewire\LivewireDatatable;

class MohClassTable extends LivewireDatatable
{
    public function builder()
    {
        return MohClass::query();
    }

    public function columns()
    {
        return [
            Column::name('moh_class'),
            Column::callback(['id','moh_class'], function ($id,$moh_class) {
                return view('table-actions.moh-classes-table', ['id' => $id,'moh_class'=>$moh_class]);
            })->unsortable()   			
        ];
    }

    public function download($fileName)
    {
            return Storage::disk('moh-file-server')->download($fileName);
    }
}