<?php

namespace App\Http\Livewire\Settings\Tickets\Departments;

use Livewire\Component;
use App\Models\CrmDepartment;


class Show extends Component
{
    // use Actions;

    public CrmDepartment $crmDepartment;
    public $updateTicketDepartmentModal = false;

    protected $rules  = [

        'crmDepartment.name' => 'required',

    ];


    protected $listeners = ['showupdateTicketDepartmentModal' => 'showupdateTicketDepartmentModal'];


    public function render()
    {
        return view('livewire.settings.tickets.departments.show');
    }

    
    public function showupdateTicketDepartmentModal($id)
    {
        $this->updateTicketDepartmentModal = true;
        $this->crmDepartment = CrmDepartment::find($id);
    }

    public function updatedupdateTicketDepartmentModal($value)
    {
        if (!$value) {
            $this->resetForm();
        }
    }

    public function save()
{
    $this->validate();

    $this->crmDepartment->save();

    $this->emitTo('tables.settings.crm-department-table', 'refreshLivewireDatatable');

    $this->updateTicketDepartmentModal = false;
    $this->resetForm();

}


    public function resetForm()
    {
        $this->crmDepartment = new CrmDepartment();
        $this->resetErrorBag();
        $this->resetValidation();
    }
}
