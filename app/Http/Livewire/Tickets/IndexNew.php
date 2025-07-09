<?php

namespace App\Http\Livewire\Tickets;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class IndexNew extends Component
{
    public $userid;

    public function mount()
    {
        $this->userid= Auth::user()->user_type_id;
    }

    public function render()
    {
        return view('livewire.tickets.index-new');
    }
}
