<?php

namespace App\Http\Livewire;

use App\Exports\DailyCallSummeryExport;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\DailyCallSummary;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Maatwebsite\Excel\Facades\Excel;
use Rappasoft\LaravelLivewireTables\Views\Filters\DateFilter;

class DailyCallSummaryTable extends DataTableComponent
{
    protected $model = DailyCallSummary::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id')
        ->setDefaultSort('date', 'desc');
    }

    public function bulkActions(): array
{
    return [
        'export' => 'Export',
    ];
}
 
public function export()
{
    $dailyCallSummery = $this->getSelected();
 
    $this->clearSelected();
 
    return Excel::download(new DailyCallSummeryExport($dailyCallSummery), 'dailyCallSummery.csv');
}

    public function columns(): array
    {
        return [
            Column::make("Id", "id")
                ->sortable()->searchable(),
            Column::make("Date", "date")
                ->sortable()->searchable(),
            Column::make("Inbound", "inbound")
                ->sortable()->searchable(),
            Column::make("Outbound", "outbound")
                ->sortable()->searchable(),
            Column::make("Queued", "queued")
                ->sortable()->searchable(),
            Column::make("Abandoned", "abandent")
                ->sortable()->searchable(),
            Column::make("Answered", "answered")
                ->sortable()->searchable(),
            // Column::make("Created at", "created_at")
            //     ->sortable()->searchable(),
            // Column::make("Updated at", "updated_at")
            //     ->sortable()->searchable(),
        ];
    }

    public function filters(): array
    {
        return [
            
            DateFilter::make('Due From')
                ->config([
                    // 'min' => '2020-01-01',
                    // 'max' => '2021-12-31',
                ])
                ->filter(function (Builder $builder, string $value) {
                    $builder->where('date', '>=', $value);
                }),
            DateFilter::make('Due To')
                ->filter(function (Builder $builder, string $value) {
                    $builder->where('date', '<=', $value);
                })

        ];
    }

    public function builder(): \Illuminate\Database\Eloquent\Builder
{
    return DailyCallSummary::query();
}

}
