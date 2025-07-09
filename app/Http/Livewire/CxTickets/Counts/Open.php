<?php

namespace App\Http\Livewire\CxTickets\Counts;

use Livewire\Component;
use App\Models\CxTicket;

class Open extends Component
{
    public $openCount = 0 ;

    public $readyToLoad = false;

     public function loaded()
    {
        $this->readyToLoad = true;
        $this->refreshComponent();
    }

    public function refreshComponent()
    {
        $this->openCount = CxTicket::where('status','Open')->count();
    }

    public function render()
    {
        return view('livewire.cx-tickets.counts.open');
    }
}
