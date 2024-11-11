<?php

namespace App\Http\Livewire\Tables;

use App\DailyCallSummary;
use Mediconesystems\LivewireDatatables\Http\Livewire\LivewireDatatable;

class DailyCallTable extends LivewireDatatable
{
    public $model = DailyCallSummary::class;

    public function columns()
    {
        //
    }
}