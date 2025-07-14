<?php

namespace App\Http\Livewire\CxTickets\Counts;

use Livewire\Component;
use App\Models\CxTicket;

class ReOpened extends Component
{

    public $reOpenCount = 0 ;

    public $readyToLoad = false;

     public function loaded()
    {
        $this->readyToLoad = true;
        $this->refreshComponent();
    }

    public function refreshComponent()
    {
        $this->reOpenCount = CxTicket::where('status','ReOpened')->count();
    }

    public function render()
    {
        return view('livewire.cx-tickets.counts.re-opened');
    }
}
