<?php

namespace App\Http\Livewire\TicketItems\Counts;

use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class NewCount extends Component
{
    public function render()
    {
        return view('livewire.ticket-items.counts.new-count');
    }

    public $newCount = 0;


    public function mount()
    {
        if(Auth::user()->department_id)
        {
            $this->newCount = Cache::remember('tickets_table', 60, function () {
                return Ticket::where('ticket_status_id', 1)->where('department_id',Auth::user()->department_id )->count();
            });
        }
else
        {

        $this->newCount = Cache::remember('tickets_table', 60, function () {
            return Ticket::where('ticket_status_id', 1)->count();
        });
    }
    }

    public function refreshComponent()
    {
        Cache::forget('tickets_table');

        if(Auth::user()->department_id)
        {
            $this->newCount = Cache::remember('tickets_table', 60, function () {
                return Ticket::where('ticket_status_id', 1)->where('department_id',Auth::user()->department_id )->count();
            });
        }
else
        {

        $this->newCount = Cache::remember('tickets_table', 60, function () {
            return Ticket::where('ticket_status_id', 1)->count();
        });
    }
    }
}
