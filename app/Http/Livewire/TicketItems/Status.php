<?php

namespace App\Http\Livewire\TicketItems;

use Livewire\Component;

class Status extends Component
{
    public $statusId;
    public $status;
    public $ticketItemId;

    public function mount($ticketStatusId, $ticketItemId, $status = null)
    {
        $this->statusId = $status->ticket_status_id;
        $this->ticketItemId = $status->id;
        $this->status = $status;
    }

    public function render()
    {
        return view('livewire.ticket-items.status');
    }
}
