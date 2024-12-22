<?php

namespace App\Http\Livewire\Reports;

use Livewire\Component;

class AbandonedCall extends Component
{
    public $readyToLoad = false;


    public function render()
    {
        return view('livewire.reports.abandoned-call');
    }


    public function loadPosts()
    {
        $this->readyToLoad = true;
    }
}
