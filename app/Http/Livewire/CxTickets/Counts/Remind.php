<?php

namespace App\Http\Livewire\CxTickets\Counts;

use Livewire\Component;
use App\Models\CxTicket;


class Remind extends Component
{
    public $remindCount = 0 ;

    public $readyToLoad = false;
    public $companyNames ;

     public function loaded()
    {
        $this->readyToLoad = true;
        $this->companyNames = array_filter(array_map('trim', explode(',', auth()->user()->tenant_context)));
        $this->refreshComponent();
    }

    public function refreshComponent()
    {
        $this->remindCount = CxTicket::where('status','Remind')->whereIn('company', $this->companyNames)->count();
    }

    public function render()
    {
        return view('livewire.cx-tickets.counts.remind');
    }
}
