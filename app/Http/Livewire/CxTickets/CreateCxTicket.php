<?php

namespace App\Http\Livewire\CxTickets;

use App\Models\CxTicket;
use App\Models\CxTicketCategory;
use App\Models\CxTicketServCenter;
use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CreateCxTicket extends Component
{
    public $category, $product, $model, $work_order_no, $service_center, $warranty_status, $sold_date;
    public $customer_name, $customer_address, $customer_contact_01, $customer_contact_02;
    public $technician_name, $technician_contact, $supervisor_name, $supervisor_contact;
    public $creatingCxTicket = false;

    public $categories = [];
    public $serviceCenters = [];

    public $ticketId = null;
    public $editMode = false;

    public $technicians = [];
    public $supervisors = [];




    public function mount()
    {
        $this->categories = CxTicketCategory::all(['id', 'name']);
        $this->serviceCenters = CxTicketServCenter::all(['id', 'name']);
        $this->technicians = User::where('user_type_id', 10)->get(['id', 'name', 'phone']);
        $this->supervisors = User::whereIn('user_type_id', [11,10])->get(['id', 'name', 'phone']);
    }

    public function updatedTechnicianName($value)
{
    $user = $this->technicians->firstWhere('name', $value);
    if ($user) {
        $this->technician_contact = $user->phone;
    } else {
        $this->technician_contact = '';
    }
}

    public function updatedSupervisorName($value)
{
    $user = $this->supervisors->firstWhere('name', $value);
    if ($user) {
        $this->supervisor_contact = $user->phone;
    } else {
        $this->supervisor_contact = '';
    }
}



    // protected $listeners = ['editTicket' => 'loadTicket'];

    protected $rules = [
        'category' => 'required',
        'product' => 'required|string',
        'model' => 'required|string',
        'work_order_no' => 'required|string|unique:cx_tickets,work_order_no',
        'service_center' => 'required',
        'warranty_status' => 'required',
        'sold_date' => 'required|date',
        'customer_name' => 'required|string',
        'customer_address' => 'required|string',
        'customer_contact_01' => 'required|string',
        'customer_contact_02' => 'nullable|string',
        'technician_name' => 'required|string',
        'technician_contact' => 'required|string',
        'supervisor_name' => 'required|string',
        'supervisor_contact' => 'required|string',
    ];

    public function save()
    {$companyName = DB::table('companies')
    ->where('id', auth()->user()?->tenant_context)
    ->value('name');

    // dd($companyName);
        $this->validate();

        if ($this->editMode) {

            
            $ticket = CxTicket::findOrFail($this->ticketId);

            $ticket->update([
                'category' => $this->category,
                'product' => $this->product,
                'model' => $this->model,
                'work_order_no' => $this->work_order_no,
                'service_center' => $this->service_center,
                'warranty_status' => $this->warranty_status,
                'sold_date' => $this->sold_date,
                'customer_name' => $this->customer_name,
                'customer_address' => $this->customer_address,
                'customer_contact_01' => $this->customer_contact_01,
                'customer_contact_02' => $this->customer_contact_02,
                'technician_name' => $this->technician_name,
                'technician_contact' => $this->technician_contact,
                'supervisor_name' => $this->supervisor_name,
                'supervisor_contact' => $this->supervisor_contact,
            ]);

            session()->flash('message', 'Ticket Updated Successfully!');
        } else {
            CxTicket::create([
                'category' => $this->category,
                'product' => $this->product,
                'model' => $this->model,
                'work_order_no' => $this->work_order_no,
                'service_center' => $this->service_center,
                'warranty_status' => $this->warranty_status,
                'sold_date' => $this->sold_date,
                'customer_name' => $this->customer_name,
                'customer_address' => $this->customer_address,
                'customer_contact_01' => $this->customer_contact_01,
                'customer_contact_02' => $this->customer_contact_02,
                'technician_name' => $this->technician_name,
                'technician_contact' => $this->technician_contact,
                'supervisor_name' => $this->supervisor_name,
                'supervisor_contact' => $this->supervisor_contact,
                'creator' => auth()->user()->name,
                'company' => $companyName ?? null,
                'status' => 'Open',
            ]);

            session()->flash('message', 'Ticket Created Successfully!');
        }

        $this->emitTo('cx-tickets-table', 'cxTicketUpdated');

        $this->resetExcept(['categories', 'serviceCenters', 'supervisors', 'technicians']);
    }



    public function loadTicket($ticketId)
    {
        $ticket = CxTicket::findOrFail($ticketId);

        $this->ticketId = $ticket->id;
        $this->editMode = true;

        $this->category = $ticket->category;
        $this->product = $ticket->product;
        $this->model = $ticket->model;
        $this->work_order_no = $ticket->work_order_no;
        $this->service_center = $ticket->service_center;
        $this->warranty_status = $ticket->warranty_status;
        $this->sold_date = $ticket->sold_date;
        $this->customer_name = $ticket->customer_name;
        $this->customer_address = $ticket->customer_address;
        $this->customer_contact_01 = $ticket->customer_contact_01;
        $this->customer_contact_02 = $ticket->customer_contact_02;
        $this->technician_name = $ticket->technician_name;
        $this->technician_contact = $ticket->technician_contact;
        $this->supervisor_name = $ticket->supervisor_name;
        $this->supervisor_contact = $ticket->supervisor_contact;

        $this->creatingCxTicket = true; // Open modal
    }



    public function render()
    {
        return view('livewire.cx-tickets.create-cx-ticket');
    }
}
