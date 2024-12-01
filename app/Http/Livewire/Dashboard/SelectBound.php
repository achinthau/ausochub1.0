<?php

namespace App\Http\Livewire\Dashboard;

use Livewire\Component;

class SelectBound extends Component
{
    public $boundType = "In Bound";
    public $isOutbound = false;
    protected $listeners = ['changeBound' => 'changeTheBound'];


    public function changeTheBound()
    {
        $this->isOutbound = true;
    }

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

    public function render()
    {
        return view('livewire.dashboard.select-bound');
    }
}
