<?php

namespace App\Http\Livewire\Orders;

use App\Models\Outlet;
use App\Models\Ticket;
use App\Models\TicketStatus;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
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
    public $outlet;

    protected $listeners = ['refreshList' => 'refreshList'];
    

    public function mount()
    {
        $this->from = Carbon::now()->startOfDay();
        $this->to = Carbon::now()->endOfDay();
        $this->outlet = Gate::allows('is-has-outlet') ? Auth::user()->outlet_id : null;
    }

    public function render()
    {
        return view(
            'livewire.orders.index',
            [
                'tickets' => Ticket::relavant()->orders()->today($this->from,$this->to)->search($this->keyword)->byStatus($this->statues)->byOutlet($this->outlet)->orderBy('id','DESC')->paginate(10),
                'users' => User::all(),
                'ticketStatues' => TicketStatus::all(),
                'outlets' => Outlet::all(),
            ]
        );
    }

    public function refreshList()
    {
    }

   
}
