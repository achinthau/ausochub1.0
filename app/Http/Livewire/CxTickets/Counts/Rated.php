<?php

namespace App\Http\Livewire\CxTickets\Counts;

use Livewire\Component;
use App\Models\CxTicket;

class Rated extends Component
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
       $this->ratedCount = CxTicket::where('status', 'Rated')->count();
    }

    public function render()
    {
        return view('livewire.cx-tickets.counts.rated');
    }
}
