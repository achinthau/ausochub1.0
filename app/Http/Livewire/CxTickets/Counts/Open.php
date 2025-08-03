<?php

namespace App\Http\Livewire\CxTickets\Counts;

use Livewire\Component;
use App\Models\CxTicket;
use Illuminate\Support\Facades\DB;

class Open extends Component
{
    public $openCount = 0 ;

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
        $this->openCount = CxTicket::where('status','Open')->where('company', $this->companyName)->count();
    }

    public function render()
    {
        return view('livewire.cx-tickets.counts.open');
    }
}
