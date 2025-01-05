<?php

namespace App\Http\Livewire;

use App\Exports\DailyQueueSummeryExport;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\DailyQueueSummary;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Maatwebsite\Excel\Facades\Excel;
use Rappasoft\LaravelLivewireTables\Views\Filters\DateFilter;
use Rappasoft\LaravelLivewireTables\Views\Filters\DateTimeFilter;

class DailyQueueSummaryTable extends DataTableComponent
{
    protected $model = DailyQueueSummary::class;

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
    $dailyQueueSummery = DailyQueueSummary::whereIn('id', $selectedIds)->get();

    $this->clearSelected();

    // Pass the fetched records to the export class
    return Excel::download(new DailyQueueSummeryExport($dailyQueueSummery), 'dailyQueueSummery.csv');
}



    public function columns(): array
    {
        return [
            Column::make("Id", "id")
                ->sortable()->searchable(),
            Column::make("Date", "date")
                ->sortable()->searchable(),
            Column::make("Queue", "queue")
                ->sortable()->searchable(),
            Column::make("Calls", "calls")
                ->sortable()->searchable(),
            Column::make("Answered", "answered")
                ->sortable()->searchable(),
            Column::make("Abandoned", "abandoned")
                ->sortable()->searchable(),
            Column::make("Agents", "agents")
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
    return DailyQueueSummary::query();
}


}
