<?php

namespace App\Http\Livewire\Tables;

use App\Models\Lead;
use App\Models\LeadStatus;
use Mediconesystems\LivewireDatatables\Http\Livewire\LivewireDatatable;
use Mediconesystems\LivewireDatatables\Column;
use Mediconesystems\LivewireDatatables\DateColumn;

class LeadsTable extends LivewireDatatable
{
    public $hideable = 'select';
    public $exportable = true;

    public function builder()
    {
        return Lead::with('status')->relavant();
    }

    public function columns()
    {
        return [
            Column::name('id')->linkTo('leads', 6),
            Column::name('contact_number')->searchable()->filterable(),
            DateColumn::name('created_at')->label('called at')->filterable(),
            Column::name('first_name')->searchable()->filterable(),
            Column::name('last_name')->searchable()->filterable(),
            Column::name('status.title')->filterable(),
            Column::name('nic')->filterable(),
            Column::name('email')->filterable(),
            Column::name('address_line_1')->label('House or Apartment #')->filterable()->hide(),
            Column::name('address_line_2')->label('Street')->filterable()->hide(),
            Column::name('city')->filterable(),
            Column::name('contact_number_2')->filterable(),


        ];
    }
}
