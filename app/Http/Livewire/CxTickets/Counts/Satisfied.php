<?php

namespace App\Http\Livewire\CxTickets\Counts;

use Livewire\Component;
use App\Models\CxTicket;
use Illuminate\Support\Facades\DB;

class Satisfied extends Component
{

    public $ratedCount = 0 ;

    public $readyToLoad = false;
    public $companyNames ;

     public function loaded()
    {
        $this->readyToLoad = true;
        $companyIds = array_filter(array_map('intval', explode(',', auth()->user()->tenant_context)));

        $this->companyNames = \DB::table('companies')
            ->whereIn('id', $companyIds)
            ->pluck('name') 
            ->toArray();
        $this->refreshComponent();
    }

    public function refreshComponent()
    {
        $this->ratedCount = CxTicket::where('status', 'Rated')->where('satisfaction_rate','>',3)->whereIn('company', $this->companyNames)->count();
        // $this->ratedCount = CxTicket::whereNotNull('satisfaction_rate')
        //                     ->where('satisfaction_rate', '<', 3)
        //                     ->count();

    }

    public function render()
    {
        return view('livewire.cx-tickets.counts.satisfied');
    }
}
