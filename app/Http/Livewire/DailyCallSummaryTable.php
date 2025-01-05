<?php

namespace App\Http\Livewire;

use App\Exports\DailyCallSummeryExport;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\DailyCallSummary;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Maatwebsite\Excel\Facades\Excel;
use Rappasoft\LaravelLivewireTables\Views\Filters\DateFilter;
use Rappasoft\LaravelLivewireTables\Views\Filters\DateTimeFilter;

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
    // Get selected record IDs
    $selectedIds = $this->getSelected();

    // Fetch full records from the database using the selected IDs
    $dailyCallSummery = DailyCallSummary::whereIn('id', $selectedIds)->get()->map(function ($item) {
        return [
            'id' => $item->id,
            'date' => $item->date,
            'inbound' => $item->inbound,
            'outbound' => $item->outbound,
            'queued' => $item->queued,
            'abandoned' => $item->abandoned,
            'answered' => $item->answered,
        ];
    })->toArray();

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
            DateTimeFilter::make('From')
                ->config([
                    // Optional: Specify format or range limits
                    'enableTime' => true, // Enables time selection
                    'time_24hr' => true, // Optional: Use 24-hour format
                ])
                ->filter(function (Builder $builder, string $value) {
                    $builder->where('date', '>=', $value);
                }),
    
            DateTimeFilter::make('To')
                ->config([
                    'enableTime' => true, // Enables time selection
                    'time_24hr' => true, // Optional: Use 24-hour format
                ])
                ->filter(function (Builder $builder, string $value) {
                    $builder->where('date', '<=', $value);
                }),
        ];
    }

    public function builder(): \Illuminate\Database\Eloquent\Builder
{
    return DailyCallSummary::query();
}

}
