<?php

namespace App\Http\Livewire\TicketItems\Tables\Cells;

use Livewire\Component;

class InputSearch extends Component
{
    // public $statuses;

    // public function mount($statuses)
    // {
    //     $this->statuses=$statuses;
    // }

    public function render()
    {
        return view('livewire.ticket-items.tables.cells.input-search');
    }
}
