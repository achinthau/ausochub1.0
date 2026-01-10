<?php

namespace App\Http\Livewire\CxTickets\Survey;

use App\Models\CxTicket;
use Livewire\Component;

class ViewPanel extends Component
{
    public $CxTicketViewingModal = false;
    public $ticket;
    protected $listeners = ['showCxTicketViewingModal' => 'showCxTicketViewingModal', 'cxTicketSurveyUpdated' => 'closeModal'];


    public function closeModal()
    {
        $this->CxTicketViewingModal = false;
    }
    public function showCxTicketViewingModal($id)
    {
        $this->CxTicketViewingModal = true;
        $ticket = CxTicket::find($id);

        
            if ($ticket) {
                $this->ticket = $ticket;
            }
    }
    

    public function render()
    {
        return view('livewire.cx-tickets.survey.view-panel');
    }
}
