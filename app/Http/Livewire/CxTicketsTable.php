<?php
namespace App\Http\Livewire;

use App\Exports\CxTicketsExport;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;
use App\Models\CxTicket;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Facades\Excel;
use Rappasoft\LaravelLivewireTables\Views\Filters\DateFilter;

class CxTicketsTable extends DataTableComponent
{
    protected $model = CxTicket::class;

    protected $listeners = ['cxTicketUpdated' => 'refreshTable'];

    public function refreshTable()
    {
        $this->resetPage();
    }

    public function bulkActions(): array
    {
        return [
            'export' => 'Export',
        ];
    }

    public function export()
{
    $selectedIds = $this->getSelected();

    $tickets = CxTicket::whereIn('id', $selectedIds)->get();
    return Excel::download(new CxTicketsExport($tickets), 'cx_tickets.xlsx');
}


    public function configure(): void
    {
        $this->setPrimaryKey('id');

    }

    public function filters(): array
{
    return [
        SelectFilter::make('Category')
            ->options([
                '' => 'All',
                'Service' => 'Service',
                'Repair' => 'Repair',
                'Installation' => 'Installation',
            ])
            ->filter(function ($query, $value) {
                if ($value !== '') {
                    $query->where('category', $value);
                }
            }),

            SelectFilter::make('Status')
            ->options([
                '' => 'All',
                'Open' => 'Open',
                'Closed' => 'Closed',
                'Rated' => 'Rated',
                'Canceled' => 'Canceled',
            ])
            ->filter(function ($query, $value) {
                if ($value !== '') {
                    $query->where('status', $value);
                }
            }),

            DateFilter::make('Due From')
                ->config([
                    // 'min' => '2020-01-01',
                    // 'max' => '2021-12-31',
                ])
                ->filter(function (Builder $builder, string $value) {
                    $builder->where('updated_at', '>=', $value);
                }),
            DateFilter::make('Due To')
                ->filter(function (Builder $builder, string $value) {
                    $builder->where('updated_at', '<=', $value);
                })
    ];
}

public function builder(): Builder
{
    $query = CxTicket::query();

    $filters = $this->getFilters();

    if (!empty($filters['Category'])) {
        $query->where('category', $filters['Category']);


    }

    return $query;
}

// public function filters(): array
//     {
//         return [
//             DateFilter::make('Due From')
//                 ->config([
//                     // 'min' => '2020-01-01',
//                     // 'max' => '2021-12-31',
//                 ])
//                 ->filter(function (Builder $builder, string $value) {
//                     $builder->where('due_at', '>=', $value);
//                 }),
//             DateFilter::make('Due To')
//                 ->filter(function (Builder $builder, string $value) {
//                     $builder->where('due_at', '<=', $value);
//                 })
//             ];
//             }


    public function columns(): array
    {
        return [
            Column::make("Id", "id")->sortable()->searchable(),
            Column::make("Category", "category")->sortable()->searchable(),
            Column::make("Product", "product")->sortable()->searchable(),
            Column::make("Model", "model")->sortable()->searchable(),
            Column::make("Work order no", "work_order_no")->sortable()->searchable(),
            Column::make("Service center", "service_center")->sortable()->searchable(),
            Column::make("Warranty status", "warranty_status")->sortable()->searchable(),
            Column::make("Sold date", "sold_date")->sortable()->searchable(),
            Column::make("Customer name", "customer_name")->sortable()->searchable(),
            Column::make("Customer address", "customer_address")->sortable()->searchable(),
            Column::make("Customer contact 01", "customer_contact_01")->sortable()->searchable(),
            Column::make("Customer contact 02", "customer_contact_02")->sortable()->searchable(),
            Column::make("Technician name", "technician_name")->sortable()->searchable(),
            Column::make("Technician contact", "technician_contact")->sortable()->searchable(),
            Column::make("Supervisor name", "supervisor_name")->sortable()->searchable(),
            Column::make("Supervisor contact", "supervisor_contact")->sortable()->searchable(),
            Column::make("Ticket Creator", "creator")->sortable()->searchable(),
            Column::make("Status", "status")->sortable()->searchable(),
            Column::make("Satisfaction Rate", "satisfaction_rate")->sortable()->searchable(),
            // Column::make("Satisfaction Reasons", "satisfaction_reasons")->sortable()->searchable(),
            // Column::make("Dis_satisfaction Reasons", "dis_satisfaction_reasons")->sortable()->searchable(),
            // Column::make("Cancelling Reasons", "cancelling_reasons")->sortable()->searchable(),
            // Column::make("Created at", "created_at")->sortable(),
            Column::make("Closed_BY", "closed_by")->sortable(),
            Column::make("Company", "company")->sortable(),
            Column::make("Surveyed By", "surveyed_by")->sortable(),
            Column::make("Updated at", "updated_at")->sortable(),
            Column::make("Actions")
                ->label(fn ($row) => view('livewire.cx-tickets.actions', ['ticket' => $row->id]))
                ->html(),
        ];
    }
}
