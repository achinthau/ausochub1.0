<?php

namespace App\Http\Livewire\TicketItems;

use App\Models\CrmDepartment;
use Livewire\Component;

class DepartmentPanel extends Component
{
    public $departments;
    public $selectedDepartment;

    public function mount()
    {
        $this->departments=CrmDepartment::all();
    }

    public function updatedSelectedDepartment($value)
    {
        $this->emit('departmentUpdated', $value); 
    }

    public function render()
    {
        return view('livewire.ticket-items.department-panel');
    }
}
