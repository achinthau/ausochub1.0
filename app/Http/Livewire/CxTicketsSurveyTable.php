<?php

namespace App\Http\Livewire;

use App\Exports\CxTicketsSurveyExport;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\CxTicket;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Facades\Excel;

class CxTicketsSurveyTable extends DataTableComponent
{
    protected $model = CxTicket::class;

    // Set the default search value in the mount method
    // public function mount()
    // {
    //     // Set a default value for search
    //     $this->search = ''; // Clear the default value to allow for dynamic searching
    // }

    protected $listeners = ['cxTicketSurveyUpdated' => 'refreshTable'];

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
    return Excel::download(new CxTicketsSurveyExport($tickets), 'cx_tickets_completed.xlsx');
}


    public function configure(): void
    {
        $this->setPrimaryKey('id');

        $this->setPaginationVisibilityDisabled();
        $this->setPerPageVisibilityDisabled();
        $this->setPerPageAccepted([1, 25, 50, 100]);
        $this->setPerPage(1);
    }

    // public function builder(): Builder
    // {
    //     $query = CxTicket::query()->where('status', 'Closed');
    //     return $query;
    // }

    public function builder(): Builder
{
    $companyName = \DB::table('companies')
        ->where('id', auth()->user()->tenant_context)
        ->value('name');

    return CxTicket::query()
    ->where('status', 'Closed')
        ->where('company', $companyName)
        ->orderBy('updated_at', 'desc');
}

    public function columns(): array
    {
        return [
            Column::make("Id", "id")
                ->sortable(),
            Column::make("Category", "category")
                ->sortable(),
            Column::make("Product", "product")
                ->sortable(),
            Column::make("Model", "model")
                ->sortable(),
            Column::make("Work order no", "work_order_no")
                ->sortable()
                ->searchable(),
            Column::make("Service center", "service_center")
                ->sortable(),
            Column::make("Warranty status", "warranty_status")
                ->sortable(),
            Column::make("Sold date", "sold_date")
                ->sortable(),
            Column::make("Customer name", "customer_name")
                ->sortable(),
            Column::make("Customer address", "customer_address")
                ->sortable(),
            Column::make("Customer contact 01", "customer_contact_01")
                ->sortable()
                ->searchable(),
            Column::make("Customer contact 02", "customer_contact_02")
                ->sortable()
                ->searchable(),
            Column::make("Technician name", "technician_name")
                ->sortable(),
            Column::make("Technician contact", "technician_contact")
                ->sortable(),
            Column::make("Supervisor name", "supervisor_name")
                ->sortable(),
            Column::make("Supervisor contact", "supervisor_contact")
                ->sortable(),
            Column::make("Status", "status")
                ->sortable(),
            Column::make("Creator", "creator")
                ->sortable(),
            Column::make("Created at", "created_at")
                ->sortable(),
            Column::make("Updated at", "updated_at")
                ->sortable(),
            Column::make("Actions")
                ->label(fn($row) => view('livewire.cx-tickets.survey.rating-button', ['clientActivity' => $row->id]))
                ->html(),
        ];
    }
}
