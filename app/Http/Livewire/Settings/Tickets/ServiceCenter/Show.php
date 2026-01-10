<?php

namespace App\Http\Livewire\Settings\Tickets\ServiceCenter;

use Livewire\Component;
use App\Models\CxTicketServCenter;


class Show extends Component
{
     public CxTicketServCenter $servicecenter;
    public $updateTicketServiceCenterModal = false;

    protected $rules  = [

        'servicecenter.name' => 'required',

    ];


    protected $listeners = ['showupdateTicketServiceCenterModal' => 'showupdateTicketServiceCenterModal'];



    public function render()
    {
        return view('livewire.settings.tickets.service-center.show');
    }

    
    public function showupdateTicketServiceCenterModal($id)
    {
        $this->updateTicketServiceCenterModal = true;
        $this->servicecenter = CxTicketServCenter::find($id);
    }

    public function updatedupdateTicketServiceCenterModal($value)
    {
        if (!$value) {
            $this->resetForm();
        }
    }

    public function save()
{
    $this->validate();

    $this->servicecenter->save();

    $this->emitTo('tables.settings.service-center-table', 'refreshLivewireDatatable');

    $this->updateTicketServiceCenterModal = false;
    $this->resetForm();

}


    public function resetForm()
    {
        $this->servicecenter = new CxTicketServCenter();
        $this->resetErrorBag();
        $this->resetValidation();
    }
}
