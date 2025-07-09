<?php

namespace App\Http\Livewire\CxTickets\Counts;

use Livewire\Component;
use App\Models\CxTicket;

class Closed extends Component
{
    public $closedCount = 0 ;

    public $readyToLoad = false;

     public function loaded()
    {
        $this->readyToLoad = true;
        $this->refreshComponent();
    }

    public function refreshComponent()
    {
        $this->closedCount = CxTicket::where('status','Closed')->count();
    }

    public function render()
    {
        return view('livewire.cx-tickets.counts.closed');
    }
}
