<?php

namespace App\Http\Livewire\Reminders\Partials;

use App\Models\CallbackCustomer;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CloseModal extends Component
{
    public $ReminderClosingModal = false;
    public $reminder;
    public $comment='';

    protected $listeners = ['closeReminder' => 'openModal'];

    public function openModal($id)
    {
        $this->reminder = CallbackCustomer::find($id);
        $this->ReminderClosingModal = true;
    }

    protected $rules = [
        'comment' => 'required|string|min:5',
    ];
    public function closeModal()
    {
        // dd($this->reminder->closing_reason);
        $this->reminder->closing_reason = $this->comment;
        $this->reminder->closed_by = Auth::user()->name;
        $this->reminder->save();
        $this->ReminderClosingModal = false;
        $this->emit('reminderTimeUpdated');
    }
    public function render()
    {
        return view('livewire.reminders.partials.close-modal');
    }
}
