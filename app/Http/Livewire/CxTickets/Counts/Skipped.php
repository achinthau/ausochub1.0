<?php

namespace App\Http\Livewire\CxTickets\Counts;
use App\Models\CxTicket;


use Livewire\Component;

class Skipped extends Component
{
    public $skipCount = 0 ;

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
        $this->skipCount = CxTicket::where('status','Skip')->whereIn('company', $this->companyNames)->count();
    }
    public function render()
    {
        return view('livewire.cx-tickets.counts.skipped');
    }
}
