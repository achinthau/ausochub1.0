<?php

namespace App\Http\Livewire\CxTickets\Counts;

use Livewire\Component;
use App\Models\CxTicket;

class Canceled extends Component
{
    public $canceledCount = 0 ;

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
        // $this->canceledCount = CxTicket::where('status','Canceled')->count();
        

$this->canceledCount = CxTicket::where('status', 'Canceled')
    ->where('company', $this->companyName)
    ->count();

    }

    public function render()
    {
        return view('livewire.cx-tickets.counts.canceled');
    }
}
