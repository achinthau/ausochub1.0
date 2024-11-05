<?php

namespace App\Http\Livewire\TicketItems\Counts;

use App\Models\Ticket;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class ClosedCount extends Component
{
    public function render()
    {
        return view('livewire.ticket-items.counts.closed-count');
    }

    public $closedCount;


    public function mount()
    {
        $this->closedCount = Cache::remember('tickets_table', 60, function () {
            return Ticket::where('ticket_status_id', 4)->count();
        });
    }

    public function refreshComponent()
    {
            Cache::forget('tickets_table');

            $this->closedCount = Cache::remember('tickets_table', 60, function () {
                return Ticket::where('ticket_status_id', 4)->count();
            });
        
    }
}
