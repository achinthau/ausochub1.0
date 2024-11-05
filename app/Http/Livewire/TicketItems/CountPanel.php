<?php

namespace App\Http\Livewire\TicketItems;

use App\Models\Ticket;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class CountPanel extends Component
{
    public $newCount;
    public $openCount;
    public $overDueCount;
    public $closedCount;

    public $tickets;

    public function mount()
    {
        $this->tickets = Cache::remember('tickets_table', 60, function () {
            return Ticket::all();
        });

        // $this->tickets=Ticket::all();

        $this->newCount = $this->tickets->where('ticket_status_id', 1)->count();
        $this->openCount = $this->tickets->where('ticket_status_id', 2)->count();
        $this->overDueCount = $this->tickets->where('ticket_status_id', 3)->count();
        $this->closedCount = $this->tickets->where('ticket_status_id', 4)->count();
    }

    public function refreshComponent()
    {
            Cache::forget('tickets_table');

            $this->tickets = Cache::remember('tickets_table', 60, function () {
                return Ticket::all();
            });

            // $this->tickets=Ticket::all();

            $this->newCount = $this->tickets->where('ticket_status_id', 1)->count();
            $this->openCount = $this->tickets->where('ticket_status_id', 2)->count();
            $this->overDueCount = $this->tickets->where('ticket_status_id', 3)->count();
            $this->closedCount = $this->tickets->where('ticket_status_id', 4)->count();
        
    }


    public function render()
    {
        return view('livewire.ticket-items.count-panel');
    }
}
