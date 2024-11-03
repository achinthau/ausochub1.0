<?php

namespace App\Http\Livewire\Tickets;

use App\Models\Ticket;
use Livewire\Component;
use WireUi\Traits\Actions;

class Show extends Component
{

    use Actions;
    public ?Ticket $ticket;
    public $showTicketModal = false;
    public $customerCard = false;
    public $comment = null;




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
    }

    public function save()
    {
        $this->ticket->ticket_status_id = 2;
        $this->ticket->save();
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
        $this->ticket->ticket_status_id = 4;
        $this->ticket->save();


        if ($this->customerCard) {
            $this->emitTo('leads.show', 'refreshCard');
        } else {
            $this->emitTo('tickets.index', 'refreshList');
        }
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
}
