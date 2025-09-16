<?php

namespace App\Http\Livewire\Leads\Partials;

use App\Models\FeedContactValid;
use Livewire\Component;

class SubmitCallStatus extends Component
{

    public $CallStatusModal = false;
    public $feed;

    protected $listeners = ['openCallStatusModal' => 'openModal'];

    public function openModal($id)
    {
        $this->CallStatusModal = true;
        $this->feed = FeedContactValid::find($id);
        // dd($this->feed);
    }

    protected $rules = [
        'comment' => 'required|string|min:5',
    ];
    public function skipContact()
    {
    //     // dd($this->reminder->closing_reason);
    //     $this->reminder->closing_reason = $this->comment;
    //     $this->reminder->closed_by = Auth::user()->name;
    //     $this->reminder->save();
    //     $this->SkipContactModal = false;
    //     $this->emit('reminderTimeUpdated');
    }
    public function render()
    {
        return view('livewire.leads.partials.submit-call-status');
    }
}
