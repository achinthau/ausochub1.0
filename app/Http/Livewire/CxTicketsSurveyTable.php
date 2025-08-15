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

    public $customerContact = null;   
    public $ticketCount = 1;          
    public $ticket;

    protected $listeners = ['cxTicketSurveyUpdated' => 'refreshTable'];

    /** Helpers */
    protected function companyNames(): array
    {
        return array_filter(array_map('trim', explode(',', (string) (auth()->user()->tenant_context ?? ''))));
    }

    public function mount($ticketId = null): void
    {
        $companies = $this->companyNames();

        
        $ticket = null;

        if ($ticketId) {
            $ticket = CxTicket::find($ticketId);
            $this->ticket = $ticket;
        }

        
        if (!$ticket) {
            $ticket = CxTicket::query()
                ->where('status', 'Closed')
                ->whereIn('company', $companies)
                ->orderBy('updated_at', 'asc')
                ->first();

                $this->ticket = $ticket;
        }

        
        if ($ticket) {
            
            $this->customerContact = $ticket->customer_contact_01 ?: $ticket->customer_contact_02;

            
            $this->ticketCount = CxTicket::query()
                ->where('status', 'Closed')
                ->whereIn('company', $companies)
                ->where(function ($q) {
                    $q->where('customer_contact_01', $this->customerContact)
                      ->orWhere('customer_contact_02', $this->customerContact);
                })
                ->count() ?: 1; 

            
            $this->setPerPageVisibilityDisabled();
            $this->setPaginationVisibilityDisabled();
            $this->setPerPageAccepted([$this->ticketCount]);
            $this->setPerPage($this->ticketCount);
        } else {
            
            $this->ticketCount = 1;
            $this->setPerPageAccepted([1]);
            $this->setPerPage(1);
        }
    }

    public function refreshTable()
{
    if ($this->customerContact) {
        $companies = $this->companyNames();

        $remainingCount = CxTicket::query()
            ->where('status', 'Closed')
            ->whereIn('company', $companies)
            ->where(function ($q) {
                $q->where('customer_contact_01', $this->customerContact)
                  ->orWhere('customer_contact_02', $this->customerContact);
            })
            ->count();

        if ($remainingCount > 1) {
           
            
            $this->resetPage();
        }
        else{
            return redirect()->route('cx-tickets-survey.index'); 
        }
    }
}


    public function bulkActions(): array
    {
        return ['export' => 'Export'];
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
        // Do NOT set perPage here.
    }

    public function builder(): Builder
    {
        $company = !empty($this->ticket) && !empty($this->ticket->company)
    ? $this->ticket->company
    : 'dddddddd';


        $query = CxTicket::query()
            ->where('status', 'Closed')
            ->where('company', $company)
            ->orderBy('updated_at', 'asc');

        
        if ($this->customerContact) {
            $query->where(function ($q) {
                $q->where('customer_contact_01', $this->customerContact)
                  ->orWhere('customer_contact_02', $this->customerContact);
            });
        } else {
            
            $query->limit(1);
        }

        return $query;
    }

    public function columns(): array
    {
        return [
            Column::make("Id", "id")->sortable(),
            Column::make("Category", "category")->sortable(),
            Column::make("Product", "product")->sortable(),
            Column::make("Model", "model")->sortable(),
            Column::make("Work order no", "work_order_no")->sortable()->searchable(),
            Column::make("Service center", "service_center")->sortable(),
            Column::make("Warranty status", "warranty_status")->sortable(),
            Column::make("Sold date", "sold_date")->sortable(),
            Column::make("Customer name", "customer_name")->sortable(),
            Column::make("Customer address", "customer_address")->sortable(),
            Column::make("Customer contact 01", "customer_contact_01")->sortable()->searchable(),
            Column::make("Customer contact 02", "customer_contact_02")->sortable()->searchable(),
            Column::make("Technician name", "technician_name")->sortable(),
            Column::make("Technician contact", "technician_contact")->sortable(),
            Column::make("Supervisor name", "supervisor_name")->sortable(),
            Column::make("Supervisor contact", "supervisor_contact")->sortable(),
            Column::make("Status", "status")->sortable(),
            Column::make("Creator", "creator")->sortable(),
            Column::make("Created at", "created_at")->sortable(),
            Column::make("Updated at", "updated_at")->sortable(),
            Column::make("Actions")
                ->label(fn($row) => view('livewire.cx-tickets.survey.rating-button', ['clientActivity' => $row->id]))
                ->html(),
        ];
    }
}
