<?php

namespace App\Http\Livewire\TicketItems\Counts;

use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class ClosedCount extends Component
{
    protected $listeners = ['departmentUpdated' => 'setDepartment'];
    public $selectedDepartment = 0;
    public $closedCount=0;
    public $readyToLoad = false;

    public function loaded()
    {
        $this->readyToLoad = true;
        $this->refreshComponent();
    }

    public function setDepartment($deptId)
    {
        $this->selectedDepartment = $deptId;
        // dd($this->selectedDepartment);
    }

    public function render()
    {
        return view('livewire.ticket-items.counts.closed-count');
    }

    


    public function mount()
    {
//         if(Auth::user()->department_id)
//         {
//             $this->closedCount = Cache::remember('tickets_table', 60, function () {
//                 return Ticket::where('ticket_status_id', 4)->where('department_id',Auth::user()->department_id )->count();
//             });
//         }
// else
//         {

//             if($this->selectedDepartment == 0)
//             {
//                 $this->closedCount = Cache::remember('tickets_table', 60, function () {
//                     return Ticket::where('ticket_status_id', 4)->count();
//                 });
//             }
//             else
//             {
//                 $this->closedCount = Cache::remember('tickets_table', 60, function () {
//                     return Ticket::where('ticket_status_id', 4)->where('department_id', $this->selectedDepartment)->count();
//                 });
//             }
//     }
    }

    public function refreshComponent()
    {
            // Cache::forget('tickets_table');

            if(Auth::user()->department_id)
        {
            $this->closedCount = Cache::remember('tickets_table_closed'.Auth::user()->department_id, 6, function () {
                return Ticket::where('ticket_status_id', 4)->where('department_id',Auth::user()->department_id )->count();
            });
        }
else
        {
            if($this->selectedDepartment == 0)
            {
                $this->closedCount = Cache::remember('tickets_table_closed', 6, function () {
                    return Ticket::where('ticket_status_id', 4)->count();
                });
            }
            else
            {
                $this->closedCount = Cache::remember('tickets_table_closed', 6, function () {
                    return Ticket::where('ticket_status_id', 4)->where('department_id', $this->selectedDepartment)->count();
                });
            }
        }
        
    }
}
