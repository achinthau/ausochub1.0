<?php

namespace App\Http\Livewire\TicketItems;

use Livewire\Component;

class UserMenuPanel extends Component
{
    public $selectedItem;

    public function render()
    {
        return view('livewire.ticket-items.user-menu-panel');
    }

    public function updatedSelectedItem($value)
    {
        $this->emit('ItemUpdated', $value); 
    }
}
