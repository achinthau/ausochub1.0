<?php

namespace App\Http\Livewire\TicketItems;

use Livewire\Component;

class Status extends Component
{
    public $statusId;
    public $status;
    public $ticketItemId;

    public function mount($ticketStatusId, $ticketItemId)
    {
        $this->statusId=$ticketStatusId;
        $this->ticketItemId=$ticketItemId;
    }

    public function render()
    {
        return view('livewire.ticket-items.status');
    }
}
