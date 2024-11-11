<?php

namespace App\Http\Livewire\Tables;

use App\DailyCallSummary;
use App\Models\DailyCallSummary as ModelsDailyCallSummary;
use Mediconesystems\LivewireDatatables\Http\Livewire\LivewireDatatable;

class DailyCallSummary extends LivewireDatatable
{
    public $model = ModelsDailyCallSummary::class;

    public function columns()
    {
        //
    }
}