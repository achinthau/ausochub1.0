<?php

namespace App\Http\Livewire\CxTickets\Survey;

use App\Models\CallbackCustomer;
use Livewire\Component;
use App\Models\CxTicket;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ReopenPanel extends Component
{
    public $ticket_id;
    public $isReOpen = '';
    public $cxTicketReOpenModal = false;
    public $comment = '';

    public $callBack = false;
    public $callbackDate;
    public $callbackTime;
    public $callbackComment;

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
        $this->ticket_id = $id;
        $this->isReOpen = $value;        // value: 'reopen', 'skip', 'remind'
        $this->cxTicketReOpenModal = true;
        $this->callBack = $value === 'remind';
    }

    public function reOpenTicket()
    {
        $this->validate();

        $ticket = CxTicket::find($this->ticket_id);
        if ($ticket) {
            if ($this->isReOpen === 'reopen') {
                $ticket->status = 'ReOpened';
                $ticket->reopened_reasons = $this->comment;
                $ticket->reopened_by = Auth::user()->name;
            }
            elseif ($this->isReOpen === 'skip') {
                $ticket->status = 'Skip';
                $ticket->skipped_reasons = $this->comment;
                $ticket->skipped_by = Auth::user()->name;
            }
            $ticket->save();
        }

        $this->emit('cxTicketSurveyUpdated');
        $this->cxTicketReOpenModal = false;
        $this->reset(['comment', 'ticket_id', 'isReOpen', 'callBack']);
    }

    public function saveCallback()
    {
        $ticket = CxTicket::find($this->ticket_id);
        if ($ticket){
            $ticket->status = 'Remind';
        }
        $ticket->save();

        $this->validate([
            'callbackDate' => 'required|date',
            'callbackTime' => 'required',
            'callbackComment' => 'nullable|string',
        ]);

        CallbackCustomer::create([
            'agent_id' => auth()->id(),
            'cx_ticket_id' => $this->ticket_id,
            'contact_number' => $ticket->customer_contact_01,
            'src' => 'survey',
            'callback_at' => Carbon::parse("{$this->callbackDate} {$this->callbackTime}"),
            'comment' => $this->callbackComment,
        ]);

        session()->flash('messagedialog', 'Callback saved successfully.');

        $this->cxTicketReOpenModal = false;
        $this->emit('cxTicketSurveyUpdated');
        $this->reset(['callBack', 'callbackDate', 'callbackTime', 'callbackComment', 'comment', 'isReOpen', 'ticket_id']);
    }
}
