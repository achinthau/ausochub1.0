<?php

namespace App\Http\Livewire\TicketItems\Counts;

use App\Models\Ticket;
use App\Models\TicketItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class OpenCount extends Component
{
    public function render()
    {
        return view('livewire.ticket-items.counts.open-count');
    }

    public $openCount = 0;


    public function mount()
    {
        if(Auth::user()->department_id)
        {
            $this->openCount = Cache::remember('tickets_table', 60, function () {
                return Ticket::where('ticket_status_id', 2)->where('department_id',Auth::user()->department_id )->count();
            });
        }
else
        {
            $this->openCount = Cache::remember('tickets_table', 60, function () {
                return Ticket::where('ticket_status_id', 2)->count();
            });
        }
    }

    public function refreshComponent()
    {
        Cache::forget('tickets_table');

        if(Auth::user()->department_id)
        {
            $this->openCount = Cache::remember('tickets_table', 60, function () {
                return Ticket::where('ticket_status_id', 2)->where('department_id',Auth::user()->department_id )->count();
            });
        }
else
        {
        $this->openCount = Cache::remember('tickets_table', 60, function () {
            return Ticket::where('ticket_status_id', 2)->count();
        });
    }
    }
}
