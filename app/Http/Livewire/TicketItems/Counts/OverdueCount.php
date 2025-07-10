<?php

namespace App\Http\Livewire\TicketItems\Counts;

use App\Models\Ticket;
use App\Models\TicketItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class OverdueCount extends Component
{
    protected $listeners = ['departmentUpdated' => 'setDepartment'];
    public $selectedDepartment = 0;
    public $overdueCount=0;

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
        return view('livewire.ticket-items.counts.overdue-count');
    }

    


    public function mount()
    {
//         Ticket::whereNotIn('ticket_status_id', [3, 4])->where('due_at', '<', Carbon::now())->update(['ticket_status_id' => '3']);

//         if(Auth::user()->department_id)
//         {
//             $this->overdueCount = Cache::remember('tickets_table', 60, function () {
//                 return Ticket::where('ticket_status_id', 3)->where('department_id',Auth::user()->department_id )->count() + (config('auso.ticket_sla_enabled') ?  Ticket::whereNotIn('ticket_status_id', [3, 4])->where('due_at', '<', Carbon::now())->count() : 0);

//             });
//         }
// else
//         {

//             if($this->selectedDepartment == 0)
//             {
//                 $this->overdueCount = Cache::remember('tickets_table', 60, function () {
//                     return Ticket::where('ticket_status_id', 3)->count();
//                 });
//             }
//             else
//             {
//                 $this->overdueCount = Cache::remember('tickets_table', 60, function () {
//                     return Ticket::where('ticket_status_id', 3)->where('department_id', $this->selectedDepartment)->count();
//                 });
//             }
//     }
    }

    public function refreshComponent()
    {
        // Cache::forget('tickets_table');

        // $this->overdueCount = Cache::remember('tickets_table', 60, function () {
        //     return Ticket::where('ticket_status_id', 3)->count() + (config('auso.ticket_sla_enabled') ?  Ticket::whereNotIn('ticket_status_id', [3, 4])->where('due_at', '<', Carbon::now())->count() : 0);
        // });

        if(Auth::user()->department_id)
        {
            $this->overdueCount = Cache::remember('tickets_table_overdue'.Auth::user()->department_id, 6, function () {
                return Ticket::where('ticket_status_id', 3)->where('department_id',Auth::user()->department_id )->count() + (config('auso.ticket_sla_enabled') ?  Ticket::whereNotIn('ticket_status_id', [3, 4])->where('due_at', '<', Carbon::now())->count() : 0);

            });
        }
else
        {

            if($this->selectedDepartment == 0)
            {
                $this->overdueCount = Cache::remember('tickets_table_overdue', 6, function () {
                    return Ticket::where('ticket_status_id', 3)->count();
                });
            }
            else
            {
                $this->overdueCount = Cache::remember('tickets_table_overdue', 6, function () {
                    return Ticket::where('ticket_status_id', 3)->where('department_id', $this->selectedDepartment)->count();
                });
            }
    }
    }
}
