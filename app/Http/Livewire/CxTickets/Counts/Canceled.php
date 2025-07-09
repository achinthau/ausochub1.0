<?php

namespace App\Http\Livewire\CxTickets\Counts;

use Livewire\Component;
use App\Models\CxTicket;

class Canceled extends Component
{
    public $canceledCount = 0 ;

    public $readyToLoad = false;

     public function loaded()
    {
        $this->readyToLoad = true;
        $this->refreshComponent();
    }

    public function refreshComponent()
    {
        $this->canceledCount = CxTicket::where('status','Canceled')->count();
    }

    public function render()
    {
        return view('livewire.cx-tickets.counts.canceled');
    }
}
