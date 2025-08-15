<?php

namespace App\Http\Livewire\CxTickets\Survey;

use Livewire\Component;
use App\Models\CxTicket;
use Illuminate\Support\Facades\Auth;

class ReopenPanel extends Component
{
    public $ticket_id;
    public $isReOpen = false;
    public $cxTicketReOpenModal;
    public $comment = '';


     protected $listeners = ['showReOpenPanel' => 'showReOpenModal'];

     protected $rules = [
    'comment' => 'required|string|min:5',
];


    public function render()
    {
        return view('livewire.cx-tickets.survey.reopen-panel');
    }

    public function showReOpenModal($id, $value)
    {
        $this->isReOpen= $value;
        $this->ticket_id = $id;
        $this->cxTicketReOpenModal = true;
    }

    public function reOpenTicket()
    {
        $this->validate();

        $ticket = CxTicket::find($this->ticket_id);
        if($ticket)
        {
            if($this->isReOpen)
            {
                $ticket->status = 'ReOpened';
            $ticket->reopened_reasons = $this->comment;
            $ticket->reopened_by = Auth::user()->name;
            }
            elseif(!$this->isReOpen)
            {
                $ticket->status = 'Skip';
            $ticket->skipped_reasons = $this->comment;
            $ticket->skipped_by = Auth::user()->name;
            }

            $ticket->save();

            // $newTicket = $ticket->replicate();
            // $newTicket->status = 'Open';
            // $newTicket->created_at = now();
            // $newTicket->updated_at = now();
            // $newTicket->save();
        }
         $this->emit('cxTicketSurveyUpdated');
         $this->cxTicketReOpenModal = false;
    $this->reset(['comment', 'ticket_id']);
    }
}
