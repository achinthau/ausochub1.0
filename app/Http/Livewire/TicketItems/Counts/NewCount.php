<?php

namespace App\Http\Livewire\TicketItems\Counts;

use App\Models\Ticket;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class NewCount extends Component
{
    public function render()
    {
        return view('livewire.ticket-items.counts.new-count');
    }

    public $newCount;


    public function mount()
    {
        $this->newCount = Cache::remember('tickets_table', 60, function () {
            return Ticket::where('ticket_status_id', 1)->count();
        });
    }

    public function refreshComponent()
    {
            Cache::forget('tickets_table');

            $this->newCount = Cache::remember('tickets_table', 60, function () {
                return Ticket::where('ticket_status_id', 1)->count();
            });
        
    }
}
