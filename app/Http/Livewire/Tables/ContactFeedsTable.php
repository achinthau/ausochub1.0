<?php

namespace App\Http\Livewire\Tables;

use App\Models\ContactFeed;
use Illuminate\Support\Facades\Storage;
use Mediconesystems\LivewireDatatables\Column;
use Mediconesystems\LivewireDatatables\DateColumn;
use Mediconesystems\LivewireDatatables\Http\Livewire\LivewireDatatable;

class ContactFeedsTable extends LivewireDatatable
{

    public $hideable = 'select';
    public $exportable = true;
    
    public function builder()
    {
        return ContactFeed::query();
    }

    public function columns()
    {
        return [
            Column::name('id')->filterable(),
            Column::name('title')->filterable(),
            Column::name('description')->filterable(),
            DateColumn::name('created_at')->filterable(),
            Column::callback(['id', 'file_name'], function ($id, $file_name) {
                return view('table-actions', ['id' => $id, 'file_name' => $file_name]);
            })->unsortable()
        ];
    }

    public function download($id, $file_name)
    {
        return Storage::download('app/contact_feeds/'.$file_name);
    }
}
