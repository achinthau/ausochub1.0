<?php

namespace App\Http\Livewire\Settings\Tickets\ServiceCenter;

use Livewire\Component;
use App\Models\CxTicketServCenter;


class Create extends Component
{
    public CxTicketServCenter $servicecenter;

    public $createTicketServiceCenterModal = false;

    protected $rules  = [

        'servicecenter.name' => 'required',

    ];

    public function render()
    {
        return view('livewire.settings.tickets.service-center.create');
    }

    public function mount()
    {
        $this->servicecenter = new CxTicketServCenter();
    }

    
    public function updatedCreateservicecenterModal($value)
    {
        $this->resetForm();
    }

    public function save()
    {


        $this->validate();

        $data = [
            ['name' => 'name', 'contents' => $this->servicecenter->name,],
        ];

        $this->servicecenter->save();
        $this->createTicketServiceCenterModal = false;
         $this->emitTo('tables.settings.service-center-table', 'refreshLivewireDatatable');
        $this->resetForm();


        
    }

    public function resetForm()
    {
        $this->servicecenter = new CxTicketServCenter();
        $this->resetErrorBag();
        $this->resetValidation();
    }
}
