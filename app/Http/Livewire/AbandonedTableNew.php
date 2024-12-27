<?php

namespace App\Http\Livewire;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\AbandonedNew;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Maatwebsite\Excel\Facades\Excel;
use Rappasoft\LaravelLivewireTables\Views\Filters\DateFilter;
use App\Exports\AbadonedCallExport;

class AbandonedTableNew extends DataTableComponent
{
    protected $model = AbandonedNew::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id')
        ->setDefaultSort('received_time', 'desc');
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
    $abandonedCallSummery = AbandonedNew::whereIn('id', $selectedIds)->get()->map(function ($item) {
        return [
            'id' => $item->id,
            'ani' => $item->ani,
            'dnis' => $item->dnis,
            'recalled_status' => $item->recalled_status,
            'received_time' => $item->received_time,
            'recalled_time' => $item->recalled_time,
        ];
    })->toArray();

    $this->clearSelected();

    return Excel::download(new AbadonedCallExport($abandonedCallSummery), 'abandoned_call_report.csv');
}


    public function columns(): array
    {
        return [
            Column::make("Id", "id")
                ->sortable()
                ->searchable(),
            Column::make("From", "ani")
                ->sortable()
                ->searchable(),
            Column::make("To", "dnis")
                ->sortable()
                ->searchable(),
                Column::make("Recalled Status", "recalled_status")
    ->sortable()
    ->format(function ($value) {
        if ($value == 1) {
            // Icon for recalled status = 1
            return '<svg class="w-6 h-6 text-green-400 mx-auto"  xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 22c5.5 0 10-4.5 10-10S17.5 2 12 2 2 6.5 2 12s4.5 10 10 10z"></path>
  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7.75 12l2.83 2.83 5.67-5.66"></path>
</svg>';
        }
        // Icon for other statuses
        return '<svg class="h-6 w-6 stroke-current text-red-500 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>';
    })
    ->html(),

            Column::make("Received", "received_time")
                ->sortable(),
            Column::make("Recalled", "recalled_time")
                ->sortable(),
            
        ];
    }




    public function filters(): array
    {
        return [
            
            DateFilter::make('From')
                ->config([
                    // 'min' => '2020-01-01',
                    // 'max' => '2021-12-31',
                ])
                ->filter(function (Builder $builder, string $value) {
                    $builder->where('received_time', '>=', $value);
                }),
            DateFilter::make('To')
                ->filter(function (Builder $builder, string $value) {
                    $builder->where('received_time', '<=', $value);
                })

        ];
    }
}
