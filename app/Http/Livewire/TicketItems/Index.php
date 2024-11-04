<?php

namespace App\Http\Livewire\TicketItems;

use App\Models\Ticket;
use Livewire\Component;

class Index extends Component
{
    public $ticketItemId; // Renaming to avoid ID conflict


    public function mount($ticketItem)
    {
        $this->ticketItemId = $ticketItem->id;
    }

    public function edit($id)
    {
        // Logic to handle the edit action
        // Example: Open a modal or redirect to the edit page
    }

    public function delet($ticketItemId)
    {
        // Logic to delete the ticket item
        Ticket::destroy($ticketItemId);

        // Optionally refresh the table or emit an event
        // $this->emit('refreshDatatable');
    }

    public function render()
    {
        return view('livewire.ticket-items.index');
    }
}
