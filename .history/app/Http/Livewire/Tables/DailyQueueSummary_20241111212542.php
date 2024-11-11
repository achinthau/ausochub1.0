<?php

namespace App\Http\Livewire\Tables;

use App\DailyQueueSummery;
use App\Models\DailyQueueSummery as ModelsDailyQueueSummery;
use Mediconesystems\LivewireDatatables\Http\Livewire\LivewireDatatable;

class DailyQueueSummary extends LivewireDatatable
{
    public $model = ModelsDailyQueueSummery::class;

    public function builder()
    {
        return ModelsDailyQueueSummery::query();
    }

    public function columns()
    {
        return [
            NumberColumn::name('id')
                ->label('ID')
                ->linkTo('job', 6),

            BooleanColumn::name('email_verified_at')
                ->label('Email Verified')
                ->format()
                ->filterable(),

            Column::name('name')
                ->defaultSort('asc')
                ->group('group1')
                ->searchable()
                ->hideable()
                ->filterable(),

            Column::name('planet.name')
                ->label('Planet')
                ->group('group1')
                ->searchable()
                ->hideable()
                ->filterable($this->planets),

            // Column that counts every line from 1 upwards, independent of content
            Column::index($this);

            DateColumn::name('dob')
                ->label('DOB')
                ->group('group2')
                ->filterable()
                ->hide(),

            (new LabelColumn())
                ->label('My custom heading')
                ->content('This fixed string appears in every row'),

            NumberColumn::name('dollars_spent')
                ->enableSummary(),
        ];
    }
}