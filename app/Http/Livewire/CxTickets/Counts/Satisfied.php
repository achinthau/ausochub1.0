<?php

namespace App\Http\Livewire\CxTickets\Counts;

use Livewire\Component;
use App\Models\CxTicket;
use Illuminate\Support\Facades\DB;

class Satisfied extends Component
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
        $this->ratedCount = CxTicket::where('status', 'Rated')->where('satisfaction_rate','>',3)->where('company', $this->companyName)->count();
        // $this->ratedCount = CxTicket::whereNotNull('satisfaction_rate')
        //                     ->where('satisfaction_rate', '<', 3)
        //                     ->count();

    }

    public function render()
    {
        return view('livewire.cx-tickets.counts.satisfied');
    }
}
