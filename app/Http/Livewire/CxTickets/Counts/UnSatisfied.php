<?php

namespace App\Http\Livewire\CxTickets\Counts;

use Livewire\Component;
use App\Models\CxTicket;

class UnSatisfied extends Component
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
        $this->ratedCount = CxTicket::where('status', 'Rated')->where('satisfaction_rate','<',3)->count();
        // $this->ratedCount = CxTicket::whereNotNull('satisfaction_rate')
        //                     ->where('satisfaction_rate', '<', 3)
        //                     ->count();

    }

    public function render()
    {
        return view('livewire.cx-tickets.counts.un-satisfied');
    }
}
