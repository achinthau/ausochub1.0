<?php

namespace App\Http\Livewire\Orders;

use App\Models\Ticket;
use Livewire\Component;
use WireUi\Traits\Actions;

class Show extends Component
{

    use Actions;
    public ?Ticket $ticket;
    public $showTicketModal = false;
    public $orderRef = "";




    protected $listeners = [
        'openTicket' => 'openTicket'
    ];

    public function render()
    {
        return view('livewire.orders.show');
    }

    public function updatedShowTicketModal($value)
    {
        if ($value) {
            $this->ticket = null;
        }
    }

    public function openTicket($ticketId)
    {
        $this->ticket = Ticket::find($ticketId);
        $this->showTicketModal = true;
    }

    public function save()
    {

        $this->validate([
            'orderRef'=>'required'
        ]);

        $this->ticket->ticket_status_id = 2;
        $this->ticket->order_ref = $this->orderRef;
        $this->ticket->bill_no = $this->ticket->outlet->contact_no."-".$this->orderRef;
        $this->ticket->save();
        $this->ticket->logActivity("Start Processing");
        $this->emitTo('orders.index','refreshList');
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
        $this->ticket->logActivity("Completed");
        $this->emitTo('orders.index','refreshList');
        $this->notification()->success(
            $title = 'Success',
            $description = 'Ticket Closed'
        );
        $this->showTicketModal = false;
    }
}
