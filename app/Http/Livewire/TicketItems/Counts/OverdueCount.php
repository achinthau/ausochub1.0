<?php

namespace App\Http\Livewire\TicketItems\Counts;

use App\Models\Ticket;
use App\Models\TicketItem;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class OverdueCount extends Component
{
    public function render()
    {
        return view('livewire.ticket-items.counts.overdue-count');
    }

    public $overdueCount;


    public function mount()
    {
        $this->overdueCount = Cache::remember('tickets_table', 60, function () {
            return Ticket::where('ticket_status_id', 3)->count();
        });
    }

    public function refreshComponent()
    {
            Cache::forget('tickets_table');

            $this->overdueCount = Cache::remember('tickets_table', 60, function () {
                return Ticket::where('ticket_status_id', 3)->count();
            });
        
    }
}
