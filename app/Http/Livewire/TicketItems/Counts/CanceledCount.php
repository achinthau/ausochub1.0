<?php

namespace App\Http\Livewire\TicketItems\Counts;

use App\Models\Ticket;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class CanceledCount extends Component
{
    public function render()
    {
        return view('livewire.ticket-items.counts.canceled-count');
    }

    public $canceledCount=0;


    public function mount()
    {
        $this->canceledCount = Cache::remember('tickets_table', 60, function () {
            return Ticket::where('ticket_status_id', 5)->count();
        });
    }

    public function refreshComponent()
    {
            Cache::forget('tickets_table');

            $this->canceledCount = Cache::remember('tickets_table', 60, function () {
                return Ticket::where('ticket_status_id', 5)->count();
            });
        
    }
}
