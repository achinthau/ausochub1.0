<?php

namespace App\Http\Livewire;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Cdr;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Filters\DateTimeFilter;
use App\Exports\NuisanceCustomerExport;
use Maatwebsite\Excel\Facades\Excel;
use Rappasoft\LaravelLivewireTables\Traits\WithBulkActions;




class NuisanceCustomersTable extends DataTableComponent
{
    use WithBulkActions;

    protected $model = Cdr::class;

    public function builder(): Builder
{
    return Cdr::query()
        ->where(function ($query) {
            $query->whereIn('uniqueid', function ($q) {
                $q->select('uniqueid')
                  ->from('callcount')
                  ->where('customer_reaction', 2);
            })
            ->orWhereIn('uniqueid', function ($q) {
                $q->select('uniqueid')
                  ->from('queuecount')
                  ->where('customer_reaction', 2);
            });
        });
}

    public function configure(): void
    {
        $this->setPrimaryKey('id')
         ->setBulkActions([
             'export' => 'Export',
         ]);
    }

    public function bulkActions(): array
{
    return [
        'export' => 'Export',
    ];
}

public function export()
{
    $ids = $this->getSelected(); // Already an array of IDs

    $customers = \App\Models\Cdr::whereIn('id', $ids)->get();

//  dd($this->getSelected());

    return Excel::download(new NuisanceCustomerExport($customers), 'nuisance_customer.xlsx');

}
    

    public function columns(): array
    {
        return [
            Column::make("Id", "id")
                ->sortable(),
            Column::make("calldate", "calldate")
                ->sortable(),
            Column::make("src", "src")
                ->sortable(),
            Column::make("dst", "dst")
                ->sortable(),
            Column::make("dcontext", "dcontext")
                ->sortable(),
            Column::make("channel", "channel")
                ->sortable(),
            Column::make("duration", "duration")
                ->sortable(),
            Column::make("billsec", "billsec")
                ->sortable(),
            Column::make("disposition", "disposition")
                ->sortable(),
                 Column::make("Actions")
    ->label(fn ($row) => view('table-actions-v2', [
        'id' => $row->id,
        'uniqueid' => $row->uniqueid
    ]))
    ->html()
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
                    $builder->where('calldate', '>=', $value);
                }),
    
            DateTimeFilter::make('To')
                ->config([
                    'enableTime' => true, // Enables time selection
                    'time_24hr' => true, // Optional: Use 24-hour format
                ])
                ->filter(function (Builder $builder, string $value) {
                    $builder->where('calldate', '<=', $value);
                }),
        ];
    }
}
