<?php

namespace App\Http\Livewire\Tickets;

use App\Models\Ticket;
use App\Models\TicketStatus;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;
    public $keyword = "";
    public $from = null;
    public $to = null;
    public $assignee = null;
    public $statues = [];

    protected $listeners = ['refreshList' => 'refreshList'];
    

    public function render()
    {
        return view(
            'livewire.tickets.index',
            [
                'tickets' => Ticket::general()->today()->search($this->keyword)->byStatus($this->statues)->orderBy('id','DESC')->paginate(10),
                'users' => User::all(),
                'ticketStatues' => TicketStatus::all(),
            ]
        );
    }

    public function refreshList()
    {
    }
}
