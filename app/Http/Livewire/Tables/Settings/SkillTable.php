<?php

namespace App\Http\Livewire\Tables\Settings;

use App\Models\Skill;
use Mediconesystems\LivewireDatatables\Column;
use Mediconesystems\LivewireDatatables\Http\Livewire\LivewireDatatable;

class SkillTable extends LivewireDatatable
{
    public function builder()
    {
        return Skill::query();
    }

    public function columns()
    {
        return [
            Column::name('skillid'),
            Column::name('skillname'),
            Column::name('moh_classes'),
        ];
    }
}