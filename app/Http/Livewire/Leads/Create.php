<?php

namespace App\Http\Livewire\Leads;

use App\Models\Lead;
use App\Models\QueueCount;
use App\Models\Ticket;
use Livewire\Component;
use WireUi\Traits\Actions;

class Create extends Component
{
    use Actions;

    public Lead $lead;
    public  $tickets;
    public  $callLogs;

    public $showTicketEditModal = false;



    protected $rules = [
        'lead.contact_number' => 'required',
        'lead.skill.skillname' => 'nullable',
        'lead.first_name' => 'required',
        'lead.last_name' => 'nullable',
        'lead.nic' => 'nullable',
        'lead.address_line_1' => 'nullable',
        'lead.address_line_2' => 'nullable',
        'lead.city' => 'nullable',
        'lead.contact_number_2' => 'nullable',
        'lead.email' => 'nullable|email',
        //'lead.priority_level_id'=>'required',
        //'lead.satisfaction_level_id'=>'required',

    ];

    public function mount($lead)
    {
        $this->lead = $lead;
        $this->showTicketEditModal = $lead->status_id == 1;
       
    }

    public function render()
    {
        return view('livewire.leads.create');
    }



    public function save()
    {
        $this->validate();
        $this->lead->status_id = 2;
        $this->lead->save();

        $this->showTicketEditModal = false;

        $this->emitTo('leads.show', 'refreshCard');

        $this->notification()->success(
            $title = 'Success',
            $description = 'Customer successfull saved'
        );
    }
}
