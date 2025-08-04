<?php

namespace App\Http\Livewire\CxTickets\Counts;

use Livewire\Component;
use App\Models\CxTicket;
use Illuminate\Support\Facades\DB;

class Rated extends Component
{
    public $ratedCount = 0 ;

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
       $this->ratedCount = CxTicket::where('status', 'Rated')->whereIn('company', $this->companyNames)->count();
    }

    public function render()
    {
        return view('livewire.cx-tickets.counts.rated');
    }
}
