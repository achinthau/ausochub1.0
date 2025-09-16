<?php

namespace App\Http\Livewire\Leads\Partials;

use Livewire\Component;

class SkipModal extends Component
{
    public $SkipContactModal = false;
    public $reminder;
    public $comment='';

    protected $listeners = ['openSkipContactModal' => 'openModal'];

    public function openModal($id)
    {
        $this->SkipContactModal = true;
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
        return view('livewire.leads.partials.skip-modal');
    }
}
