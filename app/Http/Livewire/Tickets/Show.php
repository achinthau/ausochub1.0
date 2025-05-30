<?php

namespace App\Http\Livewire\Tickets;

use App\Models\CrmDepartment;
use App\Models\Ticket;
use App\Models\TicketActivity;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use WireUi\Traits\Actions;

class Show extends Component
{

    use Actions;
    public ?Ticket $ticket;
    public $showTicketModal = false;
    public $customerCard = false;
    public $comment = null;
    public $loggedUser ;
    public $users;
    public $selectedDepartment;
    public $assignOption = null;
    public $changeDepartment = null;
    public $departments;
public $selectedUser = null;

public ?TicketActivity $activity;




    protected $listeners = [
        'openTicket' => 'openTicket'
    ];

    public function render()
    {
        return view('livewire.tickets.show');
    }


    public function updatedShowTicketModal($value)
    {
        if ($value) {
            $this->ticket = null;
        }
    }

    public function openTicket($ticketId, $customerCard = false)
    {
        $this->ticket = Ticket::find($ticketId);
        $this->showTicketModal = true;
        $this->customerCard = $customerCard;
        $this->loggedUser= Auth::user() ;
        $this->users= User::select('id','name')->where('department_id',$this->ticket->department_id)->get()->toArray();
        $this->departments=CrmDepartment::select('id','name')->get()->toArray();
    }

    public function save()
    {
        $this->ticket->ticket_status_id = 2;
        $this->ticket->save();

        $this->ticket->logActivity("Ticket Started");

        if ($this->customerCard) {
            $this->emitTo('leads.show', 'refreshCard');
        } else {
            $this->emitTo('tickets.index', 'refreshList');
        }
        $this->notification()->success(
            $title = 'Success',
            $description = 'Ticket Opened'
        );
        $this->showTicketModal = false;
    }


    public function closeTicket()
    {

        $this->validate([
            'comment' => 'required'
        ]);

        $this->ticket->ticket_status_id = 4;
        $this->ticket->updated_at = Carbon::now();
        $this->ticket->save();

        $this->ticket->logActivity("Ticket Closed", $this->comment);

        if ($this->customerCard) {
            $this->emitTo('leads.show', 'refreshCard');
        } else {
            $this->emitTo('tickets.index-new', 'refreshList');
        }
        $this->comment = null;
        $this->ticket->refresh();

        $this->notification()->success(
            $title = 'Success',
            $description = 'Ticket Closed'
        );
        $this->showTicketModal = false;
    }

    public function comment()
    {
        $this->validate([
            'comment' => 'required'
        ]);

        $this->ticket->logActivity("Commented on ticket", $this->comment);
        $this->comment = null;
        $this->ticket->refresh();

        $this->customerCard = false;

        $this->notification()->success(
            $title = 'Success',
            $description = 'Comment Added'
        );
    }

    public function assign()
    {
        $this->ticket->assigned_user_id = Auth::user()->id;
        $this->ticket->save();
        $this->ticket = $this->ticket->fresh('assignedUser');
        // $this->activity->type = "Assigned to himself";
        $this->ticket->logActivity("Assigned to himself", $this->comment);
        $this->comment='';
        $this->ticket->refresh();
    }

    public function assignToUser()
    {
        $this->ticket->assigned_user_id = $this->selectedUser;
        $this->ticket->save();
        $this->ticket = $this->ticket->fresh('assignedUser');
        $userName = User::where('id', $this->selectedUser)->value('name');
        $this->ticket->logActivity("Assigned to ". $userName , $this->comment);
        $this->comment='';
        $this->ticket->refresh();
    }

    public function unAssign()
    {
        $this->ticket->assigned_user_id = Null;
        $this->ticket->save();
        $this->ticket = $this->ticket->fresh('assignedUser');
        $this->ticket->logActivity("Unassigned", $this->comment);
        $this->ticket->refresh();
    }

    public function assignDepartment()
    {
        $this->ticket->department_id = $this->selectedDepartment;
        $this->ticket->save();
        $this->ticket->refresh();
    }
}
