<?php

namespace App\Http\Livewire\Settings\Tickets\Departments;

use Livewire\Component;
use App\Models\CrmDepartment;

class Create extends Component
{
    // use Actions;

    public CrmDepartment $crmDepartment;

    public $createTicketDepartmentModal = false;

    protected $rules  = [

        'crmDepartment.name' => 'required',

    ];


    public function render()
    {
        return view('livewire.settings.tickets.departments.create');
    }

    public function mount()
    {
        $this->crmDepartment = new CrmDepartment();
    }

    
    public function updatedCreatecrmDepartmentModal($value)
    {
        $this->resetForm();
    }

    public function save()
    {


        $this->validate();

        $data = [
            ['name' => 'name', 'contents' => $this->crmDepartment->name,],
        ];

        $this->crmDepartment->save();
        $this->createTicketDepartmentModal = false;
        $this->emitTo('tables.settings.crm-department-table', 'refreshLivewireDatatable');
        $this->resetForm();


        
    }

    public function resetForm()
    {
        $this->crmDepartment = new crmDepartment();
        $this->resetErrorBag();
        $this->resetValidation();
    }
}
