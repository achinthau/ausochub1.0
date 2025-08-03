<?php

namespace App\Http\Livewire\CxTickets\Counts;

use Livewire\Component;
use App\Models\CxTicket;
use Illuminate\Support\Facades\DB;

class Rated extends Component
{
    public $ratedCount = 0 ;

    public $readyToLoad = false;
        public $companyName ='';

     public function loaded()
    {
        $this->readyToLoad = true;
         $this->companyName = \DB::table('companies')
    ->where('id', auth()->user()->tenant_context)
    ->value('name');
        $this->refreshComponent();
    }

    public function refreshComponent()
    {
       $this->ratedCount = CxTicket::where('status', 'Rated')->where('company', $this->companyName)->count();
    }

    public function render()
    {
        return view('livewire.cx-tickets.counts.rated');
    }
}
