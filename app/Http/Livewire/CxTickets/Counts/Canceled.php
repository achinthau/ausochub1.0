<?php

namespace App\Http\Livewire\CxTickets\Counts;

use Livewire\Component;
use App\Models\CxTicket;

class Canceled extends Component
{
    public $canceledCount = 0 ;

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
        // $this->canceledCount = CxTicket::where('status','Canceled')->count();
        

$this->canceledCount = CxTicket::where('status', 'Canceled')
    ->whereIn('company', $this->companyNames)
    ->count();

    }

    public function render()
    {
        return view('livewire.cx-tickets.counts.canceled');
    }
}
