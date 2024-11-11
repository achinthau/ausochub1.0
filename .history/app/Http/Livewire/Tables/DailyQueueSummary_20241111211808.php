<?php

namespace App\Http\Livewire\Tables;

use App\DailyQueueSummery;
use Mediconesystems\LivewireDatatables\Http\Livewire\LivewireDatatable;

class DailyQueueSummary extends LivewireDatatable
{
    public $model = DailyQueueSummery::class;

    public function columns()
    {
        //
    }
}