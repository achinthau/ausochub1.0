<?php
namespace App\Http\Livewire\CxTickets;

use App\Models\CxTicket;
use App\Models\CxTicketServCenter;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use App\Models\User;

class Edit extends Component
{
    public $ticket;
    public $updateTicketModal = false;

    public $category, $product, $model, $work_order_no, $service_center, $warranty_status, $sold_date;
    public $customer_name, $customer_address, $customer_contact_01, $customer_contact_02;
    public $technician_name, $technician_contact, $supervisor_name, $supervisor_contact;
    public $status, $satisfaction_rate;

    public $serviceCenters = [];

    public $technicians = [];
    public $supervisors = [];

    protected $listeners = ['showUpdateTicketModal' => 'showUpdateTicket'];

    public function mount()
    {
        $this->ticket = new CxTicket(); 
        $this->serviceCenters = CxTicketServCenter::all(['id', 'name']);
         $this->technicians = User::where('user_type_id', 10)->get(['id', 'name', 'phone']);
         $this->supervisors = User::whereIn('user_type_id', [11,10])->get(['id', 'name', 'phone']);
    }

    public function updatedTechnicianName($value)
{
    $tech = $this->technicians->firstWhere('name', $value);
    $this->technician_contact = $tech?->phone ?? '';
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



    public function showUpdateTicket($id)
    {
        $this->ticket = CxTicket::findOrFail($id);
        $this->updateTicketModal = true;
        // dd($this->ticket->product);
        $this->category = $this->ticket->category;
    $this->product = $this->ticket->product;
    $this->model = $this->ticket->model;
    $this->work_order_no = $this->ticket->work_order_no;
    $this->service_center = $this->ticket->service_center;
    // dd($this->service_center);
    $this->warranty_status = $this->ticket->warranty_status;
    $this->sold_date = $this->ticket->sold_date;

    $this->customer_name = $this->ticket->customer_name;
    $this->customer_address = $this->ticket->customer_address;
    $this->customer_contact_01 = $this->ticket->customer_contact_01;
    $this->customer_contact_02 = $this->ticket->customer_contact_02;

    $this->technician_name = $this->ticket->technician_name;
    $this->technician_contact = $this->ticket->technician_contact;
    $this->supervisor_name = $this->ticket->supervisor_name;
    $this->supervisor_contact = $this->ticket->supervisor_contact;

    $this->status = $this->ticket->status;
    $this->satisfaction_rate = $this->ticket->satisfaction_rate;
    }

    public function update()
{
    $this->validate([
        // 'category' => 'required|string',
        // 'product' => 'required|string',
        // 'model' => 'required|string',
        // 'work_order_no' => 'required|string',
        // 'service_center' => 'required|string',
        // 'warranty_status' => 'required|string',
        // 'sold_date' => 'required|date',
        // 'customer_name' => 'required|string',
        // 'customer_address' => 'required|string',
        // 'customer_contact_01' => 'nullable|string',
        // 'customer_contact_02' => 'nullable|string',
        'technician_name' => 'required|string',
        'technician_contact' => 'nullable|string',
        'supervisor_name' => 'required|string',
        'supervisor_contact' => 'nullable|string',
    ]);

    if ($this->ticket) {
        $this->ticket->update([
            // 'category' => $this->category,
            // 'product' => $this->product,
            // 'model' => $this->model,
            // 'work_order_no' => $this->work_order_no,
            // 'service_center' => $this->service_center,
            // 'warranty_status' => $this->warranty_status,
            // 'sold_date' => $this->sold_date,
            // 'customer_name' => $this->customer_name,
            // 'customer_address' => $this->customer_address,
            // 'customer_contact_01' => $this->customer_contact_01,
            // 'customer_contact_02' => $this->customer_contact_02,
            'technician_name' => $this->technician_name,
            'technician_contact' => $this->technician_contact,
            'supervisor_name' => $this->supervisor_name,
            'supervisor_contact' => $this->supervisor_contact,
        ]);

        // Emit event to refresh ticket list (if needed)
        // $this->emit('cxTicketUpdated');
        $this->emitTo('cx-tickets-table', 'cxTicketUpdated');

        // Close modal
        $this->updateTicketModal = false;

        // Show success notification
        session()->flash('message', 'Ticket updated successfully!');
    }
}

public function closeTicket()
{
    if($this->ticket)
    {
        $this->ticket->update([
            'status' => 'Closed',
            'closed_by' => Auth::user()->name,
        ]);
    }
    $this->emitTo('cx-tickets-table', 'cxTicketUpdated');
    $this->updateTicketModal = false;
}


    public function render()
    {
        return view('livewire.cx-tickets.edit');
    }
}
