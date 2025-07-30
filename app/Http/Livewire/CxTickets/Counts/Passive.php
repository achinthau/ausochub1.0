<?php

namespace App\Http\Livewire\CxTickets\Counts;

use Livewire\Component;
use App\Models\CxTicket;

class Passive extends Component
{
    public $ratedCount = 0 ;

    public $readyToLoad = false;

     public function loaded()
    {
        $this->readyToLoad = true;
        $this->refreshComponent();
    }

    public function refreshComponent()
    {
        $this->ratedCount = CxTicket::where('status', 'Rated')->where('satisfaction_rate',8)->count();
    }

    public function render()
    {
        return view('livewire.cx-tickets.counts.passive');
    }
}
