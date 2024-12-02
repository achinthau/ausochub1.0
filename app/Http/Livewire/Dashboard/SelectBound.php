<?php

namespace App\Http\Livewire\Dashboard;

use Livewire\Component;

class SelectBound extends Component
{
    public $boundType = "In Bound";
    public $isOutbound = false;
    public $isAcw = false;
    protected $listeners = ['changeBound' => 'changeTheBound', 'setAcw' => 'setAcw'];




    public function refreshComponent()
    {
        if($this->isOutbound == true)
        {
            $this->boundType = "Out Bound";
        }
        else
        {
            $this->boundType = "In Bound";
        }
    }

    public function setAcw()
    {
        if($this->isAcw == false)
        {
            $this->isAcw = true;
        }
        else
        {
            $this->isAcw = false;
        }
    }

    public function render()
    {
        return view('livewire.dashboard.select-bound');
    }
}
